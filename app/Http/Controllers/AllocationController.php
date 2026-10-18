<?php

namespace App\Http\Controllers;

use App\Models\Allocation;
use App\Models\Booking;
use App\Models\Room;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AllocationController extends Controller
{
    public function index(Request $request)
    {
        $query = Allocation::query()->with(['student', 'room.hostel', 'allocatedBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', fn ($q) => $q->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('admission_number', 'like', "%{$search}%"))
                ->orWhereHas('room', fn ($q) => $q->where('room_number', 'like', "%{$search}%"));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $allocations = $query->latest()->paginate(15)->withQueryString();

        return view('allocations.index', compact('allocations'));
    }

    public function create()
    {
        $students = Student::where('status', 'active')->get();
        $rooms = Room::where('status', '!=', 'maintenance')->with('hostel')->get();

        return view('allocations.create', compact('students', 'rooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'room_id' => ['required', 'exists:rooms,id'],
            'booking_id' => ['nullable', 'exists:bookings,id'],
            'allocation_date' => ['required', 'date'],
            'check_in_date' => ['required', 'date', 'after_or_equal:today'],
            'expected_check_out_date' => ['nullable', 'date', 'after:check_in_date'],
        ]);

        $student = Student::findOrFail($validated['student_id']);

        if ($student->status !== 'active') {
            return back()->withErrors(['student_id' => 'This student is not eligible for accommodation.'])->withInput();
        }

        return DB::transaction(function () use ($validated, $student) {
            $room = Room::query()->with('hostel')->lockForUpdate()->findOrFail($validated['room_id']);

            if (! $room->hostel || $room->hostel->status !== 'active') {
                throw ValidationException::withMessages(['room_id' => 'The selected room belongs to an inactive hostel.']);
            }

            if (! $room->hostel->acceptsGender($student->gender)) {
                throw ValidationException::withMessages(['room_id' => 'This hostel is not available for the selected student.']);
            }

            if ($room->status === 'maintenance') {
                throw ValidationException::withMessages(['room_id' => 'The selected room is under maintenance.']);
            }

            $booking = isset($validated['booking_id'])
                ? Booking::query()
                    ->whereKey($validated['booking_id'])
                    ->where('student_id', $student->id)
                    ->whereIn('status', ['pending', 'approved'])
                    ->firstOrFail()
                : Booking::query()
                    ->where('student_id', $student->id)
                    ->whereIn('status', ['pending', 'approved'])
                    ->where(function ($query) use ($room) {
                        $query->whereNull('room_id')->orWhere('room_id', $room->id);
                    })
                    ->whereDate('requested_check_in_date', '<', $validated['expected_check_out_date'] ?? '9999-12-31')
                    ->where(function ($query) use ($validated) {
                        $query->whereNull('requested_check_out_date')
                            ->orWhereDate('requested_check_out_date', '>', $validated['check_in_date']);
                    })
                    ->oldest()
                    ->first();

            if ($booking && $booking->room_id && $booking->room_id !== $room->id) {
                throw ValidationException::withMessages(['room_id' => 'Allocate the room reserved by this booking.']);
            }

            $conflictQuery = Allocation::query()
                ->where('student_id', $student->id)
                ->where('status', 'active')
                ->whereDate('check_in_date', '<', $validated['expected_check_out_date'] ?? '9999-12-31')
                ->where(function ($query) use ($validated) {
                    $query->whereNull('expected_check_out_date')
                        ->orWhereDate('expected_check_out_date', '>', $validated['check_in_date']);
                });

            if ($booking) {
                $conflictQuery->where('booking_id', '!=', $booking->id);
            }

            if ($conflictQuery->exists()) {
                throw ValidationException::withMessages(['student_id' => 'This student already has a conflicting active allocation.']);
            }

            if ($room->availableSpacesForPeriod(
                $validated['check_in_date'],
                $validated['expected_check_out_date'] ?? null,
                $booking?->id
            ) < 1) {
                throw ValidationException::withMessages(['room_id' => 'This room has reached its capacity for the selected dates.']);
            }

            $allocation = Allocation::create([
                'student_id' => $validated['student_id'],
                'room_id' => $validated['room_id'],
                'booking_id' => $booking?->id,
                'allocated_by' => auth()->id(),
                'allocation_date' => $validated['allocation_date'],
                'check_in_date' => $validated['check_in_date'],
                'expected_check_out_date' => $validated['expected_check_out_date'] ?? null,
                'status' => 'active',
            ]);

            if ($booking) {
                $booking->update([
                    'room_id' => $room->id,
                    'preferred_hostel_id' => $room->hostel_id,
                    'status' => 'approved',
                ]);
            }

            $room->status = $room->availableSpaces() <= 0 ? 'full' : 'available';
            $room->save();

            return redirect()->route('allocations.index')->with('success', 'Room allocation created successfully.');
        });
    }

    public function show(Allocation $allocation)
    {
        $allocation->load(['student', 'room.hostel', 'booking', 'allocatedBy', 'payments']);

        return view('allocations.show', compact('allocation'));
    }

    public function destroy(Allocation $allocation)
    {
        $allocation->update([
            'status' => 'completed',
            'actual_check_out_date' => now()->toDateString(),
        ]);

        if ($allocation->room) {
            $allocation->room->status = $allocation->room->availableSpaces() > 0 ? 'available' : 'full';
            $allocation->room->save();
        }

        return redirect()->route('allocations.index')->with('success', 'Student check-out recorded successfully.');
    }
}

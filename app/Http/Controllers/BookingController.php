<?php

namespace App\Http\Controllers;

use App\Models\Allocation;
use App\Models\Booking;
use App\Models\Hostel;
use App\Models\Room;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::query()->with(['student', 'room.hostel', 'preferredHostel', 'processor']);

        if ($request->user()->hasRole('student')) {
            $query->where('student_id', $this->studentForUser($request->user())->id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('admission_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->latest()->paginate(15)->withQueryString();

        return view('bookings.index', compact('bookings'));
    }

    public function create()
    {
        $user = auth()->user();
        $studentProfile = $user->hasRole('student') ? $this->studentForUser($user) : null;
        $students = $studentProfile ? collect() : Student::where('status', 'active')->get();
        $hostels = Hostel::where('status', 'active')
            ->whereNotNull('booking_period_name')
            ->whereNotNull('booking_period_start')
            ->whereNotNull('booking_period_end')
            ->whereDate('booking_period_end', '>=', now()->toDateString())
            ->when($studentProfile, fn ($query) => $query->whereIn('gender_category', [$studentProfile->gender, 'mixed']))
            ->get();

        return view('bookings.create', compact('students', 'hostels', 'studentProfile'));
    }

    public function availableRooms(Request $request): JsonResponse
    {
        $user = $request->user();
        $isStudent = $user->hasRole('student');
        $validated = $request->validate([
            'student_id' => $isStudent
                ? ['prohibited']
                : ['required', Rule::exists('students', 'id')->where('status', 'active')],
            'preferred_hostel_id' => ['required', Rule::exists('hostels', 'id')->where('status', 'active')],
        ]);

        $student = $isStudent
            ? $this->studentForUser($user)
            : Student::findOrFail($validated['student_id']);
        $hostel = Hostel::query()->findOrFail($validated['preferred_hostel_id']);

        $this->assertBookablePeriod($hostel, $student);

        $rooms = Room::query()
            ->with('hostel')
            ->where('status', '!=', 'maintenance')
            ->where('hostel_id', $hostel->id)
            ->get()
            ->map(function (Room $room) use ($hostel): ?array {
                $availableSpaces = $room->availableSpacesForPeriod(
                    $hostel->booking_period_start->toDateString(),
                    $hostel->booking_period_end->toDateString()
                );

                if ($availableSpaces < 1) {
                    return null;
                }

                return [
                    'id' => $room->id,
                    'hostel' => $room->hostel->hostel_name,
                    'room_number' => $room->room_number,
                    'available_spaces' => $availableSpaces,
                    'period_fee' => (float) $room->period_fee,
                    'period_name' => $hostel->booking_period_name,
                    'period_start' => $hostel->booking_period_start->toDateString(),
                    'period_end' => $hostel->booking_period_end->toDateString(),
                ];
            })
            ->filter()
            ->values();

        return response()->json($rooms);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $isStudent = $user->hasRole('student');

        $validated = $request->validate([
            'student_id' => $isStudent
                ? ['prohibited']
                : ['required', Rule::exists('students', 'id')->where('status', 'active')],
            'room_id' => ['required', 'exists:rooms,id'],
            'preferred_hostel_id' => ['required', Rule::exists('hostels', 'id')->where('status', 'active')],
            'requested_check_in_date' => ['prohibited'],
            'requested_check_out_date' => ['prohibited'],
            'remarks' => ['nullable', 'string'],
        ]);

        $student = $isStudent
            ? $this->studentForUser($user)
            : Student::findOrFail($validated['student_id']);

        DB::transaction(function () use ($validated, $student, $isStudent, $user): void {
            $room = Room::query()->with('hostel')->lockForUpdate()->findOrFail($validated['room_id']);
            $student = Student::query()->lockForUpdate()->findOrFail($student->id);
            $hostel = Hostel::query()->lockForUpdate()->findOrFail($validated['preferred_hostel_id']);

            if ($student->status !== 'active') {
                throw ValidationException::withMessages(['student_id' => 'This student is not eligible for accommodation.']);
            }

            if ($hostel->status !== 'active') {
                throw ValidationException::withMessages(['preferred_hostel_id' => 'The selected hostel is inactive.']);
            }

            if ($room->hostel_id !== $hostel->id) {
                throw ValidationException::withMessages(['room_id' => 'Choose a room from the selected hostel.']);
            }

            if (! $hostel->acceptsGender($student->gender)) {
                throw ValidationException::withMessages(['room_id' => 'This hostel is not available for the selected student.']);
            }

            $this->assertBookablePeriod($hostel, $student);

            if ($room->status === 'maintenance') {
                throw ValidationException::withMessages(['room_id' => 'The selected room is under maintenance.']);
            }

            if ($room->availableSpacesForPeriod(
                $hostel->booking_period_start->toDateString(),
                $hostel->booking_period_end->toDateString()
            ) < 1) {
                throw ValidationException::withMessages(['room_id' => 'No beds are available in this room for this accommodation period.']);
            }

            $bookingOverlap = $student->bookings()
                ->whereIn('status', ['pending', 'approved'])
                ->whereDate('requested_check_in_date', '<', $hostel->booking_period_end->toDateString())
                ->where(function ($query) use ($hostel) {
                    $query->whereNull('requested_check_out_date')
                        ->orWhereDate('requested_check_out_date', '>', $hostel->booking_period_start->toDateString());
                })
                ->exists();
            $allocationOverlap = Allocation::query()
                ->where('student_id', $student->id)
                ->where('status', 'active')
                ->whereDate('check_in_date', '<', $hostel->booking_period_end->toDateString())
                ->where(function ($query) use ($hostel) {
                    $query->whereNull('expected_check_out_date')
                        ->orWhereDate('expected_check_out_date', '>', $hostel->booking_period_start->toDateString());
                })
                ->exists();

            if ($bookingOverlap || $allocationOverlap) {
                throw ValidationException::withMessages(['room_id' => 'This student already has accommodation for this period.']);
            }

            Booking::create([
                'student_id' => $student->id,
                'room_id' => $room->id,
                'preferred_hostel_id' => $hostel->id,
                'requested_check_in_date' => $hostel->booking_period_start->toDateString(),
                'requested_check_out_date' => $hostel->booking_period_end->toDateString(),
                'booking_date' => now()->toDateString(),
                'status' => 'pending',
                'remarks' => $validated['remarks'] ?? null,
                'processed_by' => $isStudent ? null : $user->id,
            ]);
        });

        return redirect()->route('bookings.index')->with('success', 'Accommodation booking created successfully.');
    }

    public function show(Booking $booking)
    {
        $user = auth()->user();

        if ($user->hasRole('student')) {
            abort_unless($booking->student_id === $this->studentForUser($user)->id, 404);
        }

        $booking->load(['student', 'room.hostel', 'preferredHostel', 'processor', 'allocation.room.hostel']);

        return view('bookings.show', compact('booking'));
    }

    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected,cancelled'],
            'remarks' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($booking, $validated): void {
            if (in_array($validated['status'], ['pending', 'approved'], true) && $booking->room_id) {
                $room = Room::query()->lockForUpdate()->findOrFail($booking->room_id);

                if ($room->availableSpacesForPeriod(
                    $booking->requested_check_in_date->toDateString(),
                    $booking->requested_check_out_date?->toDateString(),
                    $booking->id
                ) < 1) {
                    throw ValidationException::withMessages(['status' => 'The reserved room no longer has capacity for these dates.']);
                }
            }

            $booking->update([
                'status' => $validated['status'],
                'remarks' => $validated['remarks'] ?? $booking->remarks,
                'processed_by' => auth()->id(),
            ]);
        });

        return redirect()->route('bookings.index')->with('success', 'Booking status updated successfully.');
    }

    public function destroy(Booking $booking)
    {
        $booking->update(['status' => 'cancelled']);

        return redirect()->route('bookings.index')->with('success', 'Booking cancelled successfully.');
    }

    private function studentForUser(User $user): Student
    {
        $student = $user->student;

        abort_unless($student && $student->status === 'active', 403, 'An active student profile is required to book accommodation.');

        return $student;
    }

    private function assertBookablePeriod(Hostel $hostel, Student $student): void
    {
        if (! $hostel->booking_period_name || ! $hostel->booking_period_start || ! $hostel->booking_period_end) {
            throw ValidationException::withMessages([
                'preferred_hostel_id' => 'This hostel does not have an accommodation period configured yet.',
            ]);
        }

        if ($hostel->booking_period_end->lt(now()->startOfDay())) {
            throw ValidationException::withMessages([
                'preferred_hostel_id' => 'The booking period for this hostel has ended.',
            ]);
        }

        if (! $hostel->acceptsGender($student->gender)) {
            throw ValidationException::withMessages([
                'preferred_hostel_id' => 'This hostel is not available for the selected student.',
            ]);
        }
    }
}

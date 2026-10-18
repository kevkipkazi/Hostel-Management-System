<?php

namespace App\Http\Controllers;

use App\Models\Hostel;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class HostelController extends Controller
{
    public function index(Request $request)
    {
        $query = Hostel::query()->withCount('rooms');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($query) use ($search) {
                $query->where('hostel_name', 'like', "%{$search}%")
                    ->orWhere('hostel_code', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $hostels = $query->latest()->paginate(15)->withQueryString();

        return view('hostels.index', compact('hostels'));
    }

    public function create()
    {
        return view('hostels.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hostel_name' => ['required', 'string', 'max:255'],
            'hostel_code' => ['required', 'string', 'unique:hostels,hostel_code', 'max:50'],
            'location' => ['required', 'string', 'max:255'],
            'booking_period_name' => ['required', 'string', 'max:255'],
            'booking_period_start' => ['required', 'date'],
            'booking_period_end' => ['required', 'date', 'after:booking_period_start'],
            'room_count' => ['required', 'integer', 'min:1', 'max:500'],
            'period_fee' => ['required', 'numeric', 'min:0'],
            'gender_category' => ['required', 'in:male,female,mixed'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        DB::transaction(function () use ($validated): void {
            $hostel = Hostel::create(Arr::only($validated, [
                'hostel_name',
                'hostel_code',
                'location',
                'booking_period_name',
                'booking_period_start',
                'booking_period_end',
                'period_fee',
                'gender_category',
                'description',
                'status',
            ]));

            $this->synchronizeRooms($hostel, (int) $validated['room_count'], (float) $validated['period_fee']);
        });

        return redirect()->route('hostels.index')->with('success', 'Hostel and rooms created successfully.');
    }

    public function show(Hostel $hostel)
    {
        $hostel->load('rooms');

        return view('hostels.show', compact('hostel'));
    }

    public function edit(Hostel $hostel)
    {
        $hostel->loadCount('rooms');

        return view('hostels.edit', compact('hostel'));
    }

    public function update(Request $request, Hostel $hostel)
    {
        $validated = $request->validate([
            'hostel_name' => ['required', 'string', 'max:255'],
            'hostel_code' => ['required', 'string', 'unique:hostels,hostel_code,'.$hostel->id, 'max:50'],
            'location' => ['required', 'string', 'max:255'],
            'booking_period_name' => ['required', 'string', 'max:255'],
            'booking_period_start' => ['required', 'date'],
            'booking_period_end' => ['required', 'date', 'after:booking_period_start'],
            'room_count' => ['required', 'integer', 'min:1', 'max:500'],
            'period_fee' => ['required', 'numeric', 'min:0'],
            'gender_category' => ['required', 'in:male,female,mixed'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        DB::transaction(function () use ($hostel, $validated): void {
            $hostel->update(Arr::only($validated, [
                'hostel_name',
                'hostel_code',
                'location',
                'booking_period_name',
                'booking_period_start',
                'booking_period_end',
                'period_fee',
                'gender_category',
                'description',
                'status',
            ]));

            $this->synchronizeRooms($hostel, (int) $validated['room_count'], (float) $validated['period_fee']);
        });

        return redirect()->route('hostels.index')->with('success', 'Hostel details and room setup updated successfully.');
    }

    public function destroy(Hostel $hostel)
    {
        if ($hostel->rooms()->exists() || $hostel->bookings()->exists()) {
            return redirect()->route('hostels.index')->with('error', 'This hostel cannot be deleted because it still has related room or booking records.');
        }

        $hostel->delete();

        return redirect()->route('hostels.index')->with('success', 'Hostel deleted successfully.');
    }

    private function synchronizeRooms(Hostel $hostel, int $roomCount, float $periodFee): void
    {
        $rooms = $hostel->rooms()->orderBy('id')->get();

        if ($roomCount < $rooms->count()) {
            $roomsToRemove = $rooms->reverse()->take($rooms->count() - $roomCount);

            foreach ($roomsToRemove as $room) {
                if ($room->bookings()->exists() || $room->allocations()->exists()) {
                    throw ValidationException::withMessages([
                        'room_count' => 'Room count cannot be reduced while removed rooms have booking or allocation history.',
                    ]);
                }

                $room->delete();
            }
        }

        $hostel->rooms()->update([
            'capacity' => 2,
            'room_type' => 'double',
            'period_fee' => $periodFee,
        ]);

        while ($hostel->rooms()->count() < $roomCount) {
            $sequence = $hostel->rooms()->count() + 1;
            $roomNumber = 'R-'.str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);

            while ($hostel->rooms()->where('room_number', $roomNumber)->exists()) {
                $sequence++;
                $roomNumber = 'R-'.str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
            }

            $hostel->rooms()->create([
                'room_number' => $roomNumber,
                'capacity' => 2,
                'room_type' => 'double',
                'period_fee' => $periodFee,
                'status' => 'available',
            ]);
        }
    }
}

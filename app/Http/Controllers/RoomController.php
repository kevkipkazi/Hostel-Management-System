<?php

namespace App\Http\Controllers;

use App\Models\Hostel;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::query()->with('hostel');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('room_number', 'like', "%{$search}%")
                ->orWhereHas('hostel', fn ($q) => $q->where('hostel_name', 'like', "%{$search}%"));
        }

        if ($request->filled('hostel_id')) {
            $query->where('hostel_id', $request->hostel_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('room_type')) {
            $query->where('room_type', $request->room_type);
        }

        $rooms = $query->latest()->paginate(15)->withQueryString();
        $hostels = Hostel::all();

        return view('rooms.index', compact('rooms', 'hostels'));
    }

    public function create()
    {
        $hostels = Hostel::where('status', 'active')->get();

        return view('rooms.create', compact('hostels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hostel_id' => ['required', 'exists:hostels,id'],
            'room_number' => ['required', 'string', 'max:50'],
            'floor_number' => ['nullable', 'integer'],
            'status' => ['required', 'in:available,full,maintenance'],
        ]);

        $hostel = Hostel::where('status', 'active')->findOrFail($validated['hostel_id']);

        $validated['room_number'] = strtoupper($validated['room_number']);

        $exists = Room::where('hostel_id', $validated['hostel_id'])
            ->where('room_number', $validated['room_number'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['room_number' => 'This room number already exists in the selected hostel.'])->withInput();
        }

        Room::create([
            'hostel_id' => $hostel->id,
            'room_number' => $validated['room_number'],
            'floor_number' => $validated['floor_number'] ?? null,
            'capacity' => 2,
            'room_type' => 'double',
            'period_fee' => $hostel->period_fee,
            'status' => $validated['status'],
        ]);

        return redirect()->route('rooms.index')->with('success', 'Room registered successfully.');
    }

    public function show(Room $room)
    {
        $room->load(['hostel', 'allocations.student']);

        return view('rooms.show', compact('room'));
    }

    public function edit(Room $room)
    {
        $hostels = Hostel::where('status', 'active')->get();

        return view('rooms.edit', compact('room', 'hostels'));
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'hostel_id' => ['required', 'exists:hostels,id'],
            'room_number' => ['required', 'string', 'max:50'],
            'floor_number' => ['nullable', 'integer'],
            'status' => ['required', 'in:available,full,maintenance'],
        ]);

        $hostel = Hostel::where('status', 'active')->findOrFail($validated['hostel_id']);

        if ($room->hostel_id !== $hostel->id && ($room->bookings()->exists() || $room->allocations()->exists())) {
            return back()->withErrors(['hostel_id' => 'A room with booking or allocation history cannot be moved to another hostel.'])->withInput();
        }

        $validated['room_number'] = strtoupper($validated['room_number']);

        $exists = Room::where('hostel_id', $validated['hostel_id'])
            ->where('room_number', $validated['room_number'])
            ->whereKeyNot($room->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['room_number' => 'This room number already exists in the selected hostel.'])->withInput();
        }

        $room->update([
            'hostel_id' => $hostel->id,
            'room_number' => $validated['room_number'],
            'floor_number' => $validated['floor_number'] ?? null,
            'capacity' => 2,
            'room_type' => 'double',
            'period_fee' => $hostel->period_fee,
            'status' => $validated['status'],
        ]);

        return redirect()->route('rooms.index')->with('success', 'Room details updated successfully.');
    }

    public function destroy(Room $room)
    {
        if ($room->allocations()->exists() || $room->bookings()->exists()) {
            return redirect()->route('rooms.index')->with('error', 'This room has booking or allocation records and cannot be deleted.');
        }

        $room->delete();

        return redirect()->route('rooms.index')->with('success', 'Room deleted successfully.');
    }
}

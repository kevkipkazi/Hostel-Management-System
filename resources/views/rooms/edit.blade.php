@extends('layouts.app')

@section('content')
<div class="page-title">Edit Room</div>
<form method="POST" action="{{ route('rooms.update', $room) }}" class="card card-body shadow-sm border-0 rounded-4">
    @csrf
    @method('PUT')
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Hostel</label>
            <select name="hostel_id" class="form-select" required>
                @foreach($hostels as $hostel)
                    <option value="{{ $hostel->id }}" {{ old('hostel_id', $room->hostel_id) == $hostel->id ? 'selected' : '' }}>{{ $hostel->hostel_name }} · {{ number_format((float) $hostel->period_fee, 2) }} per bed / period</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6"><label class="form-label">Room number</label><input type="text" name="room_number" value="{{ old('room_number', $room->room_number) }}" class="form-control" required></div>
        <div class="col-md-3"><label class="form-label">Floor number</label><input type="number" name="floor_number" value="{{ old('floor_number', $room->floor_number) }}" class="form-control"></div>
        <div class="col-md-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="available" {{ old('status', $room->status) == 'available' ? 'selected' : '' }}>Available</option><option value="full" {{ old('status', $room->status) == 'full' ? 'selected' : '' }}>Full</option><option value="maintenance" {{ old('status', $room->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option></select></div>
    </div>
    <p class="text-muted mt-3">This room has two beds. Its fee is {{ number_format((float) $room->hostel?->period_fee, 2) }} per bed for the selected hostel period.</p>
    <div class="mt-4"><button type="submit" class="btn btn-primary">Update room</button> <a href="{{ route('rooms.index') }}" class="btn btn-outline-secondary">Cancel</a></div>
</form>
@endsection

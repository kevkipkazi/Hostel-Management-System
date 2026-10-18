@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="page-title mb-0">Rooms</div>
    <a href="{{ route('rooms.create') }}" class="btn btn-primary">Add room</a>
</div>

<form method="GET" class="row g-2 mb-3">
    <div class="col-md-3"><input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Room no or hostel"></div>
    <div class="col-md-3">
        <select name="hostel_id" class="form-select">
            <option value="">All hostels</option>
            @foreach($hostels as $hostel)
                <option value="{{ $hostel->id }}" {{ request('hostel_id') == $hostel->id ? 'selected' : '' }}>{{ $hostel->hostel_name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <select name="status" class="form-select">
            <option value="">All status</option>
            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
            <option value="full" {{ request('status') == 'full' ? 'selected' : '' }}>Full</option>
            <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
        </select>
    </div>
    <div class="col-md-2"><button class="btn btn-outline-dark w-100">Filter</button></div>
</form>

<table class="table table-hover">
    <thead><tr><th>Hostel</th><th>Room</th><th>Booked beds</th><th>Capacity</th><th>Fee per bed / period</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
        @foreach($rooms as $room)
        <tr>
            <td>{{ $room->hostel?->hostel_name }}</td>
            <td>{{ $room->room_number }}</td>
            <td>{{ $room->bookedSpaces() }} / {{ $room->capacity }}</td>
            <td>{{ $room->capacity }}</td>
            <td>{{ number_format($room->period_fee, 2) }}</td>
            <td><span class="badge-status {{ str($room->availabilityStatus())->slug() }}">{{ str($room->availabilityStatus())->headline() }}</span></td>
            <td><a href="{{ route('rooms.show', $room) }}" class="btn btn-sm btn-outline-primary">View</a></td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $rooms->links() }}
@endsection

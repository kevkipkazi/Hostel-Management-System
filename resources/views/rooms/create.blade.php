@extends('layouts.app')

@section('content')
<div class="page-title">Add Room</div>
<form method="POST" action="{{ route('rooms.store') }}" class="card card-body shadow-sm border-0 rounded-4">
    @csrf
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Hostel</label>
            <select name="hostel_id" class="form-select" required>
                @foreach($hostels as $hostel)
                    <option value="{{ $hostel->id }}" @selected(old('hostel_id', request('hostel_id')) == $hostel->id)>{{ $hostel->hostel_name }} · {{ number_format((float) $hostel->period_fee, 2) }} per bed / period</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6"><label class="form-label">Room number</label><input type="text" name="room_number" value="{{ old('room_number') }}" class="form-control" required></div>
        <div class="col-md-3"><label class="form-label">Floor number</label><input type="number" name="floor_number" value="{{ old('floor_number') }}" class="form-control"></div>
        <div class="col-md-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="available">Available</option><option value="full">Full</option><option value="maintenance">Maintenance</option></select></div>
    </div>
    <p class="text-muted mt-3">Every room has two beds and uses the selected hostel's fee for its accommodation period.</p>
    <div class="mt-4"><button type="submit" class="btn btn-primary">Save room</button> <a href="{{ route('rooms.index') }}" class="btn btn-outline-secondary">Cancel</a></div>
</form>
@endsection

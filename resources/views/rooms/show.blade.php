@extends('layouts.app')

@section('content')
<div class="page-title">Room {{ $room->room_number }}</div>
<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-body">
        <p><strong>Hostel:</strong> {{ $room->hostel?->hostel_name }}</p>
        <p><strong>Accommodation period:</strong> {{ $room->hostel?->booking_period_name ?? 'Not configured' }}</p>
        <p><strong>Period dates:</strong> {{ optional($room->hostel?->booking_period_start)->format('M j, Y') }} – {{ optional($room->hostel?->booking_period_end)->format('M j, Y') }}</p>
        <p><strong>Beds booked:</strong> {{ $room->bookedSpaces() }} / {{ $room->capacity }}</p>
        <p><strong>Accommodation period fee per bed:</strong> {{ number_format($room->period_fee, 2) }}</p>
        <p><strong>Status:</strong> <span class="badge-status {{ str($room->availabilityStatus())->slug() }}">{{ str($room->availabilityStatus())->headline() }}</span></p>
    </div>
</div>
@endsection

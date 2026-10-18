@extends('layouts.app')

@section('title', 'Booking details | '.config('app.name', 'Hostel Management System'))

@section('content')
<div class="ops-page ops-detail-page">
    <header class="ops-heading">
        <div>
            <a class="ops-back-link" href="{{ route('bookings.index') }}"><span aria-hidden="true">&larr;</span> Bookings</a>
            <p class="ops-eyebrow">Accommodation / Request details</p>
            <div class="ops-title-row">
                <h1>{{ $booking->student?->fullName() ?? 'Booking details' }}</h1>
                <span class="ops-status status-{{ str($booking->status)->slug() }}"><span></span>{{ str($booking->status)->headline() }}</span>
            </div>
            <p>Booking request #{{ $booking->id }}</p>
        </div>
    </header>

    @if ($errors->any())
        <div class="booking-validation-summary" role="alert">
            <strong>Please check the booking update.</strong>
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <section class="ops-panel" aria-label="Booking information">
        <div class="ops-panel-heading"><div><h2>Request details</h2><p>Student and requested accommodation dates.</p></div></div>
        <dl class="ops-facts-grid">
            <div><dt>Student</dt><dd>{{ $booking->student?->fullName() ?? 'Unavailable' }}</dd><small>{{ $booking->student?->admission_number ?? 'No admission number' }}</small></div>
            <div><dt>Reserved room</dt><dd>{{ $booking->room?->room_number ? 'Room '.$booking->room->room_number : 'Not assigned' }}</dd><small>{{ $booking->room?->hostel?->hostel_name ?? $booking->preferredHostel?->hostel_name ?? 'Hostel unavailable' }}</small></div>
            <div><dt>Status</dt><dd>{{ str($booking->status)->headline() }}</dd></div>
            <div><dt>Accommodation period</dt><dd>{{ $booking->preferredHostel?->booking_period_name ?? 'Period unavailable' }}</dd></div>
            <div><dt>Check-in</dt><dd>{{ optional($booking->requested_check_in_date)->format('M j, Y') ?? 'Not set' }}</dd></div>
            <div><dt>Check-out</dt><dd>{{ optional($booking->requested_check_out_date)->format('M j, Y') ?? 'Not set' }}</dd></div>
            <div><dt>Processed by</dt><dd>{{ $booking->processor?->name ?? 'Not recorded' }}</dd></div>
        </dl>
        <div class="booking-remarks"><strong>Remarks</strong><p>{{ $booking->remarks ?: 'No remarks provided.' }}</p></div>
    </section>

    @if (! auth()->user()->hasRole('student'))
    <section class="ops-panel booking-update-panel" aria-label="Update booking">
        <div class="ops-panel-heading"><div><h2>Update request</h2><p>Change the booking status or add a note.</p></div></div>
        <form method="POST" action="{{ route('bookings.update', $booking) }}" class="booking-update-form">
            @csrf
            @method('PUT')
            <label class="booking-field" for="booking-update-status">
                <span>Status <b>*</b></span>
                <select id="booking-update-status" name="status" required @class(['has-error' => $errors->has('status')])>
                    <option value="pending" @selected(old('status', $booking->status) === 'pending')>Pending</option>
                    <option value="approved" @selected(old('status', $booking->status) === 'approved')>Approved</option>
                    <option value="rejected" @selected(old('status', $booking->status) === 'rejected')>Rejected</option>
                    <option value="cancelled" @selected(old('status', $booking->status) === 'cancelled')>Cancelled</option>
                </select>
                @error('status')<small class="booking-field-error">{{ $message }}</small>@enderror
            </label>
            <label class="booking-field" for="booking-update-remarks">
                <span>Remarks <small>Optional</small></span>
                <textarea id="booking-update-remarks" name="remarks" rows="3" @class(['has-error' => $errors->has('remarks')])>{{ old('remarks', $booking->remarks) }}</textarea>
                @error('remarks')<small class="booking-field-error">{{ $message }}</small>@enderror
            </label>
            <div class="booking-update-actions"><button class="ops-primary-link" type="submit">Update booking</button></div>
        </form>
    </section>
    @endif
</div>
@endsection

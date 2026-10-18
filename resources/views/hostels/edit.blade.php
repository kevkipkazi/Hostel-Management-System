@extends('layouts.app')

@section('title', 'Edit '.$hostel->hostel_name.' | '.config('app.name', 'Hostel Management System'))

@section('content')
<div class="hostel-form-page">
    <header class="hostel-form-heading">
        <div>
            <a class="hostel-back-link" href="{{ route('hostels.show', $hostel) }}"><span aria-hidden="true">&larr;</span> Hostel details</a>
            <p class="hostels-eyebrow">Accommodation / Edit record</p>
            <h1>Edit hostel</h1>
            <p class="hostels-subtitle">Update the details for {{ $hostel->hostel_name }}.</p>
        </div>
    </header>

    @if ($errors->any())
        <div class="hostel-form-alert" role="alert">
            <strong>Please check the highlighted fields.</strong>
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('hostels.update', $hostel) }}" class="hostel-form-card">
        @csrf
        @method('PUT')
        <div class="hostel-form-section-heading">
            <span class="hostel-form-section-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21V5l9-3 9 3v16M8 21v-5h8v5M7 8h.01M12 8h.01M17 8h.01" /></svg></span>
            <span><strong>Hostel information</strong><small>Changes will appear throughout the system.</small></span>
        </div>

        <div class="hostel-form-grid">
            <label class="hostel-form-field" for="hostel-name">
                <span>Hostel name <b>*</b></span>
                <input id="hostel-name" type="text" name="hostel_name" value="{{ old('hostel_name', $hostel->hostel_name) }}" autocomplete="organization" required @class(['has-error' => $errors->has('hostel_name')])>
                @error('hostel_name')<small class="hostel-field-error">{{ $message }}</small>@enderror
            </label>
            <label class="hostel-form-field" for="hostel-code">
                <span>Hostel code <b>*</b></span>
                <input id="hostel-code" type="text" name="hostel_code" value="{{ old('hostel_code', $hostel->hostel_code) }}" required @class(['has-error' => $errors->has('hostel_code')])>
                @error('hostel_code')<small class="hostel-field-error">{{ $message }}</small>@enderror
            </label>
            <label class="hostel-form-field" for="hostel-location">
                <span>Location <b>*</b></span>
                <input id="hostel-location" type="text" name="location" value="{{ old('location', $hostel->location) }}" required @class(['has-error' => $errors->has('location')])>
                @error('location')<small class="hostel-field-error">{{ $message }}</small>@enderror
            </label>
            <label class="hostel-form-field" for="hostel-period-name">
                <span>Accommodation period <b>*</b></span>
                <input id="hostel-period-name" type="text" name="booking_period_name" value="{{ old('booking_period_name', $hostel->booking_period_name) }}" required @class(['has-error' => $errors->has('booking_period_name')])>
                @error('booking_period_name')<small class="hostel-field-error">{{ $message }}</small>@enderror
            </label>
            <label class="hostel-form-field" for="hostel-period-start">
                <span>Period starts <b>*</b></span>
                <input id="hostel-period-start" type="date" name="booking_period_start" value="{{ old('booking_period_start', optional($hostel->booking_period_start)->format('Y-m-d')) }}" required @class(['has-error' => $errors->has('booking_period_start')])>
                @error('booking_period_start')<small class="hostel-field-error">{{ $message }}</small>@enderror
            </label>
            <label class="hostel-form-field" for="hostel-period-end">
                <span>Period ends <b>*</b></span>
                <input id="hostel-period-end" type="date" name="booking_period_end" value="{{ old('booking_period_end', optional($hostel->booking_period_end)->format('Y-m-d')) }}" required @class(['has-error' => $errors->has('booking_period_end')])>
                @error('booking_period_end')<small class="hostel-field-error">{{ $message }}</small>@enderror
            </label>
            <label class="hostel-form-field" for="hostel-room-count">
                <span>Number of rooms <b>*</b></span>
                <input id="hostel-room-count" type="number" name="room_count" value="{{ old('room_count', $hostel->rooms_count) }}" min="1" max="500" step="1" required @class(['has-error' => $errors->has('room_count')])>
                @error('room_count')<small class="hostel-field-error">{{ $message }}</small>@enderror
            </label>
            <label class="hostel-form-field" for="hostel-period-fee">
                <span>Fee per bed for this period <b>*</b></span>
                <input id="hostel-period-fee" type="number" name="period_fee" value="{{ old('period_fee', $hostel->period_fee) }}" min="0" step="0.01" required @class(['has-error' => $errors->has('period_fee')])>
                @error('period_fee')<small class="hostel-field-error">{{ $message }}</small>@enderror
            </label>
            <label class="hostel-form-field" for="hostel-category">
                <span>Gender category <b>*</b></span>
                <select id="hostel-category" name="gender_category" required @class(['has-error' => $errors->has('gender_category')])>
                    <option value="male" @selected(old('gender_category', $hostel->gender_category) === 'male')>Male</option>
                    <option value="female" @selected(old('gender_category', $hostel->gender_category) === 'female')>Female</option>
                    <option value="mixed" @selected(old('gender_category', $hostel->gender_category) === 'mixed')>Mixed</option>
                </select>
                @error('gender_category')<small class="hostel-field-error">{{ $message }}</small>@enderror
            </label>
            <label class="hostel-form-field" for="hostel-status">
                <span>Status <b>*</b></span>
                <select id="hostel-status" name="status" required @class(['has-error' => $errors->has('status')])>
                    <option value="active" @selected(old('status', $hostel->status) === 'active')>Active</option>
                    <option value="inactive" @selected(old('status', $hostel->status) === 'inactive')>Inactive</option>
                </select>
                @error('status')<small class="hostel-field-error">{{ $message }}</small>@enderror
            </label>
            <label class="hostel-form-field hostel-description-field" for="hostel-description">
                <span>Description <small>Optional</small></span>
                <textarea id="hostel-description" name="description" rows="4" placeholder="Add helpful notes about this residence..." @class(['has-error' => $errors->has('description')])>{{ old('description', $hostel->description) }}</textarea>
                @error('description')<small class="hostel-field-error">{{ $message }}</small>@enderror
            </label>
        </div>

        <footer class="hostel-form-actions">
            <a class="hostel-form-cancel" href="{{ route('hostels.show', $hostel) }}">Cancel</a>
            <button class="hostel-form-submit" type="submit"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" /></svg>Save changes</button>
        </footer>
    </form>
</div>
@endsection

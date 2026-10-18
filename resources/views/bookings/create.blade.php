@extends('layouts.app')

@section('title', 'New booking | '.config('app.name', 'Hostel Management System'))

@section('content')
<div class="booking-form-page">
    <header class="booking-form-heading">
        <div>
            <a class="ops-back-link" href="{{ route('bookings.index') }}"><span aria-hidden="true">&larr;</span> Bookings</a>
            <p class="ops-eyebrow">Accommodation / New request</p>
            <h1>Create a booking</h1>
            <p>Choose a bed for the accommodation period set by the hostel manager.</p>
        </div>
    </header>

    @if ($errors->any())
        <div class="booking-validation-summary" role="alert">
            <strong>Please check the booking details.</strong>
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('bookings.store') }}" class="booking-form-card" data-availability-url="{{ route('bookings.available-rooms') }}">
        @csrf
        <div class="booking-form-intro">
            <span class="allocation-form-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 11h18M8 15h3" /></svg></span>
            <span><strong>Request details</strong><small>Required fields are marked with <b>*</b>.</small></span>
        </div>

        <div class="booking-form-grid">
            @if ($studentProfile)
                <div class="booking-student-profile">
                    <span>Booking for</span>
                    <strong>{{ $studentProfile->fullName() }}</strong>
                    <small>{{ $studentProfile->admission_number }}</small>
                </div>
            @else
                <label class="booking-field" for="booking-student">
                    <span>Student <b>*</b></span>
                    <select id="booking-student" name="student_id" required @class(['has-error' => $errors->has('student_id')])>
                        <option value="" disabled @selected(old('student_id') === null)>Choose a student</option>
                        @foreach ($students as $student)
                            <option value="{{ $student->id }}" @selected((string) old('student_id') === (string) $student->id)>{{ $student->fullName() }} · {{ $student->admission_number }}</option>
                        @endforeach
                    </select>
                    @error('student_id')<small class="booking-field-error">{{ $message }}</small>@enderror
                </label>
            @endif

            <label class="booking-field" for="booking-hostel">
                <span>Hostel and period <b>*</b></span>
                <select id="booking-hostel" name="preferred_hostel_id" required @class(['has-error' => $errors->has('preferred_hostel_id')])>
                    <option value="">Choose a hostel period</option>
                    @foreach ($hostels as $hostel)
                        <option value="{{ $hostel->id }}" data-period-name="{{ $hostel->booking_period_name }}" data-period-start="{{ $hostel->booking_period_start->format('M j, Y') }}" data-period-end="{{ $hostel->booking_period_end->format('M j, Y') }}" @selected((string) old('preferred_hostel_id') === (string) $hostel->id)>{{ $hostel->hostel_name }} · {{ $hostel->booking_period_name }}</option>
                    @endforeach
                </select>
                @error('preferred_hostel_id')<small class="booking-field-error">{{ $message }}</small>@enderror
            </label>

            <div class="booking-period-summary" id="booking-period-summary" role="status" aria-live="polite">
                Choose a hostel to see its accommodation period.
            </div>

            <label class="booking-field" for="booking-room">
                <span>Room <b>*</b></span>
                <select id="booking-room" name="room_id" required disabled @class(['has-error' => $errors->has('room_id')])>
                    <option value="">Choose a student and period first</option>
                </select>
                @error('room_id')<small class="booking-field-error">{{ $message }}</small>@enderror
                <small id="booking-room-availability" class="booking-field-error" role="status" aria-live="polite"></small>
            </label>

            <label class="booking-field booking-remarks-field" for="booking-remarks">
                <span>Remarks <small>Optional</small></span>
                <textarea id="booking-remarks" name="remarks" rows="4" @class(['has-error' => $errors->has('remarks')])>{{ old('remarks') }}</textarea>
                @error('remarks')<small class="booking-field-error">{{ $message }}</small>@enderror
            </label>
        </div>

        @if (! $studentProfile && $students->isEmpty())
            <div class="booking-form-note" role="status">No active students are available for a booking request.</div>
        @endif
        @if ($hostels->isEmpty())
            <div class="booking-form-note" role="status">No active hostel periods are available for booking. Contact the hostel manager.</div>
        @endif

        <footer class="booking-form-actions">
            <a href="{{ route('bookings.index') }}">Cancel</a>
            <button type="submit" @disabled((! $studentProfile && $students->isEmpty()) || $hostels->isEmpty())>Create booking</button>
        </footer>
    </form>
</div>

<script>
(() => {
    const form = document.querySelector('.booking-form-card');

    if (!form) {
        return;
    }

    const studentSelect = form.querySelector('#booking-student');
    const hostelSelect = form.querySelector('#booking-hostel');
    const roomSelect = form.querySelector('#booking-room');
    const availabilityMessage = form.querySelector('#booking-room-availability');
    const periodSummary = form.querySelector('#booking-period-summary');
    const selectedRoomId = @js(old('room_id'));
    let activeAvailabilityRequest;

    const refreshAvailableRooms = async () => {
        activeAvailabilityRequest?.abort();
        roomSelect.replaceChildren(new Option('Choose a student and period first', ''));
        roomSelect.disabled = true;

        const selectedHostel = hostelSelect.selectedOptions[0];

        if ((studentSelect && !studentSelect.value) || !hostelSelect.value) {
            availabilityMessage.textContent = 'Select a student and hostel period to see available rooms.';
            periodSummary.textContent = 'Choose a hostel to see its accommodation period.';
            return;
        }

        periodSummary.textContent = `${selectedHostel.dataset.periodName}: ${selectedHostel.dataset.periodStart} to ${selectedHostel.dataset.periodEnd}`;
        const query = new URLSearchParams({ preferred_hostel_id: hostelSelect.value });

        if (studentSelect) {
            query.set('student_id', studentSelect.value);
        }

        if (hostelSelect?.value) {
            query.set('preferred_hostel_id', hostelSelect.value);
        }

        availabilityMessage.textContent = 'Checking room availability...';
        activeAvailabilityRequest = new AbortController();

        try {
            const response = await fetch(`${form.dataset.availabilityUrl}?${query}`, {
                headers: { Accept: 'application/json' },
                signal: activeAvailabilityRequest.signal,
            });

            if (!response.ok) {
                throw new Error('Availability request failed.');
            }

            const rooms = await response.json();

            if (rooms.length === 0) {
                roomSelect.replaceChildren(new Option('No beds available for this period', ''));
                availabilityMessage.textContent = 'No rooms have available beds for this hostel period.';
                return;
            }

            roomSelect.replaceChildren(new Option('Choose a room', ''));
            rooms.forEach((room) => {
                const option = new Option(
                    `${room.room_number} · ${room.available_spaces} bed${room.available_spaces === 1 ? '' : 's'} available · ${room.period_fee.toLocaleString()} per bed for ${room.period_name}`,
                    room.id
                );
                option.selected = String(room.id) === String(selectedRoomId);
                roomSelect.add(option);
            });

            roomSelect.disabled = false;
            availabilityMessage.textContent = `${rooms.length} room${rooms.length === 1 ? '' : 's'} available for this period.`;
        } catch (error) {
            if (error.name === 'AbortError') {
                return;
            }

            roomSelect.replaceChildren(new Option('Availability could not be checked', ''));
            availabilityMessage.textContent = 'Refresh the room list before submitting your booking.';
        }
    };

    studentSelect?.addEventListener('change', refreshAvailableRooms);
    hostelSelect?.addEventListener('change', refreshAvailableRooms);
    refreshAvailableRooms();
})();
</script>
@endsection

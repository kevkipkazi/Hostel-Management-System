@extends('layouts.app')

@section('title', 'Allocate room | '.config('app.name', 'Hostel Management System'))

@section('content')
	<div class="allocation-form-page">
		<header class="allocation-form-heading">
			<a class="allocation-back-link" href="{{ route('allocations.index') }}"><span aria-hidden="true">&larr;</span> Allocations</a>
			<p class="dashboard-eyebrow">Operations / New allocation</p>
			<h1>Allocate a room</h1>
			<p>Assign an active student to a room and set their stay dates.</p>
		</header>

		@if ($errors->any())
			<div class="allocation-form-alert" role="alert">
				<strong>Check the allocation details.</strong>
				<ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
			</div>
		@endif

		<form method="POST" action="{{ route('allocations.store') }}" class="allocation-form-card">
			@csrf
			<div class="allocation-form-intro">
				<span class="allocation-form-icon" aria-hidden="true">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h7v7H4zM13 4h7v7h-7zM4 13h7v7H4zM16.5 13v7M13 16.5h7" /></svg>
				</span>
				<span><strong>Allocation details</strong><small>Required fields are marked with <b>*</b>.</small></span>
			</div>

			<div class="allocation-form-grid">
				<label class="allocation-field" for="allocation-student">
					<span>Student <b>*</b></span>
					<select id="allocation-student" name="student_id" required @class(['has-error' => $errors->has('student_id')])>
						<option value="" disabled @selected(old('student_id') === null)>Choose a student</option>
						@foreach ($students as $student)
							<option value="{{ $student->id }}" @selected((string) old('student_id') === (string) $student->id)>{{ $student->fullName() }} · {{ $student->admission_number }}</option>
						@endforeach
					</select>
					@error('student_id')<small class="allocation-field-error">{{ $message }}</small>@enderror
				</label>

				<label class="allocation-field" for="allocation-room">
					<span>Room <b>*</b></span>
					<select id="allocation-room" name="room_id" required @class(['has-error' => $errors->has('room_id')])>
						<option value="" disabled @selected(old('room_id') === null)>Choose a room</option>
						@foreach ($rooms as $room)
							<option value="{{ $room->id }}" @selected((string) old('room_id') === (string) $room->id)>{{ $room->hostel?->hostel_name }} · {{ $room->room_number }} · {{ $room->availableSpaces() }} beds available</option>
						@endforeach
					</select>
					@error('room_id')<small class="allocation-field-error">{{ $message }}</small>@enderror
				</label>

				<label class="allocation-field" for="allocation-date">
					<span>Allocation date <b>*</b></span>
					<input id="allocation-date" type="date" name="allocation_date" value="{{ old('allocation_date', now()->toDateString()) }}" required @class(['has-error' => $errors->has('allocation_date')])>
					@error('allocation_date')<small class="allocation-field-error">{{ $message }}</small>@enderror
				</label>

				<label class="allocation-field" for="check-in-date">
					<span>Check-in date <b>*</b></span>
					<input id="check-in-date" type="date" name="check_in_date" value="{{ old('check_in_date', now()->toDateString()) }}" required @class(['has-error' => $errors->has('check_in_date')])>
					@error('check_in_date')<small class="allocation-field-error">{{ $message }}</small>@enderror
				</label>

				<label class="allocation-field" for="expected-checkout">
					<span>Expected check-out <small>Optional</small></span>
					<input id="expected-checkout" type="date" name="expected_check_out_date" value="{{ old('expected_check_out_date') }}" @class(['has-error' => $errors->has('expected_check_out_date')])>
					@error('expected_check_out_date')<small class="allocation-field-error">{{ $message }}</small>@enderror
				</label>
			</div>

			@if ($students->isEmpty() || $rooms->isEmpty())
				<div class="allocation-form-note" role="status">
					@if ($students->isEmpty())
						No active students are available for allocation.
					@elseif ($rooms->isEmpty())
						No rooms are available for allocation.
					@endif
				</div>
			@endif

			<footer class="allocation-form-actions">
				<a href="{{ route('allocations.index') }}">Cancel</a>
				<button type="submit" @disabled($students->isEmpty() || $rooms->isEmpty())>
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" /></svg>
					Create allocation
				</button>
			</footer>
		</form>
	</div>
@endsection

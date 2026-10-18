@extends('layouts.app')

@section('title', 'Record payment | '.config('app.name', 'Hostel Management System'))

@section('content')
<div class="payment-entry-page">
    <header class="payment-entry-heading">
        <div>
            <a class="ops-back-link" href="{{ route('payments.index') }}"><span aria-hidden="true">&larr;</span> Payments</a>
            <p class="ops-eyebrow">Finance / New transaction</p>
            <h1>Record a payment</h1>
            <p>Enter the student, amount, and transaction details.</p>
        </div>
    </header>

    @if ($errors->any())
        <div class="payment-validation-summary" role="alert">
            <strong>Please check the payment details.</strong>
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('payments.store') }}" class="payment-entry-card">
        @csrf
        <div class="payment-entry-intro">
            <span class="allocation-form-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7h18v13H3zM3 10h18M7 15h4"/><path d="M7 4h10"/></svg></span>
            <span><strong>Payment details</strong><small>Required fields are marked with <b>*</b>.</small></span>
        </div>

        <div class="payment-entry-grid">
            <label class="payment-field" for="payment-student">
                <span>Student <b>*</b></span>
                <select id="payment-student" name="student_id" required @class(['has-error' => $errors->has('student_id')])>
                    <option value="" disabled @selected(old('student_id') === null)>Choose a student</option>
                    @foreach ($students as $student)
                        <option value="{{ $student->id }}" @selected((string) old('student_id') === (string) $student->id)>{{ $student->fullName() }} · {{ $student->admission_number }}</option>
                    @endforeach
                </select>
                @error('student_id')<small class="payment-field-error">{{ $message }}</small>@enderror
            </label>

            <label class="payment-field" for="payment-allocation">
                <span>Allocation <small>Optional</small></span>
                <select id="payment-allocation" name="allocation_id" @class(['has-error' => $errors->has('allocation_id')])>
                    <option value="">No allocation</option>
                    @foreach ($allocations as $allocation)
                        <option value="{{ $allocation->id }}" @selected((string) old('allocation_id') === (string) $allocation->id)>Room {{ $allocation->room?->room_number ?? 'unassigned' }} · Allocation #{{ $allocation->id }}</option>
                    @endforeach
                </select>
                @error('allocation_id')<small class="payment-field-error">{{ $message }}</small>@enderror
            </label>

            <label class="payment-field" for="payment-amount">
                <span>Amount paid <b>*</b></span>
                <input id="payment-amount" type="number" min="0.01" step="0.01" name="amount_paid" value="{{ old('amount_paid') }}" required @class(['has-error' => $errors->has('amount_paid')])>
                @error('amount_paid')<small class="payment-field-error">{{ $message }}</small>@enderror
            </label>

            <label class="payment-field" for="payment-date">
                <span>Payment date <b>*</b></span>
                <input id="payment-date" type="date" name="payment_date" value="{{ old('payment_date', now()->toDateString()) }}" required @class(['has-error' => $errors->has('payment_date')])>
                @error('payment_date')<small class="payment-field-error">{{ $message }}</small>@enderror
            </label>

            <label class="payment-field" for="payment-method">
                <span>Method <b>*</b></span>
                <select id="payment-method" name="payment_method" required @class(['has-error' => $errors->has('payment_method')])>
                    <option value="cash" @selected(old('payment_method', 'cash') === 'cash')>Cash</option>
                    <option value="bank" @selected(old('payment_method') === 'bank')>Bank</option>
                    <option value="mobile_money" @selected(old('payment_method') === 'mobile_money')>Mobile money</option>
                    <option value="other" @selected(old('payment_method') === 'other')>Other</option>
                </select>
                @error('payment_method')<small class="payment-field-error">{{ $message }}</small>@enderror
            </label>

            <label class="payment-field" for="payment-period">
                <span>Payment period <small>Optional</small></span>
                <input id="payment-period" type="text" name="payment_period" value="{{ old('payment_period') }}" placeholder="For example, October 2026" @class(['has-error' => $errors->has('payment_period')])>
                @error('payment_period')<small class="payment-field-error">{{ $message }}</small>@enderror
            </label>

            <label class="payment-field" for="payment-reference">
                <span>Transaction reference <small>Optional</small></span>
                <input id="payment-reference" type="text" name="transaction_reference" value="{{ old('transaction_reference') }}" @class(['has-error' => $errors->has('transaction_reference')])>
                @error('transaction_reference')<small class="payment-field-error">{{ $message }}</small>@enderror
            </label>

            <label class="payment-field" for="payment-remarks">
                <span>Remarks <small>Optional</small></span>
                <textarea id="payment-remarks" name="remarks" rows="3" @class(['has-error' => $errors->has('remarks')])>{{ old('remarks') }}</textarea>
                @error('remarks')<small class="payment-field-error">{{ $message }}</small>@enderror
            </label>
        </div>

        @if ($students->isEmpty())
            <div class="payment-entry-note" role="status">No students are available to record a payment for.</div>
        @endif

        <footer class="payment-entry-actions">
            <a href="{{ route('payments.index') }}">Cancel</a>
            <button type="submit" @disabled($students->isEmpty())>Record payment</button>
        </footer>
    </form>
</div>
@endsection

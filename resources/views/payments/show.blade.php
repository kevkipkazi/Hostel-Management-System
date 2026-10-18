@extends('layouts.app')

@section('title', 'Receipt '.$payment->receipt_number.' | '.config('app.name', 'Hostel Management System'))

@section('content')
<div class="ops-page ops-detail-page payment-receipt-page">
    <header class="ops-heading">
        <div>
            <a class="ops-back-link" href="{{ route('payments.index') }}"><span aria-hidden="true">&larr;</span> Payments</a>
            <p class="ops-eyebrow">Finance / Payment receipt</p>
            <div class="ops-title-row"><h1>Payment receipt</h1><span class="payment-receipt-code">{{ $payment->receipt_number }}</span></div>
            <p>Recorded {{ optional($payment->payment_date)->format('M j, Y') ?? 'on an unknown date' }}</p>
        </div>
    </header>

    <section class="ops-panel" aria-label="Payment receipt details">
        <div class="ops-panel-heading"><div><h2>Receipt details</h2><p>Payment record and student information.</p></div></div>
        <div class="payment-amount-summary"><span>Amount paid</span><strong>{{ number_format((float) $payment->amount_paid, 2) }}</strong></div>
        <dl class="ops-facts-grid">
            <div><dt>Student</dt><dd>{{ $payment->student?->fullName() ?? 'Unavailable' }}</dd><small>{{ $payment->student?->admission_number ?? 'No admission number' }}</small></div>
            <div><dt>Payment date</dt><dd>{{ optional($payment->payment_date)->format('M j, Y') ?? 'Not set' }}</dd></div>
            <div><dt>Payment method</dt><dd>{{ str($payment->payment_method)->headline() }}</dd></div>
            <div><dt>Allocation</dt><dd>{{ $payment->allocation?->room?->room_number ? 'Room '.$payment->allocation->room->room_number : 'No allocation' }}</dd></div>
            <div><dt>Payment period</dt><dd>{{ $payment->payment_period ?: 'Not specified' }}</dd></div>
            <div><dt>Recorded by</dt><dd>{{ $payment->recorder?->name ?? 'Not recorded' }}</dd></div>
            <div><dt>Transaction reference</dt><dd>{{ $payment->transaction_reference ?: 'Not provided' }}</dd></div>
        </dl>
        @if ($payment->remarks)
            <div class="payment-receipt-remarks"><strong>Remarks</strong><p>{{ $payment->remarks }}</p></div>
        @endif
    </section>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Payment report | '.config('app.name', 'Hostel Management System'))

@section('content')
<div class="ops-page">
    <header class="ops-heading">
        <div>
            <p class="ops-eyebrow">Finance / Reports</p>
            <h1>Payment report</h1>
            <p>Recorded transactions and payment totals.</p>
        </div>
        <a class="ops-primary-link" href="{{ route('reports.outstanding-fees') }}">Outstanding fees</a>
    </header>

    <section class="ops-panel" aria-label="Payment report">
        <div class="ops-panel-heading">
            <div><h2>Payments</h2><p>{{ number_format($payments->count()) }} transactions</p></div>
            <span class="ops-page-count">Total {{ number_format((float) $payments->sum('amount_paid'), 2) }}</span>
        </div>
        <div class="ops-table-scroll">
            <table class="ops-table">
                <thead><tr><th scope="col">Receipt</th><th scope="col">Student</th><th scope="col">Date</th><th scope="col">Method</th><th scope="col">Amount</th></tr></thead>
                <tbody>
                    @forelse ($payments as $payment)
                        <tr>
                            <td>{{ $payment->receipt_number }}</td>
                            <td>{{ $payment->student?->fullName() ?? 'Student unavailable' }}</td>
                            <td>{{ optional($payment->payment_date)->format('M j, Y') ?? 'Not set' }}</td>
                            <td>{{ str($payment->payment_method)->headline() }}</td>
                            <td>{{ number_format((float) $payment->amount_paid, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="ops-empty"><strong>No payment records</strong><span>There are no payments to include in this report.</span></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
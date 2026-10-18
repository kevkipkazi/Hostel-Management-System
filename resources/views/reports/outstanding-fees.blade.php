@extends('layouts.app')

@section('title', 'Outstanding fees | '.config('app.name', 'Hostel Management System'))

@section('content')
<div class="ops-page">
    <header class="ops-heading">
        <div>
            <p class="ops-eyebrow">Finance / Reports</p>
            <h1>Outstanding fees</h1>
            <p>Compare active accommodation period fees with payments recorded.</p>
        </div>
        <a class="ops-primary-link" href="{{ route('reports.payments') }}">Payment report</a>
    </header>

    <section class="ops-panel" aria-label="Outstanding fees by student">
        <div class="ops-panel-heading"><div><h2>Student balances</h2><p>Active allocations and recorded payment totals.</p></div></div>
        <div class="ops-table-scroll">
            <table class="ops-table">
                <thead><tr><th scope="col">Student</th><th scope="col">Admission number</th><th scope="col">Period fees</th><th scope="col">Payments received</th><th scope="col">Outstanding</th></tr></thead>
                <tbody>
                    @forelse ($students as $student)
                        @php
                            $periodFees = $student->allocations
                                ->where('status', 'active')
                                ->sum(fn ($allocation) => (float) ($allocation->room?->period_fee ?? 0));
                            $paymentsReceived = $student->payments
                                ->sum(fn ($payment) => (float) $payment->amount_paid);
                            $outstanding = max(0, $periodFees - $paymentsReceived);
                        @endphp
                        <tr>
                            <td>{{ $student->fullName() }}</td>
                            <td>{{ $student->admission_number }}</td>
                            <td>{{ number_format($periodFees, 2) }}</td>
                            <td>{{ number_format($paymentsReceived, 2) }}</td>
                            <td>{{ number_format($outstanding, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="ops-empty"><strong>No student balances</strong><span>There are no students to include in this report.</span></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
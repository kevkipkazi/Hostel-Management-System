@extends('layouts.app')

@section('title', 'Payments | '.config('app.name', 'Hostel Management System'))

@section('content')
<div class="ops-page">
    <header class="ops-heading">
        <div>
            <p class="ops-eyebrow">Finance / Transactions</p>
            <h1>Payments</h1>
            <p>Review recorded payments and issue receipts.</p>
        </div>
        <a class="ops-primary-link" href="{{ route('payments.create') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M5 12h14" /></svg>
            <span>Record payment</span>
        </a>
    </header>

    @if (session('success'))
        <div class="ops-alert" role="status">{{ session('success') }}</div>
    @endif

    <section class="ops-panel" aria-label="Payment records">
        <form method="GET" action="{{ route('payments.index') }}" class="payment-filters">
            <label class="payment-filter" for="payment-search">
                <span>Search payments</span>
                <input id="payment-search" type="search" name="search" value="{{ request('search') }}" placeholder="Student or receipt number">
            </label>
            <div class="payment-filter-actions">
                <button class="ops-primary-link" type="submit">Search</button>
                @if (request()->query())
                    <a class="payment-reset-link" href="{{ route('payments.index') }}">Clear</a>
                @endif
            </div>
        </form>

        <div class="ops-panel-heading">
            <div><h2>Payment records</h2><p>{{ number_format($payments->total()) }} {{ str('payment')->plural($payments->total()) }}</p></div>
            <span class="ops-page-count">Page {{ $payments->currentPage() }} of {{ max(1, $payments->lastPage()) }}</span>
        </div>

        <div class="ops-table-scroll">
            <table class="ops-table ops-mobile-table payment-mobile-table">
                <thead><tr><th scope="col">Receipt</th><th scope="col">Student</th><th scope="col">Amount</th><th scope="col">Date</th><th scope="col">Method</th><th scope="col"><span class="visually-hidden">Actions</span></th></tr></thead>
                <tbody>
                    @forelse ($payments as $payment)
                        <tr>
                            <td data-label="Receipt"><span class="payment-receipt-code">{{ $payment->receipt_number }}</span></td>
                            <td data-label="Student">
                                <span class="ops-person">
                                    <span class="ops-avatar" aria-hidden="true">{{ strtoupper(substr($payment->student?->first_name ?? 'S', 0, 1).substr($payment->student?->last_name ?? '', 0, 1)) }}</span>
                                    <span class="ops-copy"><strong>{{ $payment->student?->fullName() ?? 'Student unavailable' }}</strong><small>{{ $payment->student?->admission_number ?? 'No admission number' }}</small></span>
                                </span>
                            </td>
                            <td data-label="Amount"><strong class="payment-amount">{{ number_format((float) $payment->amount_paid, 2) }}</strong></td>
                            <td data-label="Date">{{ optional($payment->payment_date)->format('M j, Y') ?? 'Not set' }}</td>
                            <td data-label="Method">{{ str($payment->payment_method)->headline() }}</td>
                            <td data-label="Actions"><a class="ops-icon-link" href="{{ route('payments.show', $payment) }}" aria-label="View receipt {{ $payment->receipt_number }}" title="View receipt"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3" /></svg></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="ops-empty"><strong>No payments found</strong><span>Try a different search or record a payment to get started.</span></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($payments->hasPages())
            <footer class="ops-panel-footer"><span>Showing {{ $payments->firstItem() }}–{{ $payments->lastItem() }} of {{ number_format($payments->total()) }}</span><div class="ops-pagination">{{ $payments->links() }}</div></footer>
        @endif
    </section>
</div>
@endsection

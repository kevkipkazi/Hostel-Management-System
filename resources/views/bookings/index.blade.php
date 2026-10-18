@extends('layouts.app')

@section('title', 'Bookings | '.config('app.name', 'Hostel Management System'))

@section('content')
<div class="ops-page">
    <header class="ops-heading">
        <div>
            <p class="ops-eyebrow">Accommodation / Requests</p>
            <h1>Bookings</h1>
            <p>Review room requests and track their stay dates.</p>
        </div>
        <a class="ops-primary-link" href="{{ route('bookings.create') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M5 12h14" /></svg>
            <span>New booking</span>
        </a>
    </header>

    @if (session('success'))
        <div class="ops-alert" role="status">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="booking-alert-error" role="alert">{{ session('error') }}</div>
    @endif

    <section class="ops-panel" aria-label="Booking requests">
        <form method="GET" action="{{ route('bookings.index') }}" class="booking-filters">
            <label class="booking-filter booking-search-filter" for="booking-search">
                <span>Search student</span>
                <input id="booking-search" type="search" name="search" value="{{ request('search') }}" placeholder="Name or admission number">
            </label>
            <label class="booking-filter" for="booking-status">
                <span>Status</span>
                <select id="booking-status" name="status">
                    <option value="">All statuses</option>
                    <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                    <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                    <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
                    <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelled</option>
                </select>
            </label>
            <div class="booking-filter-actions">
                <button class="ops-primary-link" type="submit">Apply filters</button>
                @if (request()->query())
                    <a class="booking-reset-link" href="{{ route('bookings.index') }}">Clear</a>
                @endif
            </div>
        </form>

        <div class="ops-panel-heading">
            <div><h2>Booking requests</h2><p>{{ number_format($bookings->total()) }} {{ str('request')->plural($bookings->total()) }}</p></div>
            <span class="ops-page-count">Page {{ $bookings->currentPage() }} of {{ max(1, $bookings->lastPage()) }}</span>
        </div>

        <div class="ops-table-scroll">
            <table class="ops-table ops-mobile-table booking-mobile-table">
                <thead><tr><th scope="col">Student</th><th scope="col">Accommodation period</th><th scope="col">Room / hostel</th><th scope="col">Status</th><th scope="col">Processed by</th><th scope="col"><span class="visually-hidden">Actions</span></th></tr></thead>
                <tbody>
                    @forelse ($bookings as $booking)
                        <tr>
                            <td data-label="Student">
                                <span class="ops-person">
                                    <span class="ops-avatar" aria-hidden="true">{{ strtoupper(substr($booking->student?->first_name ?? 'S', 0, 1).substr($booking->student?->last_name ?? '', 0, 1)) }}</span>
                                    <span class="ops-copy"><strong>{{ $booking->student?->fullName() ?? 'Student unavailable' }}</strong><small>{{ $booking->student?->admission_number ?? 'No admission number' }}</small></span>
                                </span>
                            </td>
                            <td data-label="Accommodation period"><strong>{{ $booking->preferredHostel?->booking_period_name ?? 'Period unavailable' }}</strong><br>{{ optional($booking->requested_check_in_date)->format('M j, Y') ?? 'Not set' }} – {{ optional($booking->requested_check_out_date)->format('M j, Y') ?? 'Not set' }}</td>
                            <td data-label="Room / hostel">{{ $booking->room?->room_number ? 'Room '.$booking->room->room_number.' · '.($booking->room->hostel?->hostel_name ?? 'Hostel unavailable') : ($booking->preferredHostel?->hostel_name ?? 'Room not assigned') }}</td>
                            <td data-label="Status"><span class="ops-status status-{{ str($booking->status)->slug() }}"><span></span>{{ str($booking->status)->headline() }}</span></td>
                            <td data-label="Processed by">{{ $booking->processor?->name ?? 'Not recorded' }}</td>
                            <td data-label="Actions"><a class="ops-icon-link" href="{{ route('bookings.show', $booking) }}" aria-label="View booking for {{ $booking->student?->fullName() ?? 'student' }}" title="View booking"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3" /></svg></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="ops-empty"><strong>No booking requests found</strong><span>Try changing the filters or create a new booking request.</span></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($bookings->hasPages())
            <footer class="ops-panel-footer"><span>Showing {{ $bookings->firstItem() }}–{{ $bookings->lastItem() }} of {{ number_format($bookings->total()) }}</span><div class="ops-pagination">{{ $bookings->links() }}</div></footer>
        @endif
    </section>
</div>
@endsection

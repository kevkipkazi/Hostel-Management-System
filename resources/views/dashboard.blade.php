@extends('layouts.app')

@section('title', 'Dashboard | '.config('app.name', 'Hostel Management System'))

@section('content')
	@php
		$capacity = $occupiedRooms + $totalAvailableSpaces;
		$occupancyRate = $capacity > 0 ? (int) round(($occupiedRooms / $capacity) * 100) : 0;
	@endphp

	<div class="dashboard-page">
		<header class="dashboard-heading">
			<div>
				<p class="dashboard-eyebrow">{{ $roleLabel }} workspace</p>
				<h1>{{ $role === 'student' ? 'Welcome back' : 'Dashboard' }}</h1>
				<p class="dashboard-subtitle">
					{{ $role === 'student' ? 'Your accommodation details and latest updates.' : 'A clear view of your hostel operations.' }}
				</p>
			</div>
			<div class="dashboard-date">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M16 2v4M8 2v4M3 9h18"/></svg>
				<span>{{ now()->format('D, M j, Y') }}</span>
			</div>
		</header>

		@if ($role === 'student')
			<section class="student-welcome">
				<div>
					<p class="dashboard-eyebrow">Student account</p>
					<h2>{{ $user?->name ?? 'Your account' }}</h2>
					@if ($currentAllocation)
						<p>Your current room is <strong>{{ $currentAllocation->room?->room_number ?? 'assigned' }}</strong>
							@if ($currentAllocation->room?->hostel)
								in <strong>{{ $currentAllocation->room->hostel->hostel_name }}</strong>
							@endif
						</p>
					@elseif (! $studentProfile)
						<p>Your account is not linked to a student profile yet. Contact your administrator for help.</p>
					@else
						<p>You do not have an active room allocation.</p>
					@endif
				</div>
				<div class="student-welcome-actions">
					@if ($studentProfile && $studentProfile->status === 'active')
						<a class="button button-primary" href="{{ route('bookings.create') }}">Book a hostel</a>
					@endif
					<a class="button button-secondary" href="{{ route('profile.edit') }}">View profile</a>
				</div>
			</section>

			<section class="dashboard-panel student-bookings">
				<div class="panel-heading">
					<div><p class="dashboard-eyebrow">Your requests</p><h2>Recent bookings</h2></div>
					<span class="panel-count">{{ $studentBookings->count() }}</span>
				</div>
				@forelse ($studentBookings as $booking)
					<div class="booking-row">
						<div class="booking-symbol" aria-hidden="true">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M16 2v4M8 2v4M3 9h18"/></svg>
						</div>
						<div class="booking-details"><strong>{{ $booking->preferredHostel?->hostel_name ?? 'Hostel request' }}</strong><span>{{ optional($booking->booking_date)->format('M j, Y') ?? 'Date not set' }}</span></div>
						<span class="status-pill status-{{ str($booking->status)->lower()->replace(' ', '-') }}">{{ str($booking->status)->headline() }}</span>
					</div>
				@empty
					<div class="empty-state">No booking requests yet.</div>
				@endforelse
			</section>
		@elseif ($role === 'finance_officer')
			<section class="stat-grid" aria-label="Finance summary">
				<article class="stat-card">
					<div class="stat-top"><span class="stat-label">Payments received</span><span class="stat-icon stat-icon-blue" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2.5" y="5" width="19" height="14" rx="2"/><path d="M2.5 10h19M7 15h3"/></svg></span></div>
					<strong class="stat-value">{{ number_format((float) $paymentsReceived, 2) }}</strong>
					<span class="stat-caption">Recorded payments</span>
				</article>
				<article class="stat-card">
					<div class="stat-top"><span class="stat-label">Transactions</span><span class="stat-icon stat-icon-green" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg></span></div>
					<strong class="stat-value">{{ number_format($paymentCount) }}</strong>
					<span class="stat-caption">Across all students</span>
				</article>
				<article class="stat-card">
					<div class="stat-top"><span class="stat-label">Outstanding fees</span><span class="stat-icon stat-icon-orange" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></span></div>
					<strong class="stat-value">{{ number_format((float) $outstandingFees, 2) }}</strong>
					<span class="stat-caption">Based on active allocations</span>
				</article>
			</section>

			<section class="dashboard-panel finance-panel finance-dashboard-panel">
				<div class="panel-heading">
					<div><p class="dashboard-eyebrow">Finance activity</p><h2>Recent payments</h2></div>
					<a class="panel-link" href="{{ route('payments.index') }}">All payments <span aria-hidden="true">&rarr;</span></a>
				</div>
				@forelse ($recentPayments as $payment)
					<div class="payment-row">
						<span class="payment-receipt" aria-hidden="true">{{ strtoupper(substr($payment->student?->first_name ?? 'P', 0, 1)) }}</span>
						<span class="payment-details"><strong>{{ $payment->student?->fullName() ?? 'Student record unavailable' }}</strong><span>{{ $payment->receipt_number ?: optional($payment->payment_date)->format('M j, Y') }}</span></span>
						<strong class="payment-amount">{{ number_format((float) $payment->amount_paid, 2) }}</strong>
					</div>
				@empty
					<div class="empty-state">No payments to show yet.</div>
				@endforelse
			</section>

			<section class="quick-actions" aria-label="Finance actions">
				<div><p class="dashboard-eyebrow">Finance workspace</p><h2>Finance tasks</h2></div>
				<a class="button button-primary" href="{{ route('payments.create') }}">Record payment</a>
				<a class="button button-secondary" href="{{ route('reports.outstanding-fees') }}">Outstanding fees</a>
			</section>
		@else
			<section class="stat-grid" aria-label="Hostel summary">
				<article class="stat-card">
					<div class="stat-top"><span class="stat-label">Registered students</span><span class="stat-icon stat-icon-green" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c.5-3.2 2.7-5 6.5-5s6 1.8 6.5 5M16 5.2a3.5 3.5 0 0 1 0 6.6M17 15c2.6.6 4 2.2 4.5 5"/></svg></span></div>
					<strong class="stat-value">{{ number_format($students) }}</strong>
					<span class="stat-caption">Across all hostels</span>
				</article>
				<article class="stat-card">
					<div class="stat-top"><span class="stat-label">Hostels &amp; rooms</span><span class="stat-icon stat-icon-blue" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21V5l9-3 9 3v16M8 21v-5h8v5M7 8h.01M12 8h.01M17 8h.01M7 12h.01M12 12h.01M17 12h.01"/></svg></span></div>
					<strong class="stat-value">{{ number_format($hostels) }} <span class="stat-secondary">/ {{ number_format($rooms) }}</span></strong>
					<span class="stat-caption">Hostels / rooms</span>
				</article>
				<article class="stat-card">
					<div class="stat-top"><span class="stat-label">Bed occupancy</span><span class="stat-icon stat-icon-orange" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 20V8a2 2 0 0 1 2-2h4a3 3 0 0 1 3 3v3H5a2 2 0 0 0-2 2M3 16h18v4M12 12h7a2 2 0 0 1 2 2v2M7 20v2M18 20v2"/></svg></span></div>
					<strong class="stat-value">{{ number_format($occupiedRooms) }} <span class="stat-secondary">/ {{ number_format($capacity) }}</span></strong>
					<span class="stat-caption">{{ number_format($occupancyRate) }}% occupied · {{ number_format($totalAvailableSpaces) }} beds free</span>
					<div class="occupancy-track" role="progressbar" aria-label="Bed occupancy" aria-valuenow="{{ $occupancyRate }}" aria-valuemin="0" aria-valuemax="100"><span style="width: {{ $occupancyRate }}%"></span></div>
				</article>
				<article class="stat-card">
					<div class="stat-top"><span class="stat-label">Pending bookings</span><span class="stat-icon stat-icon-red" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></span></div>
					<strong class="stat-value">{{ number_format($pendingBookings) }}</strong>
					<span class="stat-caption">{{ number_format($activeAllocations) }} active allocations</span>
				</article>
			</section>

			<section class="dashboard-grid {{ $canManagePayments ? '' : 'dashboard-grid-single' }}">
				<article class="dashboard-panel booking-panel">
					<div class="panel-heading">
						<div><p class="dashboard-eyebrow">Latest activity</p><h2>Recent bookings</h2></div>
						<a class="panel-link" href="{{ route('bookings.index') }}">All bookings <span aria-hidden="true">&rarr;</span></a>
					</div>
					@forelse ($recentBookings as $booking)
						<a class="booking-row booking-row-link" href="{{ route('bookings.show', $booking) }}">
							<span class="booking-avatar" aria-hidden="true">{{ strtoupper(substr($booking->student?->first_name ?? 'S', 0, 1)) }}</span>
							<span class="booking-details"><strong>{{ $booking->student?->fullName() ?? 'Student record unavailable' }}</strong><span>{{ $booking->preferredHostel?->hostel_name ?? 'No preferred hostel' }} · {{ optional($booking->booking_date)->format('M j, Y') ?? 'Date not set' }}</span></span>
							<span class="status-pill status-{{ str($booking->status)->lower()->replace(' ', '-') }}">{{ str($booking->status)->headline() }}</span>
						</a>
					@empty
						<div class="empty-state">No bookings to show yet.</div>
					@endforelse
				</article>

				@if ($canManagePayments)
				<article class="dashboard-panel finance-panel">
					<div class="panel-heading">
						<div><p class="dashboard-eyebrow">Finance</p><h2>Fee overview</h2></div>
						<a class="panel-link" href="{{ route('payments.index') }}">Payments <span aria-hidden="true">&rarr;</span></a>
					</div>
					<div class="finance-total"><span>Total payments received</span><strong>{{ number_format((float) $paymentsReceived, 2) }}</strong></div>
					<div class="finance-breakdown">
						<div><span class="finance-dot dot-green"></span><span>Outstanding fees</span><strong>{{ number_format((float) $outstandingFees, 2) }}</strong></div>
						<div><span class="finance-dot dot-blue"></span><span>Active allocations</span><strong>{{ number_format($activeAllocations) }}</strong></div>
					</div>
					<div class="recent-payment-heading">Recent payments</div>
					@forelse ($recentPayments as $payment)
						<div class="payment-row">
							<span class="payment-receipt" aria-hidden="true">{{ strtoupper(substr($payment->student?->first_name ?? 'P', 0, 1)) }}</span>
							<span class="payment-details"><strong>{{ $payment->student?->fullName() ?? 'Student record unavailable' }}</strong><span>{{ $payment->receipt_number ?: optional($payment->payment_date)->format('M j, Y') }}</span></span>
							<strong class="payment-amount">{{ number_format((float) $payment->amount_paid, 2) }}</strong>
						</div>
					@empty
						<div class="empty-state">No payments to show yet.</div>
					@endforelse
				</article>
				@endif
			</section>

			<section class="quick-actions" aria-label="Quick actions">
				<div><p class="dashboard-eyebrow">Get started</p><h2>Quick actions</h2></div>
				<a class="button button-primary" href="{{ route('students.create') }}"><span aria-hidden="true">+</span> Add student</a>
				<a class="button button-secondary" href="{{ route('hostels.create') }}"><span aria-hidden="true">+</span> Add hostel</a>
				<a class="button button-secondary" href="{{ route('rooms.create') }}"><span aria-hidden="true">+</span> Add room</a>
			</section>
		@endif
	</div>
@endsection

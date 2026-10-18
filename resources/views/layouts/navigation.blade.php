@php
	$navigationRole = strtolower(auth()->user()?->role ?? 'student');
	$canManageHostels = auth()->user()?->can('manage-hostels') ?? false;
	$canManagePayments = auth()->user()?->can('manage-payments') ?? false;
@endphp

<aside class="app-sidebar">
	<a class="app-brand" href="{{ route('dashboard') }}">
		<span class="app-brand-mark" aria-hidden="true">
			<svg viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
				<path d="M5 27V9l11-6 11 6v18M11 27v-8h10v8M11 12h.01M16 12h.01M21 12h.01" />
			</svg>
		</span>
		<span class="app-brand-copy"><strong>Hostel</strong><span>Management</span></span>
	</a>

	<p class="nav-section-label">WORKSPACE</p>
	<nav class="sidebar-nav" aria-label="Main navigation">
		<a class="sidebar-link {{ request()->routeIs('dashboard*') ? 'is-active' : '' }}" href="{{ route('dashboard') }}" @if (request()->routeIs('dashboard*')) aria-current="page" @endif>
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="8" height="8" rx="1.5"/><rect x="13" y="3" width="8" height="5" rx="1.5"/><rect x="13" y="10" width="8" height="11" rx="1.5"/><rect x="3" y="13" width="8" height="8" rx="1.5"/></svg>
			<span>Overview</span>
		</a>

		@if ($canManageHostels)
			<a class="sidebar-link {{ request()->routeIs('students.*') ? 'is-active' : '' }}" href="{{ route('students.index') }}" @if (request()->routeIs('students.*')) aria-current="page" @endif>
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c.5-3.2 2.7-5 6.5-5s6 1.8 6.5 5M16 5.2a3.5 3.5 0 0 1 0 6.6M17 15c2.6.6 4 2.2 4.5 5"/></svg>
				<span>Students</span>
			</a>
			<a class="sidebar-link {{ request()->routeIs('hostels.*') ? 'is-active' : '' }}" href="{{ route('hostels.index') }}" @if (request()->routeIs('hostels.*')) aria-current="page" @endif>
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21V5l9-3 9 3v16M8 21v-5h8v5M7 8h.01M12 8h.01M17 8h.01M7 12h.01M12 12h.01M17 12h.01"/></svg>
				<span>Hostels &amp; rooms</span>
			</a>
		@endif

		@if ($canManageHostels || $navigationRole === 'student')
			<a class="sidebar-link {{ request()->routeIs('bookings.*') ? 'is-active' : '' }}" href="{{ route('bookings.index') }}" @if (request()->routeIs('bookings.*')) aria-current="page" @endif>
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M16 2v4M8 2v4M3 9h18M8 13h3M8 17h7"/></svg>
				<span>Bookings</span>
			</a>
		@endif

		@if ($canManageHostels)
			<a class="sidebar-link {{ request()->routeIs('allocations.*') ? 'is-active' : '' }}" href="{{ route('allocations.index') }}" @if (request()->routeIs('allocations.*')) aria-current="page" @endif>
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4h7v7H4zM13 4h7v7h-7zM4 13h7v7H4zM16.5 13v7M13 16.5h7"/></svg>
				<span>Allocations</span>
			</a>
		@endif

		@if ($canManagePayments)
			<a class="sidebar-link {{ request()->routeIs('payments.*') ? 'is-active' : '' }}" href="{{ route('payments.index') }}" @if (request()->routeIs('payments.*')) aria-current="page" @endif>
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2.5" y="5" width="19" height="14" rx="2"/><path d="M2.5 10h19M7 15h3"/></svg>
				<span>Payments</span>
			</a>
		@endif

		@if ($navigationRole === 'administrator')
			<a class="sidebar-link {{ request()->routeIs('reports.*') ? 'is-active' : '' }}" href="{{ route('reports.index') }}" @if (request()->routeIs('reports.*')) aria-current="page" @endif>
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 19V5M4 19h17M8 15l3-4 3 2 5-6"/><path d="M16 7h3v3"/></svg>
				<span>Reports</span>
			</a>
		@elseif ($navigationRole === 'hostel_manager')
			<a class="sidebar-link {{ request()->routeIs('reports.students') ? 'is-active' : '' }}" href="{{ route('reports.students') }}" @if (request()->routeIs('reports.students')) aria-current="page" @endif>
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c.5-3.2 2.7-5 6.5-5s6 1.8 6.5 5M17 8h5M19.5 5.5v5"/></svg>
				<span>Student report</span>
			</a>
			<a class="sidebar-link {{ request()->routeIs('reports.rooms') ? 'is-active' : '' }}" href="{{ route('reports.rooms') }}" @if (request()->routeIs('reports.rooms')) aria-current="page" @endif>
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21V5l9-3 9 3v16M8 21v-5h8v5M7 8h.01M12 8h.01M17 8h.01"/></svg>
				<span>Room report</span>
			</a>
		@elseif ($navigationRole === 'finance_officer')
			<a class="sidebar-link {{ request()->routeIs('reports.payments') ? 'is-active' : '' }}" href="{{ route('reports.payments') }}" @if (request()->routeIs('reports.payments')) aria-current="page" @endif>
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 19V5M4 19h17M8 15l3-4 3 2 5-6"/></svg>
				<span>Payment report</span>
			</a>
			<a class="sidebar-link {{ request()->routeIs('reports.outstanding-fees') ? 'is-active' : '' }}" href="{{ route('reports.outstanding-fees') }}" @if (request()->routeIs('reports.outstanding-fees')) aria-current="page" @endif>
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
				<span>Outstanding fees</span>
			</a>
		@endif

		<a class="sidebar-link {{ request()->routeIs('profile.*') ? 'is-active' : '' }}" href="{{ route('profile.edit') }}" @if (request()->routeIs('profile.*')) aria-current="page" @endif>
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="3.5"/><path d="M4 21c.7-4.1 3.3-6.2 8-6.2s7.3 2.1 8 6.2"/></svg>
			<span>My profile</span>
		</a>
	</nav>

	<div class="sidebar-note">
		<span class="sidebar-note-mark" aria-hidden="true">i</span>
		<span><strong>Need a hand?</strong><small>Contact your system administrator.</small></span>
	</div>
</aside>

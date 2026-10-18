@extends('layouts.app')

@section('title', 'Hostels | '.config('app.name', 'Hostel Management System'))

@section('content')
<div class="hostels-page">
    <header class="hostels-heading">
        <div>
            <p class="hostels-eyebrow">Accommodation / Directory</p>
            <h1>Hostels</h1>
            <p class="hostels-subtitle">Manage residences, locations, and availability.</p>
        </div>
        <a class="hostels-add-button" href="{{ route('hostels.create') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M5 12h14" /></svg>
            <span>Add hostel</span>
        </a>
    </header>

    @if (session('success'))
        <div class="hostels-alert hostels-alert-success" role="status">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="hostels-alert hostels-alert-error" role="alert">{{ session('error') }}</div>
    @endif

    <section class="hostels-directory" aria-label="Hostel directory">
        <form method="GET" action="{{ route('hostels.index') }}" class="hostels-filters">
            <label class="hostel-filter hostel-search-filter" for="hostel-search">
                <span>Search</span>
                <span class="hostel-search-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 5 5" /></svg>
                    <input id="hostel-search" type="search" name="search" value="{{ request('search') }}" placeholder="Name, code, or location">
                </span>
            </label>
            <label class="hostel-filter" for="hostel-status">
                <span>Status</span>
                <select id="hostel-status" name="status">
                    <option value="">All statuses</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                </select>
            </label>
            <div class="hostel-filter-actions">
                <button class="hostels-filter-button" type="submit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 5h16l-6.5 7.2V19l-3 1v-7.8L4 5z" /></svg>
                    <span>Apply filters</span>
                </button>
                @if (request()->query())
                    <a class="hostels-reset-link" href="{{ route('hostels.index') }}">Clear</a>
                @endif
            </div>
        </form>

        <div class="hostels-table-heading">
            <div>
                <h2>Hostel directory</h2>
                <p>{{ number_format($hostels->total()) }} {{ str('hostel')->plural($hostels->total()) }} found</p>
            </div>
            <span class="hostels-page-count">Page {{ $hostels->currentPage() }} of {{ max(1, $hostels->lastPage()) }}</span>
        </div>

        <div class="hostels-table-scroll">
            <table class="hostels-table">
                <thead>
                    <tr>
                        <th scope="col">Hostel</th>
                        <th scope="col">Code</th>
                        <th scope="col">Location</th>
                        <th scope="col">Category</th>
                        <th scope="col">Accommodation period</th>
                        <th scope="col">Rooms</th>
                        <th scope="col">Period fee / bed</th>
                        <th scope="col">Status</th>
                        <th class="hostel-actions-heading" scope="col"><span class="visually-hidden">Actions</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($hostels as $hostel)
                        <tr>
                            <td>
                                <div class="hostel-identity">
                                    <span class="hostel-mark" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21V5l9-3 9 3v16M8 21v-5h8v5M7 8h.01M12 8h.01M17 8h.01M7 12h.01M12 12h.01M17 12h.01" /></svg>
                                    </span>
                                    <span class="hostel-identity-copy"><strong>{{ $hostel->hostel_name }}</strong><span>{{ str($hostel->gender_category)->headline() }} residence</span></span>
                                </div>
                            </td>
                            <td><span class="hostel-code">{{ $hostel->hostel_code }}</span></td>
                            <td><span class="hostel-location"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5" /></svg>{{ $hostel->location }}</span></td>
                            <td><span class="hostel-category">{{ str($hostel->gender_category)->headline() }}</span></td>
                            <td>{{ $hostel->booking_period_name ?: 'Not configured' }}</td>
                            <td>{{ number_format($hostel->rooms_count) }}</td>
                            <td>{{ number_format((float) $hostel->period_fee, 2) }}</td>
                            <td><span class="hostel-status status-{{ str($hostel->status)->slug() }}"><span></span>{{ str($hostel->status)->headline() }}</span></td>
                            <td>
                                <div class="hostel-row-actions">
                                    <a class="hostel-icon-action" href="{{ route('hostels.show', $hostel) }}" aria-label="View {{ $hostel->hostel_name }}" title="View hostel">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3" /></svg>
                                    </a>
                                    <a class="hostel-icon-action" href="{{ route('hostels.edit', $hostel) }}" aria-label="Edit {{ $hostel->hostel_name }}" title="Edit hostel">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z" /></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="hostels-empty-state">
                                    <span class="hostels-empty-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21V5l9-3 9 3v16M8 21v-5h8v5M7 8h.01M12 8h.01M17 8h.01"/></svg>
                                    </span>
                                    <strong>No hostels found</strong>
                                    <span>Try another search or add a hostel to get started.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($hostels->hasPages())
            <footer class="hostels-table-footer">
                <span>Showing {{ $hostels->firstItem() }}–{{ $hostels->lastItem() }} of {{ number_format($hostels->total()) }}</span>
                <div class="hostels-pagination">{{ $hostels->links() }}</div>
            </footer>
        @endif
    </section>
</div>
@endsection

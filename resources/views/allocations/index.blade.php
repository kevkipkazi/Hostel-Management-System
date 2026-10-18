@extends('layouts.app')

@section('title', 'Allocations | '.config('app.name', 'Hostel Management System'))

@section('content')
<div class="ops-page">
    <header class="ops-heading">
        <div>
            <p class="ops-eyebrow">Housing / Placements</p>
            <h1>Room allocations</h1>
            <p>Track student placements and check-in dates.</p>
        </div>
        <a class="ops-primary-link" href="{{ route('allocations.create') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M5 12h14" /></svg>
            <span>Allocate room</span>
        </a>
    </header>

    @if (session('success'))
        <div class="ops-alert" role="status">{{ session('success') }}</div>
    @endif

    <section class="ops-panel" aria-label="Room allocations">
        <div class="ops-panel-heading">
            <div><h2>Allocations</h2><p>{{ number_format($allocations->total()) }} {{ str('record')->plural($allocations->total()) }}</p></div>
            <span class="ops-page-count">Page {{ $allocations->currentPage() }} of {{ max(1, $allocations->lastPage()) }}</span>
        </div>

        <div class="ops-table-scroll">
            <table class="ops-table ops-mobile-table">
                <thead><tr><th scope="col">Student</th><th scope="col">Room / hostel</th><th scope="col">Check-in</th><th scope="col">Expected out</th><th scope="col">Status</th><th scope="col"><span class="visually-hidden">Actions</span></th></tr></thead>
                <tbody>
                    @forelse ($allocations as $allocation)
                        <tr>
                            <td data-label="Student">
                                <span class="ops-person">
                                    <span class="ops-avatar" aria-hidden="true">{{ strtoupper(substr($allocation->student?->first_name ?? 'S', 0, 1).substr($allocation->student?->last_name ?? '', 0, 1)) }}</span>
                                    <span class="ops-copy"><strong>{{ $allocation->student?->fullName() ?? 'Student unavailable' }}</strong><small>{{ $allocation->student?->admission_number ?? 'No admission number' }}</small></span>
                                </span>
                            </td>
                            <td data-label="Room"><span class="ops-copy"><strong>{{ $allocation->room?->room_number ?? 'Room unavailable' }}</strong><small>{{ $allocation->room?->hostel?->hostel_name ?? 'Hostel unavailable' }}</small></span></td>
                            <td data-label="Check-in">{{ optional($allocation->check_in_date)->format('M j, Y') ?? 'Not set' }}</td>
                            <td data-label="Expected out">{{ optional($allocation->expected_check_out_date)->format('M j, Y') ?? 'Open-ended' }}</td>
                            <td data-label="Status"><span class="ops-status status-{{ str($allocation->status)->slug() }}"><span></span>{{ str($allocation->status)->headline() }}</span></td>
                            <td data-label="Actions"><a class="ops-icon-link" href="{{ route('allocations.show', $allocation) }}" aria-label="View allocation for {{ $allocation->student?->fullName() ?? 'student' }}" title="View allocation"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3" /></svg></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="ops-empty"><strong>No allocations yet</strong><span>Create an allocation to assign a student to a room.</span></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($allocations->hasPages())
            <footer class="ops-panel-footer"><span>Showing {{ $allocations->firstItem() }}–{{ $allocations->lastItem() }} of {{ number_format($allocations->total()) }}</span><div class="ops-pagination">{{ $allocations->links() }}</div></footer>
        @endif
    </section>
</div>
@endsection

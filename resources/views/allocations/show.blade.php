@extends('layouts.app')

@section('title', 'Allocation details | '.config('app.name', 'Hostel Management System'))

@section('content')
<div class="ops-page ops-detail-page">
    <header class="ops-heading">
        <div>
            <a class="ops-back-link" href="{{ route('allocations.index') }}"><span aria-hidden="true">&larr;</span> Allocations</a>
            <p class="ops-eyebrow">Housing / Placement details</p>
            <div class="ops-title-row"><h1>{{ $allocation->student?->fullName() ?? 'Allocation details' }}</h1><span class="ops-status status-{{ str($allocation->status)->slug() }}"><span></span>{{ str($allocation->status)->headline() }}</span></div>
            <p>Room {{ $allocation->room?->room_number ?? 'unassigned' }} <span aria-hidden="true">·</span> {{ $allocation->room?->hostel?->hostel_name ?? 'Hostel unavailable' }}</p>
        </div>
    </header>

    <section class="ops-panel" aria-label="Allocation information">
        <div class="ops-panel-heading"><div><h2>Stay details</h2><p>Student and room assignment information.</p></div></div>
        <dl class="ops-facts-grid">
            <div><dt>Student</dt><dd>{{ $allocation->student?->fullName() ?? 'Unavailable' }}</dd><small>{{ $allocation->student?->admission_number ?? 'No admission number' }}</small></div>
            <div><dt>Room</dt><dd>{{ $allocation->room?->room_number ?? 'Unavailable' }}</dd><small>{{ $allocation->room?->hostel?->hostel_name ?? 'Hostel unavailable' }}</small></div>
            <div><dt>Allocation date</dt><dd>{{ optional($allocation->allocation_date)->format('M j, Y') ?? 'Not set' }}</dd></div>
            <div><dt>Check-in date</dt><dd>{{ optional($allocation->check_in_date)->format('M j, Y') ?? 'Not set' }}</dd></div>
            <div><dt>Expected check-out</dt><dd>{{ optional($allocation->expected_check_out_date)->format('M j, Y') ?? 'Open-ended' }}</dd></div>
            <div><dt>Allocated by</dt><dd>{{ $allocation->allocatedBy?->name ?? 'Not recorded' }}</dd></div>
            @if ($allocation->booking)
                <div><dt>Booking request</dt><dd>#{{ $allocation->booking->id }}</dd><small>{{ str($allocation->booking->status)->headline() }}</small></div>
            @endif
        </dl>
    </section>

    @if ($allocation->status === 'active')
        <form method="POST" action="{{ route('allocations.destroy', $allocation) }}" class="ops-checkout-form" onsubmit="return confirm('Record check-out for this student?')">
            @csrf
            @method('DELETE')
            <span><strong>Ready to close this stay?</strong><small>Recording check-out will mark this allocation as completed.</small></span>
            <button class="ops-danger-button" type="submit">Record check-out</button>
        </form>
    @endif
</div>
@endsection

@extends('layouts.app')

@section('title', $hostel->hostel_name.' | '.config('app.name', 'Hostel Management System'))

@section('content')
<div class="hostel-detail-page">
    <header class="hostel-detail-heading">
        <div>
            <a class="hostel-back-link" href="{{ route('hostels.index') }}"><span aria-hidden="true">&larr;</span> Hostels</a>
            <p class="hostels-eyebrow">Accommodation / Hostel profile</p>
            <div class="hostel-detail-title-row">
                <h1>{{ $hostel->hostel_name }}</h1>
                <span class="hostel-status status-{{ str($hostel->status)->slug() }}"><span></span>{{ str($hostel->status)->headline() }}</span>
            </div>
            <p class="hostels-subtitle">{{ $hostel->hostel_code }} <span aria-hidden="true">·</span> {{ $hostel->location }}</p>
        </div>
        <a class="hostels-add-button" href="{{ route('hostels.edit', $hostel) }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z" /></svg>
            <span>Edit hostel</span>
        </a>
    </header>

    <section class="hostel-overview-panel" aria-label="Hostel information">
        <div class="hostel-overview-title">
            <span class="hostel-mark" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21V5l9-3 9 3v16M8 21v-5h8v5M7 8h.01M12 8h.01M17 8h.01M7 12h.01M12 12h.01M17 12h.01" /></svg></span>
            <span><strong>Hostel overview</strong><small>Residence details and room inventory.</small></span>
        </div>
        <dl class="hostel-facts-grid">
            <div><dt>Hostel code</dt><dd>{{ $hostel->hostel_code }}</dd></div>
            <div><dt>Location</dt><dd>{{ $hostel->location }}</dd></div>
            <div><dt>Gender category</dt><dd>{{ str($hostel->gender_category)->headline() }}</dd></div>
            <div><dt>Accommodation period</dt><dd>{{ $hostel->booking_period_name ?: 'Not configured' }}</dd></div>
            <div><dt>Period dates</dt><dd>{{ optional($hostel->booking_period_start)->format('M j, Y') }} – {{ optional($hostel->booking_period_end)->format('M j, Y') }}</dd></div>
            <div><dt>Rooms</dt><dd>{{ number_format($hostel->rooms->count()) }}</dd></div>
            <div><dt>Total beds</dt><dd>{{ number_format($hostel->rooms->sum('capacity')) }}</dd></div>
            <div><dt>Period fee per bed</dt><dd>{{ number_format((float) $hostel->period_fee, 2) }}</dd></div>
        </dl>
        @if ($hostel->description)
            <div class="hostel-description-block"><span>Description</span><p>{{ $hostel->description }}</p></div>
        @endif
    </section>

    <section class="hostel-rooms-panel" aria-label="Rooms in this hostel">
        <div class="hostel-rooms-heading">
            <div><p class="hostels-eyebrow">Inventory</p><h2>Rooms</h2><p>{{ number_format($hostel->rooms->count()) }} {{ str('room')->plural($hostel->rooms->count()) }} in this residence</p></div>
            <a class="hostel-add-room-link" href="{{ route('rooms.create', ['hostel_id' => $hostel->id]) }}"><span aria-hidden="true">+</span> Add room</a>
        </div>

        <div class="hostel-room-table-scroll">
            <table class="hostel-room-table">
                <thead><tr><th scope="col">Room</th><th scope="col">Beds booked</th><th scope="col">Capacity</th><th scope="col">Fee per bed / period</th><th scope="col">Status</th></tr></thead>
                <tbody>
                    @forelse ($hostel->rooms as $room)
                        <tr>
                            <td><strong>{{ $room->room_number }}</strong></td>
                            <td>{{ $room->bookedSpaces() }}</td>
                            <td>{{ number_format($room->capacity) }} {{ str('bed')->plural($room->capacity) }}</td>
                            <td>{{ number_format((float) $room->period_fee, 2) }}</td>
                            <td><span class="hostel-room-status room-status-{{ str($room->availabilityStatus())->slug() }}"><span></span>{{ str($room->availabilityStatus())->headline() }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="hostel-room-empty"><strong>No rooms yet</strong><span>Add the first room to this residence.</span></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection

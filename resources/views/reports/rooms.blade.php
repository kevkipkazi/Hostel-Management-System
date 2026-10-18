@extends('layouts.app')

@section('title', 'Room report | '.config('app.name', 'Hostel Management System'))

@section('content')
<div class="ops-page">
    <header class="ops-heading">
        <div>
            <p class="ops-eyebrow">Hostel operations / Reports</p>
            <h1>Room report</h1>
            <p>Room capacity, rates, and availability.</p>
        </div>
        <a class="ops-primary-link" href="{{ route('reports.students') }}">Student report</a>
    </header>

    <section class="ops-panel" aria-label="Room report">
        <div class="ops-panel-heading"><div><h2>Rooms</h2><p>{{ number_format($rooms->count()) }} records</p></div></div>
        <div class="ops-table-scroll">
            <table class="ops-table">
                <thead><tr><th scope="col">Hostel</th><th scope="col">Room</th><th scope="col">Capacity</th><th scope="col">Fee per bed / period</th><th scope="col">Status</th></tr></thead>
                <tbody>
                    @forelse ($rooms as $room)
                        <tr>
                            <td>{{ $room->hostel?->hostel_name ?? 'Hostel unavailable' }}</td>
                            <td>{{ $room->room_number }}</td>
                            <td>{{ number_format($room->capacity) }}</td>
                            <td>{{ number_format((float) $room->period_fee, 2) }}</td>
                            <td>{{ str($room->status)->headline() }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="ops-empty"><strong>No room records</strong><span>There are no rooms to include in this report.</span></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="page-title">Reports</div>

<form method="GET" class="row g-2 mb-4">
    <div class="col-md-3"><input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control"></div>
    <div class="col-md-3"><input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control"></div>
    <div class="col-md-2"><button class="btn btn-primary w-100">Apply filters</button></div>
</form>

<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card-stat"><div class="text-muted">Students</div><h4>{{ $studentCount }}</h4></div></div>
    <div class="col-md-3"><div class="card-stat"><div class="text-muted">Hostels</div><h4>{{ $hostelCount }}</h4></div></div>
    <div class="col-md-3"><div class="card-stat"><div class="text-muted">Rooms</div><h4>{{ $roomCount }}</h4></div></div>
    <div class="col-md-3"><div class="card-stat"><div class="text-muted">Total payments</div><h4>{{ number_format($paymentTotal, 2) }}</h4></div></div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white"><strong>Students</strong></div>
            <div class="card-body p-0">
                <table class="table mb-0"><thead><tr><th>Name</th><th>Course</th></tr></thead><tbody>@foreach($students as $student)<tr><td>{{ $student->fullName() }}</td><td>{{ $student->course }}</td></tr>@endforeach</tbody></table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white"><strong>Payments</strong></div>
            <div class="card-body p-0">
                <table class="table mb-0"><thead><tr><th>Receipt</th><th>Amount</th></tr></thead><tbody>@foreach($paymentsList as $payment)<tr><td>{{ $payment->receipt_number }}</td><td>{{ number_format($payment->amount_paid, 2) }}</td></tr>@endforeach</tbody></table>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Student report | '.config('app.name', 'Hostel Management System'))

@section('content')
<div class="ops-page">
    <header class="ops-heading">
        <div>
            <p class="ops-eyebrow">Hostel operations / Reports</p>
            <h1>Student report</h1>
            <p>Enrollment records and student status.</p>
        </div>
        <a class="ops-primary-link" href="{{ route('reports.rooms') }}">Room report</a>
    </header>

    <section class="ops-panel" aria-label="Student report">
        <div class="ops-panel-heading"><div><h2>Students</h2><p>{{ number_format($students->count()) }} records</p></div></div>
        <div class="ops-table-scroll">
            <table class="ops-table">
                <thead><tr><th scope="col">Admission number</th><th scope="col">Student</th><th scope="col">Course</th><th scope="col">Department</th><th scope="col">Status</th></tr></thead>
                <tbody>
                    @forelse ($students as $student)
                        <tr>
                            <td>{{ $student->admission_number }}</td>
                            <td>{{ $student->fullName() }}</td>
                            <td>{{ $student->course }}</td>
                            <td>{{ $student->department ?: 'Not set' }}</td>
                            <td>{{ str($student->status)->headline() }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="ops-empty"><strong>No student records</strong><span>There are no students to include in this report.</span></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
@extends('layouts.app')

@section('title', 'Students | '.config('app.name', 'Hostel Management System'))

@section('content')
<div class="students-page">
    <header class="students-heading">
        <div>
            <p class="students-eyebrow">Records / Directory</p>
            <h1>Students</h1>
            <p class="students-subtitle">Browse and manage student records.</p>
        </div>
        <a class="students-add-button" href="{{ route('students.create') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M5 12h14" /></svg>
            <span>Register student</span>
        </a>
    </header>

    @if (session('success'))
        <div class="students-alert" role="status">{{ session('success') }}</div>
    @endif

    <section class="students-directory" aria-label="Student directory">
        <form method="GET" action="{{ route('students.index') }}" class="students-filters">
            <label class="student-filter search-filter" for="student-search">
                <span>Search</span>
                <span class="student-search-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 5 5" /></svg>
                    <input id="student-search" type="search" name="search" value="{{ request('search') }}" placeholder="Name or admission number">
                </span>
            </label>
            <label class="student-filter course-filter" for="student-course">
                <span>Course</span>
                <input id="student-course" type="text" name="course" value="{{ request('course') }}" placeholder="Any course">
            </label>
            <label class="student-filter" for="student-gender">
                <span>Gender</span>
                <select id="student-gender" name="gender">
                    <option value="">All genders</option>
                    <option value="male" @selected(request('gender') === 'male')>Male</option>
                    <option value="female" @selected(request('gender') === 'female')>Female</option>
                </select>
            </label>
            <label class="student-filter" for="student-status">
                <span>Status</span>
                <select id="student-status" name="status">
                    <option value="">All statuses</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                    <option value="graduated" @selected(request('status') === 'graduated')>Graduated</option>
                </select>
            </label>
            <div class="student-filter-actions">
                <button class="students-filter-button" type="submit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 5h16l-6.5 7.2V19l-3 1v-7.8L4 5z" /></svg>
                    <span>Apply filters</span>
                </button>
                @if (request()->query())
                    <a class="students-reset-link" href="{{ route('students.index') }}">Clear</a>
                @endif
            </div>
        </form>

        <div class="students-table-heading">
            <div>
                <h2>Student directory</h2>
                <p>{{ number_format($students->total()) }} {{ str('student')->plural($students->total()) }} found</p>
            </div>
            <span class="students-page-count">Page {{ $students->currentPage() }} of {{ max(1, $students->lastPage()) }}</span>
        </div>

        <div class="students-table-scroll">
            <table class="students-table">
                <thead>
                    <tr>
                        <th scope="col">Student</th>
                        <th scope="col">Admission no.</th>
                        <th scope="col">Course / department</th>
                        <th class="student-column-year" scope="col">Academic year</th>
                        <th class="student-column-gender" scope="col">Gender</th>
                        <th scope="col">Status</th>
                        <th class="student-actions-heading" scope="col"><span class="visually-hidden">Actions</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($students as $student)
                        <tr>
                            <td>
                                <div class="student-identity">
                                    <span class="student-avatar" aria-hidden="true">{{ strtoupper(substr($student->first_name, 0, 1).substr($student->last_name, 0, 1)) }}</span>
                                    <span class="student-identity-copy"><strong>{{ $student->fullName() }}</strong><span>{{ $student->email ?: $student->phone }}</span></span>
                                </div>
                            </td>
                            <td><span class="admission-code">{{ $student->admission_number }}</span></td>
                            <td><span class="student-course-name">{{ $student->course }}</span><span class="student-department">{{ $student->department ?: 'Department not set' }}</span></td>
                            <td class="student-column-year">{{ $student->academic_year }}</td>
                            <td class="student-column-gender">{{ str($student->gender)->headline() }}</td>
                            <td><span class="student-status status-{{ str($student->status)->slug() }}"><span></span>{{ str($student->status)->headline() }}</span></td>
                            <td>
                                <div class="student-row-actions">
                                    <a class="student-icon-action" href="{{ route('students.show', $student) }}" aria-label="View {{ $student->fullName() }}" title="View student">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3" /></svg>
                                    </a>
                                    <a class="student-icon-action" href="{{ route('students.edit', $student) }}" aria-label="Edit {{ $student->fullName() }}" title="Edit student">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z" /></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="students-empty-state">
                                    <span class="students-empty-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="10" cy="8" r="3.5"/><path d="M3.5 20c.6-3.3 2.7-5 6.5-5M16 16l5 5m0-5-5 5" /></svg>
                                    </span>
                                    <strong>No students found</strong>
                                    <span>Try changing the filters or register a new student.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($students->hasPages())
            <footer class="students-table-footer">
                <span>Showing {{ $students->firstItem() }}–{{ $students->lastItem() }} of {{ number_format($students->total()) }}</span>
                <div class="students-pagination">{{ $students->links() }}</div>
            </footer>
        @endif
    </section>
</div>
@endsection

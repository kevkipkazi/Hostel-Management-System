
@extends('layouts.app')

@section('title', 'Student Profile | '.config('app.name', 'Hostel Management System'))

@section('content')
<div class="student-profile-page">
    <header class="student-profile-heading">
        <div>
            <p class="student-profile-eyebrow">Records / Students / Profile</p>
            <h1>Student Profile</h1>
            <p class="student-profile-subtitle">
                View the student's personal and academic information.
            </p>
        </div>

        <div class="student-profile-heading-actions">
            <a href="{{ route('students.index') }}" class="profile-back-button">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="1.8" stroke-linecap="round"
                     stroke-linejoin="round" aria-hidden="true">
                    <path d="m15 18-6-6 6-6"/>
                </svg>
                All students
            </a>

            <a href="{{ route('students.edit', $student) }}"
               class="profile-edit-button">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="1.8" stroke-linecap="round"
                     stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 20h9"/>
                    <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"/>
                </svg>
                Edit student
            </a>
        </div>
    </header>

    @if (session('success'))
        <div class="student-profile-alert" role="status">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round"
                 stroke-linejoin="round" aria-hidden="true">
                <path d="m5 12 4 4L19 6"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <section class="student-profile-hero">
        <div class="student-profile-avatar">
            {{ strtoupper(substr($student->first_name, 0, 1).substr($student->last_name, 0, 1)) }}
        </div>

        <div class="student-profile-hero-details">
            <span class="student-profile-label">STUDENT RECORD</span>
            <h2>{{ $student->fullName() }}</h2>

            <div class="student-profile-meta">
                <span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="1.8" stroke-linecap="round"
                         stroke-linejoin="round" aria-hidden="true">
                        <rect x="3" y="5" width="18" height="15" rx="2"/>
                        <path d="M8 5V3m8 2V3M3 10h18"/>
                    </svg>
                    {{ $student->admission_number }}
                </span>

                <span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="1.8" stroke-linecap="round"
                         stroke-linejoin="round" aria-hidden="true">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/>
                    </svg>
                    {{ $student->course }}
                </span>
            </div>
        </div>

        <div class="student-profile-hero-status">
            <span class="profile-status status-{{ str($student->status)->slug() }}">
                <span></span>
                {{ str($student->status)->headline() }}
            </span>
            <p>Current student status</p>
        </div>
    </section>

    <div class="student-profile-content">
        <section class="student-profile-card">
            <div class="student-profile-card-heading">
                <div class="profile-card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="1.8" stroke-linecap="round"
                         stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="8" r="4"/>
                        <path d="M4 21v-2a8 8 0 0 1 16 0v2"/>
                    </svg>
                </div>
                <div>
                    <h3>Personal information</h3>
                    <p>Identity and contact details</p>
                </div>
            </div>

            <div class="student-profile-details-grid">
                <div class="profile-detail">
                    <span>First name</span>
                    <strong>{{ $student->first_name ?: 'Not provided' }}</strong>
                </div>

                <div class="profile-detail">
                    <span>Last name</span>
                    <strong>{{ $student->last_name ?: 'Not provided' }}</strong>
                </div>

                <div class="profile-detail">
                    <span>Admission number</span>
                    <strong class="profile-admission-code">
                        {{ $student->admission_number }}
                    </strong>
                </div>

                <div class="profile-detail">
                    <span>Gender</span>
                    <strong>
                        {{ $student->gender ? str($student->gender)->headline() : 'Not provided' }}
                    </strong>
                </div>

                <div class="profile-detail">
                    <span>Date of birth</span>
                    <strong>
                        {{ $student->date_of_birth ? \Illuminate\Support\Carbon::parse($student->date_of_birth)->format('d M Y') : 'Not provided' }}
                    </strong>
                </div>

                <div class="profile-detail">
                    <span>Phone number</span>
                    <strong>
                        @if ($student->phone)
                            <a href="tel:{{ $student->phone }}">
                                {{ $student->phone }}
                            </a>
                        @else
                            Not provided
                        @endif
                    </strong>
                </div>

                <div class="profile-detail profile-detail-full">
                    <span>Email address</span>
                    <strong>
                        @if ($student->email)
                            <a href="mailto:{{ $student->email }}">
                                {{ $student->email }}
                            </a>
                        @else
                            Not provided
                        @endif
                    </strong>
                </div>
            </div>
        </section>

        <section class="student-profile-card">
            <div class="student-profile-card-heading">
                <div class="profile-card-icon academic-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="1.8" stroke-linecap="round"
                         stroke-linejoin="round" aria-hidden="true">
                        <path d="m2 10 10-5 10 5-10 5-10-5Z"/>
                        <path d="M6 12v5c4 3 8 3 12 0v-5"/>
                        <path d="M22 10v6"/>
                    </svg>
                </div>
                <div>
                    <h3>Academic information</h3>
                    <p>Course and enrollment details</p>
                </div>
            </div>

            <div class="student-profile-details-grid">
                <div class="profile-detail profile-detail-full">
                    <span>Course / Programme</span>
                    <strong>{{ $student->course ?: 'Not provided' }}</strong>
                </div>

                <div class="profile-detail">
                    <span>Department</span>
                    <strong>{{ $student->department ?: 'Not assigned' }}</strong>
                </div>

                <div class="profile-detail">
                    <span>Academic year</span>
                    <strong>{{ $student->academic_year ?: 'Not provided' }}</strong>
                </div>

                <div class="profile-detail">
                    <span>Enrollment status</span>
                    <strong>
                        <span class="profile-status status-{{ str($student->status)->slug() }}">
                            <span></span>
                            {{ str($student->status)->headline() }}
                        </span>
                    </strong>
                </div>
            </div>
        </section>
    </div>

    <footer class="student-profile-footer">
        <div>
            <strong>Need to make changes?</strong>
            <p>Update the student's record to keep the information accurate.</p>
        </div>

        <a href="{{ route('students.edit', $student) }}"
           class="profile-edit-button">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.8" stroke-linecap="round"
                 stroke-linejoin="round" aria-hidden="true">
                <path d="M12 20h9"/>
                <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"/>
            </svg>
            Edit student details
        </a>
    </footer>
</div>

<style>
.student-profile-page {
    --profile-navy: #102f5f;
    --profile-orange: #f47b20;
    --profile-text: #1c293b;
    --profile-muted: #738096;
    --profile-border: #e5eaf1;
    max-width: 1200px;
    margin: 0 auto;
    padding: 8px 0 36px;
    color: var(--profile-text);
}
.student-profile-page * { box-sizing: border-box; }

.student-profile-heading {
    display: flex; align-items: center; justify-content: space-between;
    gap: 20px; margin-bottom: 26px;
}
.student-profile-eyebrow {
    margin: 0 0 8px; color: var(--profile-orange);
    font-size: 12px; font-weight: 800;
    text-transform: uppercase; letter-spacing: 1.2px;
}
.student-profile-heading h1 {
    margin: 0; color: var(--profile-navy);
    font-size: clamp(25px, 3vw, 32px);
    font-weight: 800; letter-spacing: -1px;
}
.student-profile-subtitle {
    margin: 8px 0 0; color: var(--profile-muted); font-size: 14px;
}
.student-profile-heading-actions {
    display: flex; flex-wrap: wrap; align-items: center; gap: 10px;
}
.profile-back-button, .profile-edit-button {
    display: inline-flex; align-items: center; justify-content: center;
    gap: 8px; min-height: 42px; padding: 10px 15px;
    border-radius: 9px; text-decoration: none;
    font-size: 12px; font-weight: 750; transition: .2s;
}
.profile-back-button {
    border: 1px solid #dce3ed; color: #39475d; background: #fff;
}
.profile-back-button:hover { background: #f4f7fb; color: var(--profile-navy); }
.profile-edit-button {
    border: 1px solid var(--profile-navy);
    background: var(--profile-navy); color: #fff;
}
.profile-edit-button:hover { background: #183e75; color: #fff; }
.profile-back-button svg, .profile-edit-button svg {
    width: 16px; height: 16px;
}

.student-profile-hero {
    display: flex; align-items: center; gap: 23px;
    padding: 29px; margin-bottom: 23px;
    border-radius: 17px; color: #fff;
    background: linear-gradient(115deg, #102f5f 0%, #194579 100%);
    box-shadow: 0 10px 26px rgba(16,47,95,.12);
}
.student-profile-avatar {
    width: 78px; height: 78px; flex-shrink: 0;
    display: grid; place-items: center; border-radius: 21px;
    background: rgba(255,255,255,.13);
    border: 1px solid rgba(255,255,255,.2);
    color: #fff; font-size: 25px; font-weight: 800;
}
.student-profile-hero-details { min-width: 0; }
.student-profile-label {
    color: #ffc18f; font-size: 10px; font-weight: 800;
    letter-spacing: 1.5px;
}
.student-profile-hero-details h2 {
    margin: 7px 0 12px; font-size: clamp(21px, 3vw, 28px);
    font-weight: 800; letter-spacing: -.5px;
    overflow-wrap: anywhere;
}
.student-profile-meta {
    display: flex; flex-wrap: wrap; gap: 10px 20px;
}
.student-profile-meta > span {
    display: inline-flex; align-items: center; gap: 7px;
    color: #e0e9f7; font-size: 12px;
}
.student-profile-meta svg { width: 15px; height: 15px; flex-shrink: 0; }
.student-profile-hero-status { margin-left: auto; text-align: right; }
.student-profile-hero-status p {
    margin: 8px 0 0; color: #d5e0f1; font-size: 10px;
}
.profile-status {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 7px 10px; border-radius: 20px;
    font-size: 11px; font-weight: 800; white-space: nowrap;
}
.profile-status > span {
    width: 6px; height: 6px; border-radius: 50%;
    background: currentColor;
}
.status-active { color: #177d49; background: #e5f8ed; }
.status-inactive { color: #687386; background: #eff1f5; }
.status-graduated { color: #3158a5; background: #e8eeff; }

.student-profile-content {
    display: grid; grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 22px; align-items: start;
}
.student-profile-card {
    min-width: 0; border: 1px solid var(--profile-border);
    border-radius: 15px; background: #fff;
    box-shadow: 0 5px 22px rgba(16,47,95,.035);
    overflow: hidden;
}
.student-profile-card-heading {
    display: flex; align-items: center; gap: 13px;
    padding: 22px; border-bottom: 1px solid var(--profile-border);
}
.profile-card-icon {
    width: 42px; height: 42px; flex-shrink: 0;
    display: grid; place-items: center; border-radius: 12px;
    color: var(--profile-navy); background: #eaf0fc;
}
.profile-card-icon svg { width: 21px; height: 21px; }
.profile-card-icon.academic-icon { color: #bd5b12; background: #fff0e4; }
.student-profile-card-heading h3 {
    margin: 0; font-size: 15px; font-weight: 800;
}
.student-profile-card-heading p {
    margin: 5px 0 0; color: var(--profile-muted); font-size: 11px;
}
.student-profile-details-grid {
    display: grid; grid-template-columns: repeat(2, minmax(0, 1fr));
    padding: 5px 22px 15px;
}
.profile-detail {
    display: flex; flex-direction: column; gap: 8px;
    min-width: 0; padding: 17px 10px 16px 0;
    border-bottom: 1px solid #edf0f5;
}
.profile-detail:nth-last-child(-n+2) { border-bottom: none; }
.profile-detail-full { grid-column: 1 / -1; }
.profile-detail > span {
    color: var(--profile-muted); font-size: 11px; font-weight: 600;
}
.profile-detail > strong {
    min-width: 0; color: #26364c;
    font-size: 13px; font-weight: 750; overflow-wrap: anywhere;
}
.profile-detail a {
    color: var(--profile-navy); text-decoration: none;
}
.profile-detail a:hover { color: var(--profile-orange); text-decoration: underline; }
.profile-admission-code {
    display: inline-block; align-self: flex-start;
    padding: 5px 8px; border-radius: 6px;
    background: #f0f4fa; color: var(--profile-navy) !important;
    font-size: 12px !important;
}
.student-profile-footer {
    display: flex; justify-content: space-between; align-items: center;
    gap: 20px; margin-top: 23px; padding: 20px 23px;
    border: 1px solid var(--profile-border); border-radius: 14px;
    background: #f9fbfe;
}
.student-profile-footer > div > strong { font-size: 13px; }
.student-profile-footer p {
    margin: 5px 0 0; color: var(--profile-muted); font-size: 12px;
}
.student-profile-alert {
    display: flex; align-items: center; gap: 9px;
    padding: 13px 16px; margin-bottom: 20px; border-radius: 9px;
    border: 1px solid #c9ecd8; background: #eaf8f0;
    color: #176b40; font-size: 13px;
}
.student-profile-alert svg { width: 17px; height: 17px; flex-shrink: 0; }

@media (max-width: 900px) {
    .student-profile-content { grid-template-columns: 1fr; }
}
@media (max-width: 600px) {
    .student-profile-heading {
        align-items: flex-start; flex-direction: column;
    }
    .student-profile-heading-actions { width: 100%; }
    .student-profile-heading-actions > * { flex: 1; }
    .student-profile-hero {
        padding: 21px; flex-wrap: wrap; gap: 15px;
    }
    .student-profile-avatar { width: 61px; height: 61px; border-radius: 16px; }
    .student-profile-hero-details { flex: 1; }
    .student-profile-hero-status {
        width: 100%; margin: 4px 0 0; padding-top: 15px;
        text-align: left; border-top: 1px solid rgba(255,255,255,.15);
    }
    .student-profile-hero-status p { display: inline; margin-left: 7px; }
    .student-profile-card-heading { padding: 18px; }
    .student-profile-details-grid { padding-right: 18px; padding-left: 18px; }
    .student-profile-footer { align-items: flex-start; flex-direction: column; }
    .student-profile-footer > a { width: 100%; }
}
</style>
@endsection

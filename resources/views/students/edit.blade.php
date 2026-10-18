
@extends('layouts.app')

@section('title', 'Edit Student | '.config('app.name', 'Hostel Management System'))

@section('content')
<div class="student-form-page">
    <header class="student-form-heading">
        <div>
            <p class="student-form-eyebrow">Records / Students / Edit</p>
            <h1>Edit Student</h1>
            <p class="student-form-subtitle">
                Update the student's personal and academic information.
            </p>
        </div>

        <a href="{{ route('students.show', $student) }}"
           class="student-secondary-button">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.8" stroke-linecap="round"
                 stroke-linejoin="round" aria-hidden="true">
                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/>
                <circle cx="12" cy="12" r="3"/>
            </svg>
            View profile
        </a>
    </header>

    @if (session('success'))
        <div class="student-form-alert success" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="student-form-alert error" role="alert">
            <strong>Please correct the following errors:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ route('students.update', $student) }}"
          class="student-form-card">
        @csrf
        @method('PUT')

        <div class="student-form-card-header">
            <div class="student-form-avatar">
                {{ strtoupper(substr($student->first_name, 0, 1).substr($student->last_name, 0, 1)) }}
            </div>
            <div>
                <h2>{{ $student->fullName() }}</h2>
                <p>Admission number: {{ $student->admission_number }}</p>
            </div>
            <span class="student-form-status status-{{ str($student->status)->slug() }}">
                {{ str($student->status)->headline() }}
            </span>
        </div>

        <div class="student-form-section">
            <div class="student-form-section-title">
                <span class="student-section-icon">01</span>
                <div>
                    <h3>Personal information</h3>
                    <p>Update the student's identifying and contact details.</p>
                </div>
            </div>

            <div class="student-form-grid">
                <div class="student-field">
                    <label for="first_name">First name <span>*</span></label>
                    <input id="first_name" type="text" name="first_name"
                           value="{{ old('first_name', $student->first_name) }}"
                           autocomplete="given-name" required
                           class="@error('first_name') is-invalid @enderror">
                    @error('first_name')
                        <small class="student-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="student-field">
                    <label for="last_name">Last name <span>*</span></label>
                    <input id="last_name" type="text" name="last_name"
                           value="{{ old('last_name', $student->last_name) }}"
                           autocomplete="family-name" required
                           class="@error('last_name') is-invalid @enderror">
                    @error('last_name')
                        <small class="student-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="student-field">
                    <label for="admission_number">Admission number <span>*</span></label>
                    <input id="admission_number" type="text" name="admission_number"
                           value="{{ old('admission_number', $student->admission_number) }}"
                           required
                           class="@error('admission_number') is-invalid @enderror">
                    @error('admission_number')
                        <small class="student-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="student-field">
                    <label for="phone">Phone number <span>*</span></label>
                    <input id="phone" type="tel" name="phone"
                           value="{{ old('phone', $student->phone) }}"
                           autocomplete="tel" required
                           class="@error('phone') is-invalid @enderror">
                    @error('phone')
                        <small class="student-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="student-field">
                    <label for="email">Email address</label>
                    <input id="email" type="email" name="email"
                               value="{{ old('email', $student->user && ! $canManageStudentAccount ? $student->user->email : $student->email) }}"
                           autocomplete="email"
                           placeholder="student@example.com"
                               @readonly($student->user && ! $canManageStudentAccount)
                           class="@error('email') is-invalid @enderror">
                    @error('email')
                        <small class="student-field-error">{{ $message }}</small>
                    @enderror
                        @if ($student->user && ! $canManageStudentAccount)
                            <small class="student-field-error">Only an administrator or hostel manager can change the login email.</small>
                        @endif
                </div>

                <div class="student-field">
                    <label for="gender">Gender <span>*</span></label>
                    <select id="gender" name="gender" required
                            class="@error('gender') is-invalid @enderror">
                        <option value="">Select gender</option>
                        <option value="male" @selected(old('gender', $student->gender) === 'male')>Male</option>
                        <option value="female" @selected(old('gender', $student->gender) === 'female')>Female</option>
                    </select>
                    @error('gender')
                        <small class="student-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="student-field">
                    <label for="date_of_birth">Date of birth</label>
                    <input id="date_of_birth" type="date" name="date_of_birth"
                           value="{{ old('date_of_birth', $student->date_of_birth ? \Illuminate\Support\Carbon::parse($student->date_of_birth)->format('Y-m-d') : '') }}"
                           max="{{ now()->format('Y-m-d') }}"
                           class="@error('date_of_birth') is-invalid @enderror">
                    @error('date_of_birth')
                        <small class="student-field-error">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </div>

        <div class="student-form-divider"></div>

        <div class="student-form-section">
            <div class="student-form-section-title">
                <span class="student-section-icon">02</span>
                <div>
                    <h3>Academic information</h3>
                    <p>Maintain the student's course and departmental details.</p>
                </div>
            </div>

            <div class="student-form-grid">
                <div class="student-field">
                    <label for="course">Course <span>*</span></label>
                    <input id="course" type="text" name="course"
                           value="{{ old('course', $student->course) }}"
                           required
                           class="@error('course') is-invalid @enderror">
                    @error('course')
                        <small class="student-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="student-field">
                    <label for="department">Department</label>
                    <input id="department" type="text" name="department"
                           value="{{ old('department', $student->department) }}"
                           class="@error('department') is-invalid @enderror">
                    @error('department')
                        <small class="student-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="student-field">
                    <label for="academic_year">Academic year <span>*</span></label>
                    <input id="academic_year" type="text" name="academic_year"
                           value="{{ old('academic_year', $student->academic_year) }}"
                           placeholder="e.g. 2026"
                           required
                           class="@error('academic_year') is-invalid @enderror">
                    @error('academic_year')
                        <small class="student-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="student-field">
                    <label for="status">Student status <span>*</span></label>
                    <select id="status" name="status" required
                            class="@error('status') is-invalid @enderror">
                        <option value="active" @selected(old('status', $student->status) === 'active')>Active</option>
                        <option value="inactive" @selected(old('status', $student->status) === 'inactive')>Inactive</option>
                        <option value="graduated" @selected(old('status', $student->status) === 'graduated')>Graduated</option>
                    </select>
                    @error('status')
                        <small class="student-field-error">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </div>

        @if ($canManageStudentAccount)
            <div class="student-form-divider"></div>

            <div class="student-form-section">
                <div class="student-form-section-title">
                    <span class="student-section-icon">03</span>
                    <div>
                        <h3>Student login</h3>
                        <p>
                            @if ($student->user)
                                Leave both fields blank to keep the current password.
                            @else
                                Set a password to create this student's login account.
                            @endif
                        </p>
                    </div>
                </div>

                <div class="student-form-grid">
                    <div class="student-field">
                        <label for="password">New password</label>
                        <input id="password" type="password" name="password"
                               autocomplete="new-password"
                               class="@error('password') is-invalid @enderror">
                        @error('password')
                            <small class="student-field-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="student-field">
                        <label for="password_confirmation">Confirm new password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation"
                               autocomplete="new-password"
                               class="@error('password_confirmation') is-invalid @enderror">
                        @error('password_confirmation')
                            <small class="student-field-error">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
        @endif

        <div class="student-form-footer">
            <p><span>*</span> Required fields</p>
            <div class="student-form-actions">
                <a href="{{ route('students.index') }}"
                   class="student-secondary-button">Cancel</a>
                <button type="submit" class="student-primary-button">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="1.9" stroke-linecap="round"
                         stroke-linejoin="round" aria-hidden="true">
                        <path d="M20 7 10 17l-5-5"/>
                    </svg>
                    Save changes
                </button>
            </div>
        </div>
    </form>
</div>

<style>
.student-form-page {
    --student-navy: #102f5f;
    --student-orange: #f47b20;
    --student-text: #1c293b;
    --student-muted: #738096;
    --student-border: #e5eaf1;
    max-width: 1200px;
    margin: 0 auto;
    padding: 8px 0 36px;
    color: var(--student-text);
}
.student-form-page * { box-sizing: border-box; }
.student-form-heading {
    display: flex; align-items: center; justify-content: space-between;
    gap: 20px; margin-bottom: 26px;
}
.student-form-eyebrow {
    color: var(--student-orange); font-size: 12px; font-weight: 800;
    text-transform: uppercase; letter-spacing: 1.2px; margin: 0 0 8px;
}
.student-form-heading h1 {
    color: var(--student-navy); font-size: clamp(25px, 3vw, 32px);
    font-weight: 800; letter-spacing: -1px; margin: 0;
}
.student-form-subtitle {
    color: var(--student-muted); margin: 8px 0 0; font-size: 14px;
}
.student-form-card {
    background: #fff; border: 1px solid var(--student-border);
    border-radius: 18px; box-shadow: 0 8px 30px rgba(16,47,95,.045);
    overflow: hidden;
}
.student-form-card-header {
    display: flex; align-items: center; gap: 14px;
    padding: 24px 28px; background: #f8faff;
    border-bottom: 1px solid var(--student-border);
}
.student-form-avatar {
    width: 50px; height: 50px; flex-shrink: 0; border-radius: 15px;
    display: grid; place-items: center; background: #e7eefb;
    color: var(--student-navy); font-size: 16px; font-weight: 800;
}
.student-form-card-header h2 { margin: 0; font-size: 17px; font-weight: 800; }
.student-form-card-header p { margin: 5px 0 0; color: var(--student-muted); font-size: 13px; }
.student-form-status {
    margin-left: auto; padding: 7px 11px; border-radius: 20px;
    font-size: 11px; font-weight: 800; white-space: nowrap;
}
.student-form-status:before {
    content: ""; display: inline-block; width: 6px; height: 6px;
    border-radius: 50%; background: currentColor; margin: 0 7px 1px 0;
}
.status-active { background: #e7f8ef; color: #17834c; }
.status-inactive { background: #f0f2f5; color: #687386; }
.status-graduated { background: #e9efff; color: #3158a5; }
.student-form-section { padding: 27px 28px; }
.student-form-section-title {
    display: flex; align-items: center; gap: 13px; margin-bottom: 24px;
}
.student-section-icon {
    width: 39px; height: 39px; flex-shrink: 0; border-radius: 12px;
    display: grid; place-items: center; background: #fff1e6;
    color: #c55c0a; font-size: 12px; font-weight: 800;
}
.student-form-section-title h3 { margin: 0; font-size: 16px; font-weight: 800; }
.student-form-section-title p { margin: 5px 0 0; font-size: 12px; color: var(--student-muted); }
.student-form-grid {
    display: grid; grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 21px 24px;
}
.student-field { min-width: 0; }
.student-field label {
    display: block; font-size: 12px; font-weight: 750;
    color: #344258; margin-bottom: 8px;
}
.student-field label span, .student-form-footer > p span { color: #e45b50; }
.student-field input, .student-field select {
    display: block; width: 100%; min-height: 45px; padding: 11px 13px;
    border: 1px solid #dce3ed; border-radius: 9px;
    background: #fff; color: var(--student-text); font: inherit;
    font-size: 13px; outline: none; transition: border-color .2s, box-shadow .2s;
}
.student-field input:focus, .student-field select:focus {
    border-color: var(--student-navy);
    box-shadow: 0 0 0 3px rgba(16,47,95,.09);
}
.student-field input.is-invalid, .student-field select.is-invalid {
    border-color: #dc5454;
}
.student-field-error { display: block; color: #c23d3d; font-size: 11px; margin-top: 6px; }
.student-form-divider { height: 1px; background: var(--student-border); margin: 0 28px; }
.student-form-footer {
    display: flex; align-items: center; justify-content: space-between;
    gap: 16px; padding: 20px 28px; background: #fafbfd;
    border-top: 1px solid var(--student-border);
}
.student-form-footer > p { color: var(--student-muted); font-size: 12px; margin: 0; }
.student-form-actions { display: flex; align-items: center; gap: 10px; }
.student-primary-button, .student-secondary-button {
    display: inline-flex; align-items: center; justify-content: center;
    gap: 8px; min-height: 42px; padding: 10px 16px; border-radius: 9px;
    text-decoration: none; font-size: 12px; font-weight: 750;
    cursor: pointer; transition: .2s;
}
.student-primary-button {
    border: 1px solid var(--student-navy); background: var(--student-navy);
    color: #fff;
}
.student-primary-button:hover { background: #183e75; color: #fff; }
.student-secondary-button {
    border: 1px solid #dce3ed; background: #fff; color: #39475d;
}
.student-secondary-button:hover { background: #f4f7fb; color: var(--student-navy); }
.student-primary-button svg, .student-secondary-button svg { width: 16px; height: 16px; }
.student-form-alert {
    padding: 14px 17px; border-radius: 10px; margin-bottom: 20px;
    font-size: 13px;
}
.student-form-alert.success { background: #eaf8f0; color: #176b40; border: 1px solid #c9ecd8; }
.student-form-alert.error { background: #fff0ef; color: #a83232; border: 1px solid #f3d0cd; }
.student-form-alert ul { margin: 8px 0 0; padding-left: 20px; }
@media (max-width: 640px) {
    .student-form-page { padding-top: 0; }
    .student-form-heading { align-items: flex-start; flex-direction: column; }
    .student-form-card-header { padding: 18px; flex-wrap: wrap; }
    .student-form-section { padding: 22px 18px; }
    .student-form-grid { grid-template-columns: 1fr; gap: 17px; }
    .student-form-divider { margin: 0 18px; }
    .student-form-footer { padding: 18px; align-items: flex-start; flex-direction: column; }
    .student-form-actions { width: 100%; }
    .student-form-actions > * { flex: 1; }
}
</style>
@endsection

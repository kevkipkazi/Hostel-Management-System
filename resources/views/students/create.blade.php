
@extends('layouts.app')

@section('title', 'Register Student | '.config('app.name', 'Hostel Management System'))

@section('content')
<div class="student-create-page">

    {{-- Page heading --}}
    <header class="create-heading">
        <div>
            <p class="create-eyebrow">Records / Students / Registration</p>
            <h1>Register Student</h1>
            <p class="create-subtitle">
                Add the student's profile and create their login account.
            </p>
        </div>

        <a href="{{ route('students.index') }}" class="create-back-button">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.8" stroke-linecap="round"
                 stroke-linejoin="round" aria-hidden="true">
                <path d="m15 18-6-6 6-6"/>
            </svg>
            Back to students
        </a>
    </header>

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="create-alert create-alert-error" role="alert">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round"
                 stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 8v4m0 4h.01"/>
            </svg>

            <div>
                <strong>Please check the form.</strong>
                <p>Correct the following errors before registering the student.</p>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Registration form --}}
    <form method="POST"
          action="{{ route('students.store') }}"
          class="create-student-form">
        @csrf

        {{-- Form introduction --}}
        <div class="create-form-header">
            <div class="create-form-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="1.8" stroke-linecap="round"
                     stroke-linejoin="round" aria-hidden="true">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="10" cy="7" r="4"/>
                    <path d="M20 8v6m-3-3h6"/>
                </svg>
            </div>
            <div>
                <h2>Student registration form</h2>
                <p>Enter accurate profile details and sign-in credentials.</p>
            </div>
        </div>

        {{-- Section 1: Personal details --}}
        <section class="create-form-section">
            <div class="create-section-heading">
                <span class="create-section-number">01</span>
                <div>
                    <h3>Personal information</h3>
                    <p>Basic identification and contact information.</p>
                </div>
            </div>

            <div class="create-form-grid">
                <div class="create-field">
                    <label for="first_name">First name <span>*</span></label>
                    <input
                        id="first_name"
                        type="text"
                        name="first_name"
                        value="{{ old('first_name') }}"
                        placeholder="Enter first name"
                        autocomplete="given-name"
                        required
                        class="@error('first_name') is-invalid @enderror"
                    >
                    @error('first_name')
                        <small class="create-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="create-field">
                    <label for="last_name">Last name <span>*</span></label>
                    <input
                        id="last_name"
                        type="text"
                        name="last_name"
                        value="{{ old('last_name') }}"
                        placeholder="Enter last name"
                        autocomplete="family-name"
                        required
                        class="@error('last_name') is-invalid @enderror"
                    >
                    @error('last_name')
                        <small class="create-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="create-field">
                    <label for="admission_number">
                        Admission number <span>*</span>
                    </label>
                    <input
                        id="admission_number"
                        type="text"
                        name="admission_number"
                        value="{{ old('admission_number') }}"
                        placeholder="e.g. ADM/2026/001"
                        required
                        class="@error('admission_number') is-invalid @enderror"
                    >
                    @error('admission_number')
                        <small class="create-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="create-field">
                    <label for="phone">Phone number <span>*</span></label>
                    <input
                        id="phone"
                        type="tel"
                        name="phone"
                        value="{{ old('phone') }}"
                        placeholder="Enter phone number"
                        autocomplete="tel"
                        required
                        class="@error('phone') is-invalid @enderror"
                    >
                    @error('phone')
                        <small class="create-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="create-field">
                    <label for="email">Email address <span>*</span></label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="student@example.com"
                        autocomplete="email"
                        required
                        class="@error('email') is-invalid @enderror"
                    >
                    @error('email')
                        <small class="create-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="create-field">
                    <label for="gender">Gender <span>*</span></label>
                    <select
                        id="gender"
                        name="gender"
                        required
                        class="@error('gender') is-invalid @enderror"
                    >
                        <option value="">Select gender</option>
                        <option value="male" @selected(old('gender') === 'male')>Male</option>
                        <option value="female" @selected(old('gender') === 'female')>Female</option>
                    </select>
                    @error('gender')
                        <small class="create-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="create-field">
                    <label for="date_of_birth">Date of birth</label>
                    <input
                        id="date_of_birth"
                        type="date"
                        name="date_of_birth"
                        value="{{ old('date_of_birth') }}"
                        max="{{ now()->format('Y-m-d') }}"
                        class="@error('date_of_birth') is-invalid @enderror"
                    >
                    @error('date_of_birth')
                        <small class="create-field-error">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </section>

        <div class="create-section-divider"></div>

        {{-- Section 2: Academic details --}}
        <section class="create-form-section">
            <div class="create-section-heading">
                <span class="create-section-number">02</span>
                <div>
                    <h3>Academic information</h3>
                    <p>Course, department and current enrollment status.</p>
                </div>
            </div>

            <div class="create-form-grid">
                <div class="create-field">
                    <label for="course">Course / Programme <span>*</span></label>
                    <input
                        id="course"
                        type="text"
                        name="course"
                        value="{{ old('course') }}"
                        placeholder="Enter course name"
                        required
                        class="@error('course') is-invalid @enderror"
                    >
                    @error('course')
                        <small class="create-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="create-field">
                    <label for="department">Department</label>
                    <input
                        id="department"
                        type="text"
                        name="department"
                        value="{{ old('department') }}"
                        placeholder="Enter department"
                        class="@error('department') is-invalid @enderror"
                    >
                    @error('department')
                        <small class="create-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="create-field">
                    <label for="academic_year">Academic year <span>*</span></label>
                    <input
                        id="academic_year"
                        type="text"
                        name="academic_year"
                        value="{{ old('academic_year') }}"
                        placeholder="e.g. 2026"
                        required
                        class="@error('academic_year') is-invalid @enderror"
                    >
                    @error('academic_year')
                        <small class="create-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="create-field">
                    <label for="status">Student status <span>*</span></label>
                    <select
                        id="status"
                        name="status"
                        required
                        class="@error('status') is-invalid @enderror"
                    >
                        <option value="active" @selected(old('status', 'active') === 'active')>
                            Active
                        </option>
                        <option value="inactive" @selected(old('status') === 'inactive')>
                            Inactive
                        </option>
                        <option value="graduated" @selected(old('status') === 'graduated')>
                            Graduated
                        </option>
                    </select>
                    @error('status')
                        <small class="create-field-error">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </section>

        <div class="create-section-divider"></div>

        <section class="create-form-section">
            <div class="create-section-heading">
                <span class="create-section-number">03</span>
                <div>
                    <h3>Student login</h3>
                    <p>The student will use this email and password to sign in.</p>
                </div>
            </div>

            <div class="create-form-grid">
                <div class="create-field">
                    <label for="password">Password <span>*</span></label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        autocomplete="new-password"
                        required
                        class="@error('password') is-invalid @enderror"
                    >
                    @error('password')
                        <small class="create-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="create-field">
                    <label for="password_confirmation">Confirm password <span>*</span></label>
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        autocomplete="new-password"
                        required
                        class="@error('password_confirmation') is-invalid @enderror"
                    >
                    @error('password_confirmation')
                        <small class="create-field-error">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </section>

        {{-- Form footer --}}
        <footer class="create-form-footer">
            <p><span>*</span> Required fields</p>

            <div class="create-form-actions">
                <a href="{{ route('students.index') }}"
                   class="create-cancel-button">
                    Cancel
                </a>

                <button type="submit" class="create-submit-button">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="1.9" stroke-linecap="round"
                         stroke-linejoin="round" aria-hidden="true">
                        <path d="M12 5v14M5 12h14"/>
                    </svg>
                    Register student
                </button>
            </div>
        </footer>
    </form>
</div>

<style>
.student-create-page {
    --create-navy: #102f5f;
    --create-orange: #f47b20;
    --create-text: #1c293b;
    --create-muted: #738096;
    --create-border: #e5eaf1;
    max-width: 1200px;
    margin: 0 auto;
    padding: 8px 0 36px;
    color: var(--create-text);
}
.student-create-page * { box-sizing: border-box; }

.create-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 26px;
}
.create-eyebrow {
    margin: 0 0 8px;
    color: var(--create-orange);
    font-size: 12px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1.2px;
}
.create-heading h1 {
    margin: 0;
    color: var(--create-navy);
    font-size: clamp(25px, 3vw, 32px);
    font-weight: 800;
    letter-spacing: -1px;
}
.create-subtitle {
    margin: 8px 0 0;
    color: var(--create-muted);
    font-size: 14px;
}
.create-back-button, .create-cancel-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 42px;
    padding: 10px 15px;
    border: 1px solid #dce3ed;
    border-radius: 9px;
    background: #fff;
    color: #39475d;
    text-decoration: none;
    font-size: 12px;
    font-weight: 750;
    transition: .2s;
}
.create-back-button:hover, .create-cancel-button:hover {
    background: #f4f7fb;
    color: var(--create-navy);
}
.create-back-button svg { width: 16px; height: 16px; }

.create-student-form {
    overflow: hidden;
    border: 1px solid var(--create-border);
    border-radius: 17px;
    background: #fff;
    box-shadow: 0 8px 30px rgba(16,47,95,.045);
}
.create-form-header {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 24px 28px;
    background: #f8faff;
    border-bottom: 1px solid var(--create-border);
}
.create-form-icon {
    display: grid;
    place-items: center;
    width: 48px;
    height: 48px;
    flex-shrink: 0;
    border-radius: 14px;
    background: #e8eefb;
    color: var(--create-navy);
}
.create-form-icon svg { width: 23px; height: 23px; }
.create-form-header h2 {
    margin: 0;
    font-size: 17px;
    font-weight: 800;
}
.create-form-header p {
    margin: 5px 0 0;
    color: var(--create-muted);
    font-size: 12px;
}
.create-form-section { padding: 27px 28px; }
.create-section-heading {
    display: flex;
    align-items: center;
    gap: 13px;
    margin-bottom: 24px;
}
.create-section-number {
    display: grid;
    place-items: center;
    width: 39px;
    height: 39px;
    flex-shrink: 0;
    border-radius: 12px;
    background: #fff1e6;
    color: #c55c0a;
    font-size: 12px;
    font-weight: 800;
}
.create-section-heading h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 800;
}
.create-section-heading p {
    margin: 5px 0 0;
    color: var(--create-muted);
    font-size: 12px;
}
.create-form-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 21px 24px;
}
.create-field { min-width: 0; }
.create-field label {
    display: block;
    margin-bottom: 8px;
    color: #344258;
    font-size: 12px;
    font-weight: 750;
}
.create-field label span, .create-form-footer > p span { color: #e45b50; }
.create-field input, .create-field select {
    display: block;
    width: 100%;
    min-height: 45px;
    padding: 11px 13px;
    border: 1px solid #dce3ed;
    border-radius: 9px;
    background: #fff;
    color: var(--create-text);
    font-family: inherit;
    font-size: 13px;
    outline: none;
    transition: border-color .2s, box-shadow .2s;
}
.create-field input::placeholder { color: #a0a9b8; }
.create-field input:focus, .create-field select:focus {
    border-color: var(--create-navy);
    box-shadow: 0 0 0 3px rgba(16,47,95,.09);
}
.create-field input.is-invalid, .create-field select.is-invalid {
    border-color: #dc5454;
}
.create-field-error {
    display: block;
    margin-top: 6px;
    color: #c23d3d;
    font-size: 11px;
}
.create-section-divider {
    height: 1px;
    margin: 0 28px;
    background: var(--create-border);
}
.create-form-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 20px 28px;
    border-top: 1px solid var(--create-border);
    background: #fafbfd;
}
.create-form-footer > p {
    margin: 0;
    color: var(--create-muted);
    font-size: 12px;
}
.create-form-actions {
    display: flex;
    align-items: center;
    gap: 10px;
}
.create-submit-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 42px;
    padding: 10px 17px;
    border: 1px solid var(--create-navy);
    border-radius: 9px;
    background: var(--create-navy);
    color: #fff;
    font-family: inherit;
    font-size: 12px;
    font-weight: 750;
    cursor: pointer;
    transition: .2s;
}
.create-submit-button:hover {
    background: #183e75;
    border-color: #183e75;
}
.create-submit-button svg { width: 16px; height: 16px; }

.create-alert {
    display: flex;
    align-items: flex-start;
    gap: 11px;
    padding: 15px 17px;
    margin-bottom: 20px;
    border-radius: 10px;
    font-size: 13px;
}
.create-alert > svg { width: 19px; height: 19px; flex-shrink: 0; }
.create-alert-error {
    color: #a83232;
    border: 1px solid #f3d0cd;
    background: #fff0ef;
}
.create-alert p { margin: 5px 0; }
.create-alert ul { margin: 7px 0 0; padding-left: 19px; }
.create-alert li + li { margin-top: 4px; }

@media (max-width: 640px) {
    .create-heading {
        align-items: flex-start;
        flex-direction: column;
    }
    .create-form-header { padding: 19px; }
    .create-form-section { padding: 22px 18px; }
    .create-form-grid { grid-template-columns: 1fr; gap: 17px; }
    .create-section-divider { margin: 0 18px; }
    .create-form-footer {
        align-items: flex-start;
        flex-direction: column;
        padding: 18px;
    }
    .create-form-actions { width: 100%; }
    .create-form-actions > * { flex: 1; }
}
</style>
@endsection

@extends('layouts.app')

@section('title', 'My profile | '.config('app.name', 'Hostel Management System'))

@section('content')
    <div class="profile-page">
        <header class="profile-page-heading">
            <p class="profile-eyebrow">Account / Settings</p>
            <h1>My profile</h1>
            <p>Manage your personal details, password, and account.</p>
        </header>

        <div class="profile-sections">
            <section class="profile-panel">
                @include('profile.partials.update-profile-information-form')
            </section>

            <section class="profile-panel">
                @include('profile.partials.update-password-form')
            </section>

            <section class="profile-panel profile-danger-panel">
                @include('profile.partials.delete-user-form')
            </section>
        </div>
    </div>
@endsection

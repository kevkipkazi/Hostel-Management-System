<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#f3f6f2">
    <title>Sign in | {{ config('app.name', 'Hostel Management System') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700|dm-serif-display:400&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="login-screen">
        <main class="login-main">
            <section class="login-panel" aria-labelledby="login-title">
                <a class="login-brand" href="{{ url('/') }}" aria-label="{{ config('app.name', 'Hostel Management System') }} home">
                    <span class="brand-mark" aria-hidden="true">
                        <svg viewBox="0 0 32 32" fill="none">
                            <path d="M5 26V8.5L16 3l11 5.5V26M11 26v-8h10v8M11 11h.01M16 11h.01M21 11h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                    <span>{{ config('app.name', 'Hostel Management System') }}</span>
                </a>

                <div class="login-content">
                    <p class="login-kicker"><span></span> Management portal</p>
                    <h1 id="login-title">Welcome back<span>.</span></h1>
                    <p class="login-intro">Sign in to continue to your account.</p>

                    @if (session('status'))
                        <div class="login-status" role="status">{{ session('status') }}</div>
                    @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="field">
                    <label class="field-label" for="email">Email address</label>
                    <div class="input-wrap">
                        <span class="input-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/>
                            </svg>
                        </span>
                        <input
                            id="email"
                            class="field-input @error('email') is-invalid @enderror"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="Enter your email address"
                            required
                            autofocus
                            autocomplete="username"
                            aria-describedby="@error('email') email-error @enderror"
                        >
                    </div>
                    @error('email')
                        <span class="field-error" id="email-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="field">
                    <label class="field-label" for="password">Password</label>
                    <div class="input-wrap">
                        <span class="input-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/>
                            </svg>
                        </span>
                        <input
                            id="password"
                            class="field-input password-input @error('password') is-invalid @enderror"
                            type="password"
                            name="password"
                            placeholder="Enter your password"
                            required
                            autocomplete="current-password"
                            aria-describedby="@error('password') password-error @enderror"
                        >
                        <button
                            type="button"
                            class="password-toggle"
                            id="togglePassword"
                            aria-label="Show password"
                            aria-controls="password"
                            aria-pressed="false"
                        >
                            <svg id="eyeIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <span class="field-error" id="password-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-options">
                    <label class="remember" for="remember_me">
                        <input
                            id="remember_me"
                            type="checkbox"
                            name="remember"
                            value="1"
                            @checked(old('remember'))
                        >
                        <span>Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="forgot-link" href="{{ route('password.request') }}">Forgot password?</a>
                    @endif
                </div>

                <button type="submit" class="login-button">
                    <span>Sign in to your account</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M5 12h14M13 6l6 6-6 6"/>
                    </svg>
                </button>
            </form>
                </div>

            <div class="security-note">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11z"/><path d="m9 12 2 2 4-4"/>
                </svg>
                <span>Your account is protected by secure authentication.</span>
            </div>

            <footer class="copyright">
                &copy; {{ date('Y') }} Hostel Management System. All rights reserved.
            </footer>
        </section>

        <aside class="login-visual" aria-label="A welcoming hostel room">
            <img src="https://images.unsplash.com/photo-1555854877-bab0e564b8d5?auto=format&fit=crop&w=1800&q=85" alt="A bright, thoughtfully arranged hostel room">
            <div class="visual-shade"></div>
            <div class="visual-copy">
                <span class="visual-label"><span></span> A place to belong</span>
                <p>Make room<br>for what matters.</p>
            </div>
            <span class="visual-caption">HOSTEL MANAGEMENT</span>
        </aside>
    </main>
</div>

<script>
    (() => {
        const toggle = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        if (!toggle || !password || !eyeIcon) return;

        const eyeOpen = '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>';
        const eyeClosed = '<path d="M3 3l18 18"/><path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"/><path d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.5 0 10 7 10 7a14.8 14.8 0 0 1-3.1 3.9"/><path d="M6.2 6.2C3.5 8 2 12 2 12s3.5 7 10 7c1 0 2-.2 2.8-.5"/>';

        toggle.addEventListener('click', () => {
            const shouldShow = password.type === 'password';
            password.type = shouldShow ? 'text' : 'password';
            toggle.setAttribute('aria-label', shouldShow ? 'Hide password' : 'Show password');
            toggle.setAttribute('aria-pressed', String(shouldShow));
            eyeIcon.innerHTML = shouldShow ? eyeClosed : eyeOpen;
        });
    })();
</script>
</body>
</html>
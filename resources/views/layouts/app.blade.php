<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>@yield('title', config('app.name', 'Hostel Management System'))</title>
	<link rel="preconnect" href="https://fonts.bunny.net">
	<link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700&display=swap" rel="stylesheet">
	@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="app-body">
	<div class="app-shell">
		@include('layouts.navigation')

		<div class="app-main">
			<header class="app-topbar">
				<div class="topbar-heading">
					<span class="topbar-eyebrow">HOSTEL MANAGEMENT</span>
					<span class="topbar-label">Operations workspace</span>
				</div>
				<div class="topbar-account">
					<span class="user-initial" aria-hidden="true">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
					<span class="account-details">
						<strong>{{ auth()->user()->name ?? 'User' }}</strong>
						<span>{{ str($role ?? auth()->user()->role ?? 'User')->replace('_', ' ')->title() }}</span>
					</span>
					<form method="POST" action="{{ route('logout') }}">
						@csrf
						<button class="sign-out-button" type="submit" aria-label="Sign out" title="Sign out">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
								<path d="M10 17l5-5-5-5M15 12H3"/><path d="M12 3h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-6"/>
							</svg>
						</button>
					</form>
				</div>
			</header>

			<main class="app-content">
				@yield('content')
			</main>

			<footer class="app-footer">
				<span>&copy; {{ date('Y') }} {{ config('app.name', 'Hostel Management System') }}</span>
				<span>Accommodation, made easier.</span>
			</footer>
		</div>
	</div>
</body>
</html>

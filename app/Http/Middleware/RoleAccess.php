<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleAccess
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (! $user->is_active) {
            auth()->logout();

            return redirect()->route('login')->withErrors([
                'email' => 'Your account is inactive. Please contact the administrator.',
            ]);
        }

        if ($roles === []) {
            return $next($request);
        }

        $allowed = collect($roles)
            ->flatMap(fn ($role) => explode(',', $role))
            ->map(fn ($role) => trim($role))
            ->filter()->all();

        if (! in_array($user->role, $allowed, true)) {
            abort(403, 'You do not have permission to access this resource.');
        }

        return $next($request);
    }
}

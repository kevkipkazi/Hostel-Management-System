<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $email = strtolower(trim((string) $request->input('email')));
        $request->merge(['email' => $email]);

        $validated = $request->validate([
            'admission_number' => [
                'required',
                'string',
                Rule::exists('students', 'admission_number')
                    ->where('email', $email)
                    ->where('status', 'active')
                    ->whereNull('user_id'),
            ],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = DB::transaction(function () use ($validated): User {
            $student = Student::query()
                ->where('admission_number', $validated['admission_number'])
                ->where('email', $validated['email'])
                ->where('status', 'active')
                ->whereNull('user_id')
                ->lockForUpdate()
                ->first();

            if (! $student) {
                throw ValidationException::withMessages([
                    'admission_number' => 'The admission number and email must match an unlinked active student record.',
                ]);
            }

            $user = User::create([
                'name' => $student->fullName(),
                'email' => $validated['email'],
                'password' => $validated['password'],
                'role' => 'student',
                'is_active' => true,
            ]);

            $student->user()->associate($user);
            $student->save();

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}

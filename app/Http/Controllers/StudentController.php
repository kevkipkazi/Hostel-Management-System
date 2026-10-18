<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('admission_number', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('course')) {
            $query->where('course', $request->course);
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $students = $query->latest()->paginate(15)->withQueryString();

        return view('students.index', compact('students'));
    }

    public function create()
    {
        return view('students.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'admission_number' => ['required', 'string', 'unique:students,admission_number'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female'],
            'date_of_birth' => ['nullable', 'date'],
            'phone' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique(User::class)],
            'course' => ['required', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'academic_year' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive,graduated'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        DB::transaction(function () use ($validated): void {
            $student = Student::create(Arr::only($validated, [
                'admission_number',
                'first_name',
                'last_name',
                'gender',
                'date_of_birth',
                'phone',
                'email',
                'course',
                'department',
                'academic_year',
                'status',
            ]));

            $user = User::create([
                'name' => $student->fullName(),
                'email' => $student->email,
                'password' => $validated['password'],
                'role' => 'student',
                'is_active' => true,
            ]);
            $user->email_verified_at = now();
            $user->save();

            $student->user()->associate($user);
            $student->save();
        });

        return redirect()->route('students.index')->with('success', 'Student and login account created successfully.');
    }

    public function show(Student $student)
    {
        $student->load(['bookings.preferredHostel', 'allocations.room.hostel', 'payments']);

        return view('students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $student->loadMissing('user');
        $canManageStudentAccount = in_array(auth()->user()->role, ['administrator', 'hostel_manager'], true);

        return view('students.edit', compact('student', 'canManageStudentAccount'));
    }

    public function update(Request $request, Student $student)
    {
        $student->loadMissing('user');
        $studentUser = $student->user;
        $canManageStudentAccount = in_array($request->user()->role, ['administrator', 'hostel_manager'], true);

        if (! $canManageStudentAccount && $request->filled('password')) {
            abort(403);
        }

        $emailRules = [
            Rule::requiredIf($canManageStudentAccount && ($studentUser || $request->filled('password'))),
            'email',
            'max:255',
        ];
        $uniqueEmailRule = Rule::unique(User::class);

        if ($studentUser) {
            $uniqueEmailRule->ignore($studentUser->id);

            if (! $canManageStudentAccount) {
                $emailRules[] = Rule::in([$studentUser->email]);
            }
        }

        $emailRules[] = $uniqueEmailRule;

        $validated = $request->validate([
            'admission_number' => ['required', 'string', 'unique:students,admission_number,'.$student->id],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female'],
            'date_of_birth' => ['nullable', 'date'],
            'phone' => ['required', 'string', 'max:255'],
            'email' => $emailRules,
            'course' => ['required', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'academic_year' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive,graduated'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        DB::transaction(function () use ($validated, $student, $studentUser, $canManageStudentAccount): void {
            $student->update(Arr::only($validated, [
                'admission_number',
                'first_name',
                'last_name',
                'gender',
                'date_of_birth',
                'phone',
                'email',
                'course',
                'department',
                'academic_year',
                'status',
            ]));

            if (! $canManageStudentAccount) {
                return;
            }

            if ($studentUser) {
                $studentUser->name = $student->fullName();
                $studentUser->email = $student->email;
                $studentUser->email_verified_at = now();

                if (filled($validated['password'] ?? null)) {
                    $studentUser->password = $validated['password'];
                }

                $studentUser->save();

                return;
            }

            if (! filled($validated['password'] ?? null)) {
                return;
            }

            $user = User::create([
                'name' => $student->fullName(),
                'email' => $student->email,
                'password' => $validated['password'],
                'role' => 'student',
                'is_active' => true,
            ]);
            $user->email_verified_at = now();
            $user->save();

            $student->user()->associate($user);
            $student->save();
        });

        return redirect()->route('students.index')->with('success', 'Student details updated successfully.');
    }

    public function destroy(Student $student)
    {
        $student->update(['status' => 'inactive']);

        return redirect()->route('students.index')->with('success', 'Student record deactivated successfully.');
    }
}

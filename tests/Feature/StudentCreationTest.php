<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_hostel_manager_can_create_a_student_with_a_login_password(): void
    {
        $manager = User::factory()->create(['role' => 'hostel_manager']);

        $response = $this->actingAs($manager)
            ->post(route('students.store'), $this->studentDetails());

        $student = Student::where('admission_number', 'ADM-100')->firstOrFail();
        $user = $student->user;

        $this->assertNotNull($user);
        $this->assertSame('student', $user->role);
        $this->assertSame($student->email, $user->email);
        $this->assertTrue($user->hasVerifiedEmail());
        $response->assertRedirect(route('students.index'));

        $this->post('/logout');
        $this->post('/login', [
            'email' => $student->email,
            'password' => 'Password123!',
        ])->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticatedAs($user);
        $this->get(route('bookings.create'))->assertOk();
    }

    public function test_administrator_can_open_the_student_creation_form(): void
    {
        $administrator = User::factory()->create(['role' => 'administrator']);

        $this->actingAs($administrator)
            ->get(route('students.create'))
            ->assertOk()
            ->assertSee('Password')
            ->assertSee('Confirm password');
    }

    public function test_hostel_manager_can_change_a_students_login_password(): void
    {
        $student = $this->existingStudent();
        $user = $this->linkStudentUser($student, 'OriginalPass123!');
        $manager = User::factory()->create(['role' => 'hostel_manager']);

        $this->actingAs($manager)
            ->put(route('students.update', $student), $this->studentUpdateDetails($student, [
                'password' => 'UpdatedPass123!',
                'password_confirmation' => 'UpdatedPass123!',
            ]))
            ->assertRedirect(route('students.index'));

        $this->post('/logout');
        $this->post('/login', [
            'email' => $user->email,
            'password' => 'UpdatedPass123!',
        ])->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticatedAs($user);
    }

    public function test_blank_password_keeps_the_students_existing_password(): void
    {
        $student = $this->existingStudent();
        $user = $this->linkStudentUser($student, 'OriginalPass123!');
        $manager = User::factory()->create(['role' => 'hostel_manager']);

        $this->actingAs($manager)
            ->put(route('students.update', $student), $this->studentUpdateDetails($student))
            ->assertRedirect(route('students.index'));

        $this->post('/logout');
        $this->post('/login', [
            'email' => $user->email,
            'password' => 'OriginalPass123!',
        ])->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticatedAs($user);
    }

    public function test_hostel_manager_can_add_a_login_to_an_existing_student(): void
    {
        $student = $this->existingStudent();
        $manager = User::factory()->create(['role' => 'hostel_manager']);

        $this->actingAs($manager)
            ->put(route('students.update', $student), $this->studentUpdateDetails($student, [
                'password' => 'NewLogin123!',
                'password_confirmation' => 'NewLogin123!',
            ]))
            ->assertRedirect(route('students.index'));

        $user = $student->fresh()->user;

        $this->assertNotNull($user);
        $this->assertSame('student', $user->role);
        $this->assertTrue($user->hasVerifiedEmail());
    }

    public function test_finance_officer_cannot_change_a_students_login_password(): void
    {
        $student = $this->existingStudent();
        $user = $this->linkStudentUser($student, 'OriginalPass123!');
        $financeOfficer = User::factory()->create(['role' => 'finance_officer']);

        $this->actingAs($financeOfficer)
            ->put(route('students.update', $student), $this->studentUpdateDetails($student, [
                'password' => 'StolenPass123!',
                'password_confirmation' => 'StolenPass123!',
            ]))
            ->assertForbidden();

        $this->assertTrue(Hash::check('OriginalPass123!', $user->fresh()->password));
    }

    public function test_finance_officer_cannot_create_student_accounts(): void
    {
        $financeOfficer = User::factory()->create(['role' => 'finance_officer']);

        $this->actingAs($financeOfficer)
            ->get(route('students.create'))
            ->assertForbidden();

        $this->post(route('students.store'), $this->studentDetails())
            ->assertForbidden();

        $this->assertDatabaseMissing('students', ['admission_number' => 'ADM-100']);
        $this->assertDatabaseMissing('users', ['email' => 'student100@example.com']);
    }

    private function studentDetails(): array
    {
        return [
            'admission_number' => 'ADM-100',
            'first_name' => 'Sam',
            'last_name' => 'Student',
            'gender' => 'male',
            'phone' => '0700000100',
            'email' => 'student100@example.com',
            'course' => 'Computer Science',
            'department' => 'ICT',
            'academic_year' => '2026',
            'status' => 'active',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];
    }

    private function existingStudent(): Student
    {
        return Student::create([
            'admission_number' => 'ADM-200',
            'first_name' => 'Alex',
            'last_name' => 'Student',
            'gender' => 'male',
            'phone' => '0700000200',
            'email' => 'student200@example.com',
            'course' => 'Computer Science',
            'department' => 'ICT',
            'academic_year' => '2026',
            'status' => 'active',
        ]);
    }

    private function linkStudentUser(Student $student, string $password): User
    {
        $user = User::factory()->create([
            'name' => $student->fullName(),
            'email' => $student->email,
            'password' => $password,
            'role' => 'student',
        ]);

        $student->user()->associate($user)->save();

        return $user;
    }

    private function studentUpdateDetails(Student $student, array $passwordDetails = []): array
    {
        return array_merge([
            'admission_number' => $student->admission_number,
            'first_name' => $student->first_name,
            'last_name' => $student->last_name,
            'gender' => $student->gender,
            'phone' => $student->phone,
            'email' => $student->email,
            'course' => $student->course,
            'department' => $student->department,
            'academic_year' => $student->academic_year,
            'status' => $student->status,
        ], $passwordDetails);
    }
}

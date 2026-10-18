<?php

namespace Tests\Feature\Auth;

use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $student = Student::create([
            'admission_number' => 'ADM-001',
            'first_name' => 'Test',
            'last_name' => 'Student',
            'gender' => 'male',
            'phone' => '0700000000',
            'email' => 'test@example.com',
            'course' => 'Computer Science',
            'academic_year' => '2026',
            'status' => 'active',
        ]);
        Notification::fake();

        $response = $this->post('/register', [
            'admission_number' => $student->admission_number,
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', $student->email)->firstOrFail();

        $this->assertAuthenticated();
        $this->assertSame('student', $user->role);
        $this->assertSame($student->id, $user->student->id);
        Notification::assertSentTo($user, VerifyEmail::class);
        $response->assertRedirect(route('dashboard', absolute: false));
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_sees_the_hostel_overview_and_management_links(): void
    {
        $administrator = User::factory()->create(['role' => 'administrator']);

        $this->actingAs($administrator)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Registered students')
            ->assertSee('Recent bookings')
            ->assertSee('Fee overview')
            ->assertSee('Hostels & rooms');
    }

    public function test_student_sees_their_account_view_without_global_totals(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $this->actingAs($student)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Student account')
            ->assertSee('not linked to a student profile')
            ->assertDontSee('Registered students')
            ->assertDontSee('Fee overview');
    }
}

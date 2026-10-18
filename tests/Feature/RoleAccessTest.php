<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_can_access_operations_finance_and_full_reports(): void
    {
        $administrator = User::factory()->create(['role' => 'administrator']);

        $this->actingAs($administrator)->get(route('students.create'))->assertOk();
        $this->actingAs($administrator)->get(route('hostels.create'))->assertOk();
        $this->actingAs($administrator)->get(route('payments.create'))->assertOk();
        $this->actingAs($administrator)->get(route('reports.index'))->assertOk();

        $this->actingAs($administrator)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Administrator workspace')
            ->assertSee('Fee overview')
            ->assertSee('Pending bookings');
    }

    public function test_hostel_manager_can_manage_accommodation_but_not_finances(): void
    {
        $manager = User::factory()->create(['role' => 'hostel_manager']);

        $this->actingAs($manager)->get(route('students.create'))->assertOk();
        $this->actingAs($manager)->get(route('hostels.create'))->assertOk();
        $this->actingAs($manager)->get(route('rooms.create'))->assertOk();
        $this->actingAs($manager)->get(route('bookings.create'))->assertOk();
        $this->actingAs($manager)->get(route('allocations.index'))->assertOk();
        $this->actingAs($manager)->get(route('reports.students'))->assertOk();
        $this->actingAs($manager)->get(route('reports.rooms'))->assertOk();
        $this->actingAs($manager)->get(route('reports.export', ['type' => 'students']))->assertOk();

        $this->actingAs($manager)->get(route('payments.index'))->assertForbidden();
        $this->actingAs($manager)->get(route('reports.index'))->assertForbidden();
        $this->actingAs($manager)->get(route('reports.payments'))->assertForbidden();
        $this->actingAs($manager)->get(route('reports.export', ['type' => 'payments']))->assertForbidden();

        $this->actingAs($manager)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Hostel Manager workspace')
            ->assertSee('Pending bookings')
            ->assertDontSee('Fee overview');
    }

    public function test_finance_officer_can_manage_finances_but_not_hostel_operations(): void
    {
        $financeOfficer = User::factory()->create(['role' => 'finance_officer']);

        $this->actingAs($financeOfficer)->get(route('payments.index'))->assertOk();
        $this->actingAs($financeOfficer)->get(route('payments.create'))->assertOk();
        $this->actingAs($financeOfficer)->get(route('reports.payments'))->assertOk();
        $this->actingAs($financeOfficer)->get(route('reports.outstanding-fees'))->assertOk();
        $this->actingAs($financeOfficer)->get(route('reports.export', ['type' => 'payments']))->assertOk();
        $this->actingAs($financeOfficer)->get(route('reports.export', ['type' => 'students']))->assertForbidden();

        $this->actingAs($financeOfficer)->get(route('students.index'))->assertForbidden();
        $this->actingAs($financeOfficer)->get(route('students.create'))->assertForbidden();
        $this->actingAs($financeOfficer)->get(route('hostels.index'))->assertForbidden();
        $this->actingAs($financeOfficer)->get(route('bookings.index'))->assertForbidden();
        $this->actingAs($financeOfficer)->get(route('allocations.index'))->assertForbidden();
        $this->actingAs($financeOfficer)->get(route('reports.index'))->assertForbidden();
        $this->actingAs($financeOfficer)->get(route('reports.students'))->assertForbidden();

        $this->actingAs($financeOfficer)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Finance Officer workspace')
            ->assertSee('Payments received')
            ->assertDontSee('Pending bookings')
            ->assertDontSee('Registered students');
    }
}

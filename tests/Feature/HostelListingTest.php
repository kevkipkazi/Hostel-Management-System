<?php

namespace Tests\Feature;

use App\Models\Hostel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HostelListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_filter_applies_to_all_hostel_search_matches(): void
    {
        $user = User::factory()->create(['role' => 'administrator']);

        Hostel::create([
            'hostel_name' => 'Apex Hostel',
            'hostel_code' => 'APX-ACTIVE',
            'location' => 'Main campus',
            'gender_category' => 'male',
            'status' => 'active',
        ]);

        Hostel::create([
            'hostel_name' => 'Apex Hostel Annex',
            'hostel_code' => 'APX-INACTIVE',
            'location' => 'North campus',
            'gender_category' => 'male',
            'status' => 'inactive',
        ]);

        $this->actingAs($user)
            ->get(route('hostels.index', ['search' => 'Apex', 'status' => 'active']))
            ->assertOk()
            ->assertSee('APX-ACTIVE')
            ->assertDontSee('APX-INACTIVE');
    }
}

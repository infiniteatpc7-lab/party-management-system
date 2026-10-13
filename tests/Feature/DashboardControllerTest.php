<?php

namespace Tests\Feature;

use App\Models\Party;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_dashboard_and_see_database_statistics(): void
    {
        $admin = User::factory()->admin()->create();

        Party::factory()->create([
            'party_type' => 'new',
            'priority_status' => 'priority',
            'latitude' => 12.9716,
            'longitude' => 77.5946,
        ]);
        Party::factory()->create([
            'party_type' => 'old',
            'priority_status' => 'non_priority',
            'latitude' => 12.9720,
            'longitude' => 77.5950,
        ]);

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('totalParties', 2);
        $response->assertViewHas('priorityParties', 1);
        $response->assertViewHas('nonPriorityParties', 1);
        $response->assertViewHas('newParties', 1);
        $response->assertViewHas('oldParties', 1);
    }

    public function test_dashboard_calculates_nearby_parties_when_gps_provided(): void
    {
        $admin = User::factory()->admin()->create();

        // Close party (within 1 km)
        Party::factory()->create([
            'party_name' => 'Close Venue',
            'latitude' => 12.9716,
            'longitude' => 77.5946,
        ]);

        // Far party (beyond 20 km)
        Party::factory()->create([
            'party_name' => 'Faraway Venue',
            'latitude' => 13.3000,
            'longitude' => 77.5000,
        ]);

        $response = $this->actingAs($admin)->get('/dashboard?lat=12.9716&lng=77.5946');

        $response->assertStatus(200);
        $response->assertViewHas('hasLocation', true);
        $response->assertViewHas('nearbyPartiesCount', 1);
    }
}

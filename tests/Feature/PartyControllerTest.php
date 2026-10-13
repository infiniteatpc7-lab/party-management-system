<?php

namespace Tests\Feature;

use App\Models\Party;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PartyControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_parties_index_displays_parties_and_respects_filters(): void
    {
        $admin = User::factory()->admin()->create();

        $priorityParty = Party::factory()->priority()->newType()->create([
            'party_name' => 'Diamond Ballroom',
            'latitude' => 12.9716,
            'longitude' => 77.5946,
        ]);
        $oldParty = Party::factory()->nonPriority()->oldType()->create([
            'party_name' => 'Silver Hall',
            'latitude' => 12.9750,
            'longitude' => 77.5960,
        ]);

        // Test basic load
        $response = $this->actingAs($admin)->get('/parties');
        $response->assertStatus(200);
        $response->assertSee('Diamond Ballroom');
        $response->assertSee('Silver Hall');

        // Test search filter
        $searchResponse = $this->actingAs($admin)->get('/parties?search=Diamond');
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('Diamond Ballroom');
        $searchResponse->assertDontSee('Silver Hall');

        // Test priority filter
        $priorityResponse = $this->actingAs($admin)->get('/parties?priority=priority');
        $priorityResponse->assertStatus(200);
        $priorityResponse->assertSee('Diamond Ballroom');
        $priorityResponse->assertDontSee('Silver Hall');
    }

    public function test_party_can_be_created_with_valid_coordinates(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/parties', [
            'party_name' => 'Royal Heritage Convention',
            'mobile_number' => '+91 9845012345',
            'map_link' => 'https://maps.google.com/?q=12.9716,77.5946',
            'latitude' => 12.9716,
            'longitude' => 77.5946,
            'party_type' => 'new',
            'priority_status' => 'priority',
        ]);

        $response->assertRedirect('/parties');
        $this->assertDatabaseHas('parties', [
            'party_name' => 'Royal Heritage Convention',
            'mobile_number' => '+91 9845012345',
            'priority_status' => 'priority',
        ]);
    }

    public function test_party_creation_auto_extracts_coordinates_from_map_link(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/parties', [
            'party_name' => 'Auto Coordinate Venue',
            'mobile_number' => '+91 9845099999',
            'map_link' => 'https://www.google.com/maps/@12.9715987,77.5945627,15z',
            'latitude' => '',
            'longitude' => '',
            'party_type' => 'new',
            'priority_status' => 'non_priority',
        ]);

        $response->assertRedirect('/parties');
        $this->assertDatabaseHas('parties', [
            'party_name' => 'Auto Coordinate Venue',
            'latitude' => 12.9715987,
            'longitude' => 77.5945627,
        ]);
    }

    public function test_duplicate_party_name_and_mobile_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();

        Party::factory()->create([
            'party_name' => 'Unique Plaza',
            'mobile_number' => '+91 9845000000',
        ]);

        $response = $this->actingAs($admin)->post('/parties', [
            'party_name' => 'Unique Plaza',
            'mobile_number' => '+91 9845000000',
            'latitude' => 12.9716,
            'longitude' => 77.5946,
            'party_type' => 'new',
            'priority_status' => 'priority',
        ]);

        $response->assertSessionHasErrors(['party_name']);
    }

    public function test_party_can_be_updated(): void
    {
        $admin = User::factory()->admin()->create();
        $party = Party::factory()->create([
            'party_name' => 'Initial Name',
        ]);

        $response = $this->actingAs($admin)->put("/parties/{$party->id}", [
            'party_name' => 'Updated Name',
            'mobile_number' => '+91 9999999999',
            'latitude' => 12.9800,
            'longitude' => 77.6000,
            'party_type' => 'old',
            'priority_status' => 'priority',
        ]);

        $response->assertRedirect('/parties');
        $this->assertDatabaseHas('parties', [
            'id' => $party->id,
            'party_name' => 'Updated Name',
            'party_type' => 'old',
            'priority_status' => 'priority',
        ]);
    }

    public function test_party_can_be_soft_deleted(): void
    {
        $admin = User::factory()->admin()->create();
        $party = Party::factory()->create();

        $response = $this->actingAs($admin)->delete("/parties/{$party->id}");

        $response->assertRedirect('/parties');
        $this->assertSoftDeleted('parties', [
            'id' => $party->id,
        ]);
    }

    public function test_map_view_renders_pins_and_parties(): void
    {
        $admin = User::factory()->admin()->create();
        Party::factory()->create([
            'party_name' => 'Mapped Venue',
            'latitude' => 12.9716,
            'longitude' => 77.5946,
        ]);

        $response = $this->actingAs($admin)->get('/parties/map');

        $response->assertStatus(200);
        $response->assertSee('Interactive Map Explorer');
        $response->assertSee('Mapped Venue');
    }

    public function test_party_can_be_created_and_updated_with_lead_status(): void
    {
        $admin = User::factory()->admin()->create();

        // Create party with is_lead = 1
        $createResponse = $this->actingAs($admin)->post('/parties', [
            'party_name' => 'Lead Client Venue',
            'mobile_number' => '+91 9900112233',
            'latitude' => 12.9716,
            'longitude' => 77.5946,
            'party_type' => 'new',
            'priority_status' => 'priority',
            'is_lead' => '1',
        ]);

        $createResponse->assertRedirect('/parties');
        $this->assertDatabaseHas('parties', [
            'party_name' => 'Lead Client Venue',
            'is_lead' => true,
        ]);

        $party = Party::where('party_name', 'Lead Client Venue')->firstOrFail();
        $this->assertTrue($party->isLead());

        // Update party to uncheck is_lead (omitted or '0')
        $updateResponse = $this->actingAs($admin)->put("/parties/{$party->id}", [
            'party_name' => 'Lead Client Venue',
            'latitude' => 12.9716,
            'longitude' => 77.5946,
            'party_type' => 'new',
            'priority_status' => 'priority',
            // is_lead not sent (unchecked checkbox)
        ]);

        $updateResponse->assertRedirect('/parties');
        $party->refresh();
        $this->assertFalse($party->isLead());
    }

    public function test_parties_can_be_filtered_by_lead_status(): void
    {
        $admin = User::factory()->admin()->create();

        Party::factory()->create([
            'party_name' => 'Special Lead Hall',
            'is_lead' => true,
        ]);

        Party::factory()->create([
            'party_name' => 'Standard Client Center',
            'is_lead' => false,
        ]);

        // Filter for leads only
        $leadResponse = $this->actingAs($admin)->get('/parties?lead=lead');
        $leadResponse->assertStatus(200);
        $leadResponse->assertSee('Special Lead Hall');
        $leadResponse->assertDontSee('Standard Client Center');

        // Filter for non-leads only
        $nonLeadResponse = $this->actingAs($admin)->get('/parties?lead=non_lead');
        $nonLeadResponse->assertStatus(200);
        $nonLeadResponse->assertSee('Standard Client Center');
        $nonLeadResponse->assertDontSee('Special Lead Hall');
    }
}

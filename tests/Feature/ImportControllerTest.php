<?php

namespace Tests\Feature;

use App\Models\Party;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImportControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_template_download_returns_valid_csv(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/import/template');

        $response->assertStatus(200);
        $this->assertStringContainsString('party_name,mobile_number,map_link', $response->getContent());
    }

    public function test_csv_upload_successfully_imports_valid_rows(): void
    {
        $admin = User::factory()->admin()->create();

        $csvContent = "party_name,mobile_number,map_link,latitude,longitude,party_type,priority_status\n";
        $csvContent .= "Emerald Palace,+91 9876543210,,12.9715987,77.5945627,new,priority\n";
        $csvContent .= "Sapphire Hall,+91 9123456789,,12.9720000,77.5950000,old,non_priority\n";

        $file = UploadedFile::fake()->createWithContent('parties.csv', $csvContent);

        $response = $this->actingAs($admin)->post('/import', [
            'file' => $file,
        ]);

        $response->assertSessionHas('import_result');
        $result = session('import_result');

        $this->assertEquals(2, $result['total']);
        $this->assertEquals(2, $result['imported']);
        $this->assertEquals(0, $result['failed']);

        $this->assertDatabaseHas('parties', ['party_name' => 'Emerald Palace', 'priority_status' => 'priority']);
        $this->assertDatabaseHas('parties', ['party_name' => 'Sapphire Hall', 'party_type' => 'old']);
    }

    public function test_import_detects_duplicates_and_validation_errors(): void
    {
        $admin = User::factory()->admin()->create();

        Party::factory()->create([
            'party_name' => 'Existing Venue',
            'mobile_number' => '+91 9000000000',
        ]);

        $csvContent = "party_name,mobile_number,map_link,latitude,longitude,party_type,priority_status\n";
        // Duplicate row with existing DB record
        $csvContent .= "Existing Venue,+91 9000000000,,12.9715,77.5945,new,priority\n";
        // Invalid coordinates row
        $csvContent .= "Broken Venue,+91 9111111111,,invalid_lat,invalid_lng,new,priority\n";
        // Missing name row
        $csvContent .= ",+91 9222222222,,12.9700,77.5900,new,priority\n";

        $file = UploadedFile::fake()->createWithContent('invalid_parties.csv', $csvContent);

        $response = $this->actingAs($admin)->post('/import', [
            'file' => $file,
        ]);

        $result = session('import_result');
        $this->assertGreaterThan(0, $result['duplicates']);
        $this->assertGreaterThan(0, $result['failed']);
    }
}

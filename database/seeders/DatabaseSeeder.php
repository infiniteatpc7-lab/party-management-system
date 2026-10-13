<?php

namespace Database\Seeders;

use App\Models\Party;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Super Administrator account
        User::updateOrCreate(
            ['email' => 'infiniteatpc7@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('Admin@123#'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Realistic seed parties with actual coordinates in and around the metro area
        $sampleParties = [
            [
                'party_name' => 'Royal Orchid Convention Center',
                'mobile_number' => '+91 9845012345',
                'map_link' => 'https://maps.google.com/?q=12.9602000,77.6485000',
                'latitude' => 12.9602000,
                'longitude' => 77.6485000,
                'party_type' => 'new',
                'priority_status' => 'priority',
            ],
            [
                'party_name' => 'The Oberoi Grand Ballroom',
                'mobile_number' => '+91 9845123456',
                'map_link' => 'https://maps.google.com/?q=12.9734000,77.6185000',
                'latitude' => 12.9734000,
                'longitude' => 77.6185000,
                'party_type' => 'new',
                'priority_status' => 'priority',
            ],
            [
                'party_name' => 'ITC Windsor Manor Hall',
                'mobile_number' => '+91 9845234567',
                'map_link' => 'https://maps.google.com/?q=12.9965000,77.5878000',
                'latitude' => 12.9965000,
                'longitude' => 77.5878000,
                'party_type' => 'old',
                'priority_status' => 'priority',
            ],
            [
                'party_name' => 'Indiranagar Club Lawn',
                'mobile_number' => '+91 9845345678',
                'map_link' => 'https://maps.google.com/?q=12.9784000,77.6408000',
                'latitude' => 12.9784000,
                'longitude' => 77.6408000,
                'party_type' => 'new',
                'priority_status' => 'non_priority',
            ],
            [
                'party_name' => 'Koramangala Valley Banquet',
                'mobile_number' => '+91 9845456789',
                'map_link' => 'https://maps.google.com/?q=12.9352000,77.6245000',
                'latitude' => 12.9352000,
                'longitude' => 77.6245000,
                'party_type' => 'new',
                'priority_status' => 'non_priority',
            ],
            [
                'party_name' => 'Whitefield Marriott Pavilion',
                'mobile_number' => '+91 9845567890',
                'map_link' => 'https://maps.google.com/?q=12.9856000,77.7312000',
                'latitude' => 12.9856000,
                'longitude' => 77.7312000,
                'party_type' => 'old',
                'priority_status' => 'priority',
            ],
            [
                'party_name' => 'Malleshwaram Heritage Bhavan',
                'mobile_number' => '+91 9845678901',
                'map_link' => 'https://maps.google.com/?q=13.0034000,77.5689000',
                'latitude' => 13.0034000,
                'longitude' => 77.5689000,
                'party_type' => 'old',
                'priority_status' => 'non_priority',
            ],
            [
                'party_name' => 'Jayanagar Cosmopolitan Pavilion',
                'mobile_number' => '+91 9845789012',
                'map_link' => 'https://maps.google.com/?q=12.9250000,77.5830000',
                'latitude' => 12.9250000,
                'longitude' => 77.5830000,
                'party_type' => 'new',
                'priority_status' => 'priority',
            ],
            [
                'party_name' => 'UB City Rooftop Lounge',
                'mobile_number' => '+91 9845890123',
                'map_link' => 'https://maps.google.com/?q=12.9716000,77.5959000',
                'latitude' => 12.9716000,
                'longitude' => 77.5959000,
                'party_type' => 'new',
                'priority_status' => 'priority',
            ],
            [
                'party_name' => 'Hebbal Lake View Arena',
                'mobile_number' => '+91 9845901234',
                'map_link' => 'https://maps.google.com/?q=13.0450000,77.5910000',
                'latitude' => 13.0450000,
                'longitude' => 77.5910000,
                'party_type' => 'old',
                'priority_status' => 'non_priority',
            ],
        ];

        foreach ($sampleParties as $partyData) {
            Party::updateOrCreate(
                ['party_name' => $partyData['party_name']],
                $partyData
            );
        }
    }
}

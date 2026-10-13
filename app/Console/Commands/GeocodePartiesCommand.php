<?php

namespace App\Console\Commands;

use App\Models\Party;
use App\Services\CoordinateExtractor;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('parties:geocode')]
#[Description('Extract and populate coordinates for parties with map links but missing coordinates')]
class GeocodePartiesCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $parties = Party::whereNull('latitude')
            ->whereNotNull('map_link')
            ->get();

        if ($parties->isEmpty()) {
            $this->info('No parties with missing coordinates found.');

            return self::SUCCESS;
        }

        $this->info("Found {$parties->count()} parties with missing coordinates. Resolving...");

        $successCount = 0;
        $failedCount = 0;

        foreach ($parties as $party) {
            $this->line("Processing: {$party->party_name} ({$party->map_link})");

            $coords = CoordinateExtractor::extract($party->map_link);

            if ($coords) {
                $party->update([
                    'latitude' => $coords['latitude'],
                    'longitude' => $coords['longitude'],
                ]);

                $this->info(" -> Geocoded successfully: Lat {$coords['latitude']}, Lng {$coords['longitude']}");
                $successCount++;
            } else {
                $this->warn(" -> Could not extract coordinates for: {$party->party_name}");
                $failedCount++;
            }
        }

        $this->info("Done! Successfully geocoded {$successCount} parties. (Failed: {$failedCount})");

        return self::SUCCESS;
    }
}

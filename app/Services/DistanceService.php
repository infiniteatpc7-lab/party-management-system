<?php

namespace App\Services;

class DistanceService
{
    /**
     * Calculate straight-line Haversine distance between two sets of GPS coordinates in kilometers.
     *
     * @return float Distance in kilometers
     */
    public static function calculate(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadiusKm = 6371.0;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadiusKm * $c;
    }

    /**
     * Format distance in kilometers to a clean, human-readable string.
     */
    public static function format(?float $distanceKm): ?string
    {
        if ($distanceKm === null) {
            return null;
        }

        if ($distanceKm < 1.0) {
            return round($distanceKm * 1000).' m';
        }

        return number_format($distanceKm, 1).' km';
    }
}

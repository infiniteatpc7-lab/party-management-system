<?php

namespace Tests\Unit;

use App\Services\DistanceService;
use PHPUnit\Framework\TestCase;

class DistanceServiceTest extends TestCase
{
    public function test_calculates_haversine_distance_accurately(): void
    {
        // MG Road (12.9716, 77.5946) to Indiranagar (12.9784, 77.6408) is approx 5.0 - 5.1 km
        $distance = DistanceService::calculate(12.9716, 77.5946, 12.9784, 77.6408);

        $this->assertGreaterThan(4.5, $distance);
        $this->assertLessThan(5.5, $distance);
    }

    public function test_formats_distance_properly(): void
    {
        $this->assertEquals('500 m', DistanceService::format(0.5));
        $this->assertEquals('5.2 km', DistanceService::format(5.234));
        $this->assertNull(DistanceService::format(null));
    }
}

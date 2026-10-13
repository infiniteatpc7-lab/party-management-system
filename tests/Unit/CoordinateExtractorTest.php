<?php

namespace Tests\Unit;

use App\Services\CoordinateExtractor;
use PHPUnit\Framework\TestCase;

class CoordinateExtractorTest extends TestCase
{
    public function test_extracts_coordinates_from_at_syntax(): void
    {
        $url = 'https://www.google.com/maps/@12.9715987,77.5945627,15z';
        $result = CoordinateExtractor::extract($url);

        $this->assertNotNull($result);
        $this->assertEqualsWithDelta(12.9715987, $result['latitude'], 0.0001);
        $this->assertEqualsWithDelta(77.5945627, $result['longitude'], 0.0001);
    }

    public function test_extracts_coordinates_from_q_query_parameter(): void
    {
        $url = 'https://maps.google.com/?q=13.0827,80.2707';
        $result = CoordinateExtractor::extract($url);

        $this->assertNotNull($result);
        $this->assertEqualsWithDelta(13.0827, $result['latitude'], 0.0001);
        $this->assertEqualsWithDelta(80.2707, $result['longitude'], 0.0001);
    }

    public function test_extracts_coordinates_from_raw_string(): void
    {
        $raw = '12.9716, 77.5946';
        $result = CoordinateExtractor::extract($raw);

        $this->assertNotNull($result);
        $this->assertEqualsWithDelta(12.9716, $result['latitude'], 0.0001);
        $this->assertEqualsWithDelta(77.5946, $result['longitude'], 0.0001);
    }

    public function test_extracts_coordinates_from_place_data_parameter(): void
    {
        $url = 'https://www.google.com/maps/place/Decora+Furnishing/@21.2377132,72.8619897,15z/data=!4m7!3m6!1s0x3be04f715ce64fb5:0xcbf204bd86df7643!8m2!3d21.2392677!4d72.8754472!15sCghjdXJ0YWluc1oKIghjdXJ0YWluc5IBCnNvZmFfc3RvcmXgAQA';
        $result = CoordinateExtractor::extract($url);

        $this->assertNotNull($result);
        $this->assertEqualsWithDelta(21.2392677, $result['latitude'], 0.0001);
        $this->assertEqualsWithDelta(72.8754472, $result['longitude'], 0.0001);
    }

    public function test_returns_null_for_invalid_input(): void
    {
        $this->assertNull(CoordinateExtractor::extract(''));
        $this->assertNull(CoordinateExtractor::extract('https://google.com'));
        $this->assertNull(CoordinateExtractor::extract('random text'));
    }
}

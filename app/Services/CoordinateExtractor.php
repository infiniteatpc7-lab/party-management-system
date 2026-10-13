<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Throwable;

class CoordinateExtractor
{
    /**
     * Attempt to extract latitude and longitude from a string or Google Maps URL.
     *
     * @return array{latitude: ?float, longitude: ?float}|null
     */
    public static function extract(?string $input, bool $resolveRedirects = true): ?array
    {
        if (empty($input)) {
            return null;
        }

        $input = trim($input);

        // Fast path: extract directly from string/URL without HTTP requests
        $extracted = self::extractFromText($input);
        if ($extracted !== null) {
            return $extracted;
        }

        // If not found in text, and input is a valid URL, resolve shortened/redirect URLs (e.g. maps.app.goo.gl)
        if ($resolveRedirects && filter_var($input, FILTER_VALIDATE_URL)) {
            return self::resolveAndExtract($input);
        }

        return null;
    }

    /**
     * Extract coordinates from plain text or URL query string without network calls.
     *
     * @return array{latitude: ?float, longitude: ?float}|null
     */
    public static function extractFromText(string $input): ?array
    {
        // Case 1: Direct coordinates format "12.9715987, 77.5945627" or "12.9715987 77.5945627"
        if (preg_match('/^(-?\d{1,2}(?:\.\d+)?)[,\s]+(-?\d{1,3}(?:\.\d+)?)$/', $input, $matches)) {
            $lat = (float) $matches[1];
            $lng = (float) $matches[2];
            if (self::isValid($lat, $lng)) {
                return ['latitude' => $lat, 'longitude' => $lng];
            }
        }

        // Case 2: Google Maps data parameter !3dlat!4dlng (exact place pin location)
        if (preg_match('/!3d(-?\d{1,2}\.\d+)!4d(-?\d{1,3}\.\d+)/', $input, $matches)) {
            $lat = (float) $matches[1];
            $lng = (float) $matches[2];
            if (self::isValid($lat, $lng)) {
                return ['latitude' => $lat, 'longitude' => $lng];
            }
        }

        // Case 3: Google Maps query parameter ?q=lat,lng or &q=lat,lng or ll=lat,lng
        if (preg_match('/[?&](?:q|ll|destination|center)=(-?\d{1,2}\.\d+),(-?\d{1,3}\.\d+)/', $input, $matches)) {
            $lat = (float) $matches[1];
            $lng = (float) $matches[2];
            if (self::isValid($lat, $lng)) {
                return ['latitude' => $lat, 'longitude' => $lng];
            }
        }

        // Case 4: Google Maps @lat,lng format e.g. /@12.9715987,77.5945627
        if (preg_match('/@(-?\d{1,2}\.\d+),(-?\d{1,3}\.\d+)/', $input, $matches)) {
            $lat = (float) $matches[1];
            $lng = (float) $matches[2];
            if (self::isValid($lat, $lng)) {
                return ['latitude' => $lat, 'longitude' => $lng];
            }
        }

        return null;
    }

    /**
     * Follow HTTP redirects to resolve shortened URLs (e.g. maps.app.goo.gl, goo.gl/maps) and extract coordinates.
     *
     * @return array{latitude: ?float, longitude: ?float}|null
     */
    protected static function resolveAndExtract(string $url): ?array
    {
        try {
            // First attempt: inspect Location header without downloading the whole body
            $response = Http::timeout(5)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'])
                ->withoutRedirecting()
                ->get($url);

            if ($response->status() >= 300 && $response->status() < 400) {
                $location = $response->header('Location');
                if (! empty($location)) {
                    $fromLocation = self::extractFromText($location);
                    if ($fromLocation !== null) {
                        return $fromLocation;
                    }
                    if (filter_var($location, FILTER_VALIDATE_URL)) {
                        $url = $location;
                    }
                }
            }

            // Follow redirects to get the final effective URL
            $followed = Http::timeout(6)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'])
                ->get($url);

            $effectiveUri = (string) $followed->effectiveUri();
            $fromEffective = self::extractFromText($effectiveUri);
            if ($fromEffective !== null) {
                return $fromEffective;
            }

            // Search HTML body for pin coordinates
            $body = $followed->body();
            if (preg_match('/!3d(-?\d{1,2}\.\d+)!4d(-?\d{1,3}\.\d+)/', $body, $matches)) {
                $lat = (float) $matches[1];
                $lng = (float) $matches[2];
                if (self::isValid($lat, $lng)) {
                    return ['latitude' => $lat, 'longitude' => $lng];
                }
            }

            if (preg_match('/@(-?\d{1,2}\.\d+),(-?\d{1,3}\.\d+)/', $body, $matches)) {
                $lat = (float) $matches[1];
                $lng = (float) $matches[2];
                if (self::isValid($lat, $lng)) {
                    return ['latitude' => $lat, 'longitude' => $lng];
                }
            }
        } catch (Throwable) {
            // Silently return null on network failures or timeouts
        }

        return null;
    }

    /**
     * Check if latitude and longitude are within standard geographical boundaries.
     */
    public static function isValid(float $lat, float $lng): bool
    {
        return $lat >= -90.0 && $lat <= 90.0 && $lng >= -180.0 && $lng <= 180.0;
    }
}

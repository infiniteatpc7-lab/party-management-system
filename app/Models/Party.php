<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Party extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'party_name',
        'mobile_number',
        'map_link',
        'latitude',
        'longitude',
        'party_type',
        'priority_status',
        'is_lead',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'is_lead' => 'boolean',
        ];
    }

    /**
     * Check if party is marked as a lead.
     */
    public function isLead(): bool
    {
        return (bool) $this->is_lead;
    }

    /**
     * Check if party is marked as priority.
     */
    public function isPriority(): bool
    {
        return $this->priority_status === 'priority';
    }

    /**
     * Check if party is marked as new.
     */
    public function isNew(): bool
    {
        return $this->party_type === 'new';
    }

    /**
     * Google Maps navigation URL with validated destination coordinates.
     */
    public function getGoogleNavigationUrlAttribute(): ?string
    {
        if ($this->latitude === null || $this->longitude === null) {
            return $this->map_link ?: null;
        }

        return "https://www.google.com/maps/dir/?api=1&destination={$this->latitude},{$this->longitude}";
    }

    /**
     * Formatted human-readable distance.
     */
    public function getFormattedDistanceAttribute(): ?string
    {
        if (! isset($this->attributes['distance']) || $this->attributes['distance'] === null) {
            return null;
        }

        $distance = (float) $this->attributes['distance'];
        if ($distance < 1.0) {
            return round($distance * 1000).' m';
        }

        return number_format($distance, 1).' km';
    }

    /**
     * Scope query to search by party name or mobile number.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('party_name', 'like', "%{$term}%")
                ->orWhere('mobile_number', 'like', "%{$term}%");
        });
    }

    /**
     * Scope query by party type ('new' or 'old').
     */
    public function scopeType(Builder $query, ?string $type): Builder
    {
        if (empty($type) || $type === 'all') {
            return $query;
        }

        return $query->where('party_type', $type);
    }

    /**
     * Scope query by priority status ('priority' or 'non_priority').
     */
    public function scopePriority(Builder $query, ?string $priority): Builder
    {
        if (empty($priority) || $priority === 'all') {
            return $query;
        }

        return $query->where('priority_status', $priority);
    }

    /**
     * Scope query by lead status ('all', 'lead', or 'non_lead').
     */
    public function scopeLead(Builder $query, ?string $lead): Builder
    {
        if (empty($lead) || $lead === 'all') {
            return $query;
        }

        return $query->where('is_lead', $lead === 'lead');
    }

    /**
     * Scope query to calculate straight-line Haversine distance in kilometers.
     */
    public function scopeWithDistance(Builder $query, float $userLat, float $userLng): Builder
    {
        $lat = (float) $userLat;
        $lng = (float) $userLng;

        $rawSql = "(6371 * acos(cos(radians({$lat})) * cos(radians(latitude)) * cos(radians(longitude) - radians({$lng})) + sin(radians({$lat})) * sin(radians(latitude))))";

        return $query->select('parties.*')
            ->selectRaw("{$rawSql} AS distance")
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');
    }

    /**
     * Scope query within a maximum radius (in kilometers).
     */
    public function scopeWithinRadius(Builder $query, float $userLat, float $userLng, float $radiusKm): Builder
    {
        $lat = (float) $userLat;
        $lng = (float) $userLng;
        $radius = (float) $radiusKm;

        $rawSql = "(6371 * acos(cos(radians({$lat})) * cos(radians(latitude)) * cos(radians(longitude) - radians({$lng})) + sin(radians({$lat})) * sin(radians(latitude))))";

        return $query->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereRaw("{$rawSql} <= {$radius}");
    }
}

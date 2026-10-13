<?php

namespace Database\Factories;

use App\Models\Party;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Party>
 */
class PartyFactory extends Factory
{
    protected $model = Party::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'party_name' => fake()->company().' Banquet',
            'mobile_number' => '+91 '.fake()->numerify('98########'),
            'map_link' => 'https://maps.google.com/?q=12.9715987,77.5945627',
            'latitude' => fake()->latitude(12.85, 13.05),
            'longitude' => fake()->longitude(77.50, 77.75),
            'party_type' => fake()->randomElement(['new', 'old']),
            'priority_status' => fake()->randomElement(['priority', 'non_priority']),
            'is_lead' => false,
        ];
    }

    /**
     * Indicate that the party is high priority.
     */
    public function priority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority_status' => 'priority',
        ]);
    }

    /**
     * Indicate that the party is non-priority.
     */
    public function nonPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority_status' => 'non_priority',
        ]);
    }

    /**
     * Indicate that the party is new.
     */
    public function newType(): static
    {
        return $this->state(fn (array $attributes) => [
            'party_type' => 'new',
        ]);
    }

    /**
     * Indicate that the party is old.
     */
    public function oldType(): static
    {
        return $this->state(fn (array $attributes) => [
            'party_type' => 'old',
        ]);
    }
}

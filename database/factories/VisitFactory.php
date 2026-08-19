<?php

namespace Database\Factories;

use App\Models\Visit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Visit>
 */
class VisitFactory extends Factory
{
    protected $model = Visit::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ip_address' => fake()->unique()->ipv4(),
            'device_type' => fake()->randomElement(['desktop', 'mobile', 'tablet']),
            'browser' => fake()->randomElement(['Chrome', 'Firefox', 'Safari', 'Edge', null]),
            'network_type' => fake()->randomElement(['wifi', 'cellular', null]),
            'country' => fake()->optional(0.8)->country(),
            'region' => fake()->optional(0.8)->state(),
            'city' => fake()->optional(0.8)->city(),
            'page_url' => 'http://localhost/',
            'referrer' => null,
            'visited_at' => now()->subMinutes(rand(0, 60 * 24 * 7)),
        ];
    }
}

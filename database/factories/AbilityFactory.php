<?php

namespace Database\Factories;

use App\Models\Ability;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ability>
 */
class AbilityFactory extends Factory
{
    protected $model = Ability::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $apiId = 1;

        return [
            'api_id' => $apiId++,
            'name' => fake()->unique()->words(2, true),
            'effect' => fake()->paragraph(),
            'short_effect' => fake()->sentence(),
            'is_main_series' => true,
        ];
    }

    /**
     * Indicate that the ability is not main series.
     */
    public function notMainSeries(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_main_series' => false,
        ]);
    }
}

<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    protected $model = Item::class;

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
            'cost' => fake()->numberBetween(0, 100000),
            'fling_power' => fake()->optional(0.5)->numberBetween(10, 130),
            'fling_effect' => fake()->optional(0.3)->randomElement(['badly-poison', 'burn', 'berry-effect', 'herb-effect', 'paralyze', 'poison', 'flinch']),
            'category' => fake()->randomElement(['healing', 'status-cures', 'revival', 'pp-recovery', 'vitamins', 'stat-boosts', 'held-items', 'evolution', 'pokeballs', 'medicine', 'berries', 'machines', 'key-items']),
            'effect' => fake()->paragraph(),
            'short_effect' => fake()->sentence(),
            'flavor_text' => fake()->sentence(),
            'sprite' => 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/poke-ball.png',
        ];
    }

    /**
     * Held item.
     */
    public function heldItem(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'held-items',
        ]);
    }

    /**
     * Medicine item.
     */
    public function medicine(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'medicine',
        ]);
    }

    /**
     * Berry item.
     */
    public function berry(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'berries',
        ]);
    }
}

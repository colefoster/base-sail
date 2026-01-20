<?php

namespace Database\Factories;

use App\Models\Move;
use App\Models\Type;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Move>
 */
class MoveFactory extends Factory
{
    protected $model = Move::class;

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
            'power' => fake()->optional(0.8)->numberBetween(10, 200),
            'pp' => fake()->randomElement([5, 10, 15, 20, 25, 30, 35, 40]),
            'accuracy' => fake()->optional(0.9)->numberBetween(30, 100),
            'priority' => fake()->numberBetween(-7, 5),
            'type_id' => Type::factory(),
            'damage_class' => fake()->randomElement(['physical', 'special', 'status']),
            'effect_chance' => fake()->optional(0.3)->numberBetween(10, 100),
            'contest_type' => fake()->optional()->randomElement(['cool', 'beautiful', 'cute', 'clever', 'tough']),
            'generation' => fake()->randomElement(['generation-i', 'generation-ii', 'generation-iii', 'generation-iv', 'generation-v', 'generation-vi', 'generation-vii', 'generation-viii', 'generation-ix']),
            'effect' => fake()->paragraph(),
            'short_effect' => fake()->sentence(),
            'flavor_text' => fake()->sentence(),
            'target' => fake()->randomElement(['selected-pokemon', 'all-opponents', 'user', 'all-other-pokemon', 'entire-field']),
            'ailment' => fake()->optional(0.2)->randomElement(['paralysis', 'sleep', 'freeze', 'burn', 'poison', 'confusion']),
            'meta_category' => fake()->randomElement(['damage', 'ailment', 'net-good-stats', 'heal', 'damage+ailment', 'swagger', 'damage+lower', 'damage+raise', 'damage+heal', 'ohko', 'whole-field-effect', 'field-effect', 'force-switch', 'unique']),
            'min_hits' => null,
            'max_hits' => null,
            'min_turns' => null,
            'max_turns' => null,
            'drain' => 0,
            'healing' => 0,
            'crit_rate' => 0,
            'ailment_chance' => 0,
            'flinch_chance' => 0,
            'stat_chance' => 0,
        ];
    }

    /**
     * Physical attack move.
     */
    public function physical(): static
    {
        return $this->state(fn (array $attributes) => [
            'damage_class' => 'physical',
            'power' => fake()->numberBetween(40, 150),
        ]);
    }

    /**
     * Special attack move.
     */
    public function special(): static
    {
        return $this->state(fn (array $attributes) => [
            'damage_class' => 'special',
            'power' => fake()->numberBetween(40, 150),
        ]);
    }

    /**
     * Status move.
     */
    public function status(): static
    {
        return $this->state(fn (array $attributes) => [
            'damage_class' => 'status',
            'power' => null,
        ]);
    }

    /**
     * Multi-hit move.
     */
    public function multiHit(int $min = 2, int $max = 5): static
    {
        return $this->state(fn (array $attributes) => [
            'min_hits' => $min,
            'max_hits' => $max,
        ]);
    }
}

<?php

namespace Database\Factories;

use App\Models\Type;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Type>
 */
class TypeFactory extends Factory
{
    protected $model = Type::class;

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
            'name' => fake()->unique()->randomElement([
                'normal', 'fire', 'water', 'electric', 'grass', 'ice',
                'fighting', 'poison', 'ground', 'flying', 'psychic', 'bug',
                'rock', 'ghost', 'dragon', 'dark', 'steel', 'fairy',
            ]),
        ];
    }
}

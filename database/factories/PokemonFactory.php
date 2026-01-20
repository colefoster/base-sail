<?php

namespace Database\Factories;

use App\Models\Pokemon;
use App\Models\PokemonSpecies;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pokemon>
 */
class PokemonFactory extends Factory
{
    protected $model = Pokemon::class;

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
            'name' => fake()->unique()->word(),
            'height' => fake()->numberBetween(1, 200),
            'weight' => fake()->numberBetween(1, 10000),
            'base_experience' => fake()->numberBetween(36, 608),
            'is_default' => true,
            'species_id' => null,
            'sprite_front_default' => 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/'.fake()->numberBetween(1, 1000).'.png',
            'sprite_front_shiny' => null,
            'sprite_back_default' => null,
            'sprite_back_shiny' => null,
            'cry_latest' => null,
            'cry_legacy' => null,
        ];
    }

    /**
     * Indicate that this is an alternate form.
     */
    public function alternateForm(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => false,
        ]);
    }

    /**
     * Configure the model to have sprites.
     */
    public function withSprites(): static
    {
        return $this->state(function (array $attributes) {
            $id = $attributes['api_id'];

            return [
                'sprite_front_default' => "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/{$id}.png",
                'sprite_front_shiny' => "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/shiny/{$id}.png",
                'sprite_back_default' => "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/back/{$id}.png",
                'sprite_back_shiny' => "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/back/shiny/{$id}.png",
            ];
        });
    }

    /**
     * Configure the model to have a species.
     */
    public function withSpecies(): static
    {
        return $this->state(fn (array $attributes) => [
            'species_id' => PokemonSpecies::factory(),
        ]);
    }
}

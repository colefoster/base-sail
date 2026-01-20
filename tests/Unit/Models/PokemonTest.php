<?php

namespace Tests\Unit\Models;

use App\Models\Pokemon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PokemonTest extends TestCase
{
    use RefreshDatabase;

    public function test_pokemon_has_fillable_attributes(): void
    {
        $pokemon = new Pokemon;

        $this->assertEquals([
            'api_id',
            'name',
            'height',
            'weight',
            'base_experience',
            'is_default',
            'species_id',
            'sprite_front_default',
            'sprite_front_shiny',
            'sprite_back_default',
            'sprite_back_shiny',
            'cry_latest',
            'cry_legacy',
        ], $pokemon->getFillable());
    }

    public function test_pokemon_can_be_created(): void
    {
        $pokemon = Pokemon::create([
            'api_id' => 1,
            'name' => 'bulbasaur',
            'height' => 7,
            'weight' => 69,
            'base_experience' => 64,
            'is_default' => true,
        ]);

        $this->assertDatabaseHas('pokemon', [
            'name' => 'bulbasaur',
            'api_id' => 1,
        ]);
    }

    public function test_pokemon_has_types_relationship(): void
    {
        $pokemon = new Pokemon;

        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\BelongsToMany::class,
            $pokemon->types()
        );
    }

    public function test_pokemon_has_abilities_relationship(): void
    {
        $pokemon = new Pokemon;

        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\BelongsToMany::class,
            $pokemon->abilities()
        );
    }

    public function test_pokemon_has_moves_relationship(): void
    {
        $pokemon = new Pokemon;

        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\BelongsToMany::class,
            $pokemon->moves()
        );
    }
}

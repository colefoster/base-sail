<?php

namespace Database\Seeders;

use App\Models\PokemonSpecies;

class PokemonSpeciesSeeder extends BasePokeApiSeeder
{
    public function run(): void
    {
        $this->importWithPagination(
            endpoint: '/pokemon-species',
            progressKey: 'species',
            emoji: '🧬',
            resourceName: 'Pokemon Species',
            processItem: function (int $speciesId) {
                $speciesDetails = $this->api->fetch("/pokemon-species/{$speciesId}");

                PokemonSpecies::updateOrCreate(
                    ['api_id' => $speciesDetails['id']],
                    [
                        'name' => $speciesDetails['name'],
                        'base_happiness' => $speciesDetails['base_happiness'],
                        'capture_rate' => $speciesDetails['capture_rate'],
                        'color' => $speciesDetails['color']['name'] ?? null,
                        'gender_rate' => $speciesDetails['gender_rate'],
                        'hatch_counter' => $speciesDetails['hatch_counter'],
                        'is_baby' => $speciesDetails['is_baby'] ?? false,
                        'is_legendary' => $speciesDetails['is_legendary'] ?? false,
                        'is_mythical' => $speciesDetails['is_mythical'] ?? false,
                        'habitat' => $speciesDetails['habitat']['name'] ?? null,
                        'shape' => $speciesDetails['shape']['name'] ?? null,
                        'generation' => $speciesDetails['generation']['name'] ?? null,
                    ]
                );

                $this->advanceProgress("Importing species: {$speciesDetails['name']}");
            }
        );

        $this->command->info("Pokemon Species imported: " . PokemonSpecies::count());
    }
}

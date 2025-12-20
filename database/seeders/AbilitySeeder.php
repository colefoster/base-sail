<?php

namespace Database\Seeders;

use App\Models\Ability;

class AbilitySeeder extends BasePokeApiSeeder
{
    public function run(): void
    {
        $this->importWithPagination(
            endpoint: '/ability',
            progressKey: 'abilities',
            emoji: '⚡',
            resourceName: 'Abilities',
            processItem: function (int $abilityId) {
                $abilityDetails = $this->api->fetch("/ability/{$abilityId}");
                $effectEntry = $this->api->getEnglishEffect($abilityDetails['effect_entries'] ?? []);

                Ability::updateOrCreate(
                    ['api_id' => $abilityDetails['id']],
                    [
                        'name' => $abilityDetails['name'],
                        'effect' => $effectEntry['effect'] ?? null,
                        'short_effect' => $effectEntry['short_effect'] ?? null,
                        'is_main_series' => $abilityDetails['is_main_series'] ?? true,
                    ]
                );

                $this->advanceProgress("Importing ability: {$abilityDetails['name']}");
            }
        );

        $this->command->info("Abilities imported: " . Ability::count());
    }
}

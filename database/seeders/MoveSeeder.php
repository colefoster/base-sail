<?php

namespace Database\Seeders;

use App\Models\Move;
use App\Models\Type;

class MoveSeeder extends BasePokeApiSeeder
{
    public function run(): void
    {
        $this->importWithPagination(
            endpoint: '/move',
            progressKey: 'moves',
            emoji: '🥊',
            resourceName: 'Moves',
            processItem: function (int $moveId) {
                $moveDetails = $this->api->fetch("/move/{$moveId}");

                $typeId = null;
                if (isset($moveDetails['type']['name'])) {
                    $type = Type::where('name', $moveDetails['type']['name'])->first();
                    $typeId = $type?->id;
                }

                $effectEntry = $this->api->getEnglishEffect($moveDetails['effect_entries'] ?? []);
                $flavorTextEntry = $this->api->getEnglishFlavorText($moveDetails['flavor_text_entries'] ?? []);
                $meta = $moveDetails['meta'] ?? [];

                Move::updateOrCreate(
                    ['api_id' => $moveDetails['id']],
                    [
                        'name' => $moveDetails['name'],
                        'power' => $moveDetails['power'],
                        'pp' => $moveDetails['pp'],
                        'accuracy' => $moveDetails['accuracy'],
                        'priority' => $moveDetails['priority'],
                        'type_id' => $typeId,
                        'damage_class' => $moveDetails['damage_class']['name'] ?? null,
                        'effect_chance' => $moveDetails['effect_chance'] ?? null,
                        'contest_type' => $moveDetails['contest_type']['name'] ?? null,
                        'generation' => $moveDetails['generation']['name'] ?? null,
                        'effect' => $effectEntry['effect'] ?? null,
                        'short_effect' => $effectEntry['short_effect'] ?? null,
                        'flavor_text' => $flavorTextEntry['flavor_text'] ?? null,
                        'target' => $moveDetails['target']['name'] ?? null,
                        'ailment' => $meta['ailment']['name'] ?? null,
                        'meta_category' => $meta['category']['name'] ?? null,
                        'min_hits' => $meta['min_hits'] ?? null,
                        'max_hits' => $meta['max_hits'] ?? null,
                        'min_turns' => $meta['min_turns'] ?? null,
                        'max_turns' => $meta['max_turns'] ?? null,
                        'drain' => $meta['drain'] ?? null,
                        'healing' => $meta['healing'] ?? null,
                        'crit_rate' => $meta['crit_rate'] ?? null,
                        'ailment_chance' => $meta['ailment_chance'] ?? null,
                        'flinch_chance' => $meta['flinch_chance'] ?? null,
                        'stat_chance' => $meta['stat_chance'] ?? null,
                    ]
                );

                $this->advanceProgress("Importing move: {$moveDetails['name']}");
            }
        );

        $this->command->info("Moves imported: " . Move::count());
    }
}

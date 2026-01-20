<?php

namespace Database\Seeders;

use App\Models\Item;

class ItemSeeder extends BasePokeApiSeeder
{
    public function run(): void
    {
        $this->importWithPagination(
            endpoint: '/item',
            progressKey: 'items',
            emoji: '🎒',
            resourceName: 'Items',
            processItem: function (int $itemId) {
                $itemDetails = $this->api->fetch("/item/{$itemId}");
                $effectEntry = $this->api->getEnglishEffect($itemDetails['effect_entries'] ?? []);
                $flavorTextEntry = $this->api->getEnglishFlavorText($itemDetails['flavor_text_entries'] ?? []);

                Item::updateOrCreate(
                    ['api_id' => $itemDetails['id']],
                    [
                        'name' => $itemDetails['name'],
                        'cost' => $itemDetails['cost'] ?? null,
                        'fling_power' => $itemDetails['fling_power'] ?? null,
                        'fling_effect' => $itemDetails['fling_effect']['name'] ?? null,
                        'category' => $itemDetails['category']['name'] ?? null,
                        'effect' => $effectEntry['effect'] ?? null,
                        'short_effect' => $effectEntry['short_effect'] ?? null,
                        'flavor_text' => $flavorTextEntry['text'] ?? null,
                        'sprite' => $itemDetails['sprites']['default'] ?? null,
                    ]
                );

                $this->advanceProgress("Importing item: {$itemDetails['name']}");
            }
        );

        $this->command->info('Items imported: '.Item::count());
    }
}

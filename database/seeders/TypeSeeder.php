<?php

namespace Database\Seeders;

use App\Models\Type;

class TypeSeeder extends BasePokeApiSeeder
{
    public function run(): void
    {
        $this->importSingleBatch(
            endpoint: '/type',
            progressKey: 'types',
            emoji: '📋',
            resourceName: 'Types',
            processItem: function (int $typeId) {
                $typeDetails = $this->api->fetch("/type/{$typeId}");

                Type::updateOrCreate(
                    ['api_id' => $typeDetails['id']],
                    ['name' => $typeDetails['name']]
                );

                $this->advanceProgress("Importing type: {$typeDetails['name']}");
            }
        );

        $this->command->info('Types imported: '.Type::count());
    }
}

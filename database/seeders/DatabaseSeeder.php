<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Get parallel processing options with defaults
        $threads = (int) ($this->command->option('threads') ?? 4);
        $delay = (int) ($this->command->option('delay') ?? 100);
        $limit = (int) ($this->command->option('limit') ?? 100);

        // Seed users first
        $this->command->info('👥 Creating users...');
        $this->call(UserSeeder::class);
        $this->command->newLine();

        // Seed Pokemon data from PokeAPI with parallel processing
        // Order matters due to foreign key dependencies
        $commands = [
            ['seed:types', 'Types'],
            ['seed:abilities', 'Abilities'],
            ['seed:moves', 'Moves'],
            ['seed:items', 'Items'],
            ['seed:pokemon-species', 'Pokemon Species'],
            ['seed:evolution-chains', 'Evolution Chains'],
            ['seed:pokemon', 'Pokemon'],
        ];

        foreach ($commands as [$command, $label]) {
            $this->command->info("Seeding {$label}...");

            $result = $this->command->call($command, [
                '--threads' => $threads,
                '--delay' => $delay,
                '--limit' => $limit,
            ]);

            if ($result !== 0) {
                $this->command->error("Failed to seed {$label}");

                return;
            }

            $this->command->newLine();
        }

        $this->command->newLine();
        $this->command->info('Database seeding completed successfully!');
    }
}

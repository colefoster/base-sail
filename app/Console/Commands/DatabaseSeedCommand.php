<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\ConnectionResolverInterface as Resolver;
use Symfony\Component\Console\Input\InputOption;

class DatabaseSeedCommand extends Command
{
    protected $name = 'db:seed';

    protected $description = 'Seed the database with records (with parallel processing support)';

    protected Resolver $resolver;

    public function __construct(Resolver $resolver)
    {
        parent::__construct();
        $this->resolver = $resolver;
    }

    public function handle(): int
    {
        if (!$this->confirmToProceed()) {
            return 1;
        }

        $database = $this->input->getOption('database');
        $this->resolver->setDefaultConnection($database);

        $threads = (int) $this->option('threads');
        $delay = (int) $this->option('delay');
        $limit = (int) $this->option('limit');

        $this->info('🚀 Starting database seeding...');
        if ($threads > 1) {
            $this->info("⚡ Parallel mode enabled: {$threads} workers | Delay: {$delay}ms | Limit: {$limit}");
        }
        $this->newLine();

        // Get the seeder class
        $class = $this->input->getOption('class');

        // Create seeder instance
        $seeder = $this->laravel->make($class);

        // Run the seeder (it will access options via $this->command->option())
        $seeder->__invoke();

        $this->newLine();
        $this->info('✅ Database seeding completed successfully.');

        return 0;
    }

    protected function getOptions(): array
    {
        return [
            ['class', null, InputOption::VALUE_OPTIONAL, 'The class name of the root seeder', 'Database\\Seeders\\DatabaseSeeder'],
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to seed'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production'],
            ['threads', null, InputOption::VALUE_OPTIONAL, 'Number of parallel workers', '4'],
            ['delay', null, InputOption::VALUE_OPTIONAL, 'Delay between requests in milliseconds', '100'],
            ['limit', null, InputOption::VALUE_OPTIONAL, 'Items per page', '100'],
        ];
    }
}

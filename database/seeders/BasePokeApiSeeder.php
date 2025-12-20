<?php

namespace Database\Seeders;

use App\Services\PokeApiService;
use App\Services\SeederProgressService;
use Illuminate\Database\Seeder;
use Symfony\Component\Console\Helper\ProgressBar;

abstract class BasePokeApiSeeder extends Seeder
{
    protected PokeApiService $api;
    protected SeederProgressService $progress;

    public function __construct()
    {
        $this->api = app(PokeApiService::class);
        $this->progress = app(SeederProgressService::class);
    }

    /**
     * Import resources with pagination.
     */
    protected function importWithPagination(
        string $endpoint,
        string $progressKey,
        string $emoji,
        string $resourceName,
        callable $processItem,
        int $limit = 100
    ): void {
        $this->command->info("{$emoji} Importing {$resourceName}...");

        try {
            $totalCount = $this->getTotalCount($endpoint);
            $this->progress->start($progressKey, $totalCount);

            $offset = 0;
            do {
                $response = $this->api->fetch("{$endpoint}?limit={$limit}&offset={$offset}");
                $items = $response['results'] ?? [];

                if (empty($items)) {
                    break;
                }

                $this->processBatch($items, $processItem, $resourceName);
                $offset += $limit;

            } while (!empty($items));

            $this->progress->complete($progressKey);
        } catch (\Exception $e) {
            $this->command->error("❌ {$resourceName} import failed: " . $e->getMessage());
            $this->progress->error($e->getMessage());
        }
    }

    /**
     * Import resources without pagination (single batch).
     */
    protected function importSingleBatch(
        string $endpoint,
        string $progressKey,
        string $emoji,
        string $resourceName,
        callable $processItem,
        int $limit = 100
    ): void {
        $this->command->info("{$emoji} Importing {$resourceName}...");

        try {
            $response = $this->api->fetch("{$endpoint}?limit={$limit}&offset=0");
            $items = $response['results'] ?? [];

            $this->progress->start($progressKey, count($items));
            $this->processBatch($items, $processItem, $resourceName);
            $this->progress->complete($progressKey);
        } catch (\Exception $e) {
            $this->command->error("❌ {$resourceName} import failed: " . $e->getMessage());
            $this->progress->error($e->getMessage());
        }
    }

    /**
     * Process a batch of items with progress bar.
     */
    protected function processBatch(array $items, callable $processItem, string $resourceName): void
    {
        $bar = $this->createProgressBar(count($items));
        $bar->start();

        foreach ($items as $item) {
            try {
                $itemId = $this->api->extractIdFromUrl($item['url']);
                $processItem($itemId);

                $bar->advance();
                $this->rateLimit();
            } catch (\Exception $e) {
                $this->command->warn("\nError importing {$resourceName}: " . $e->getMessage());
                $this->progress->error($e->getMessage());
            }
        }

        $bar->finish();
        $this->command->newLine();
    }

    /**
     * Get total count of resources.
     */
    protected function getTotalCount(string $endpoint): int
    {
        $response = $this->api->fetch("{$endpoint}?limit=1&offset=0");
        return $response['count'] ?? 0;
    }

    /**
     * Create a progress bar.
     */
    protected function createProgressBar(int $max): ProgressBar
    {
        return $this->command->getOutput()->createProgressBar($max);
    }

    /**
     * Apply rate limiting between API requests.
     */
    protected function rateLimit(int $microseconds = 100000): void
    {
        usleep($microseconds);
    }

    /**
     * Advance progress tracker with success status.
     */
    protected function advanceProgress(string $message): void
    {
        $this->progress->advance($message);
        $this->progress->success();
    }
}

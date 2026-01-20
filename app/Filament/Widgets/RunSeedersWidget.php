<?php

namespace App\Filament\Widgets;

use App\Models\Ability;
use App\Models\Evolution;
use App\Models\EvolutionChain;
use App\Models\Item;
use App\Models\Move;
use App\Models\Pokemon;
use App\Models\PokemonGameIndex;
use App\Models\PokemonSpecies;
use App\Models\PokemonStat;
use App\Models\Type;
use App\Services\SeederProgressService;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Widgets\Widget;
use Livewire\Attributes\On;

class RunSeedersWidget extends Widget implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected string $view = 'filament.widgets.run-seeders-widget';

    protected int|string|array $columnSpan = 'full';

    public ?array $data = [];

    public static function canView(): bool
    {
        // Only show on Admin Tools page, not on dashboard
        return false;
    }

    public bool $isImporting = false;

    public array $progress = [];

    public int $successCount = 0;

    public int $errorCount = 0;

    public function mount(): void
    {
        $this->resetProgress();
        $this->form->fill([
            'delay' => 100,
            'limit' => 50,
            'maxPokemon' => null,
            'importTypes' => false,
            'importAbilities' => false,
            'importMoves' => false,
            'importItems' => false,
            'importSpecies' => false,
            'importEvolutionChains' => false,
            'importPokemon' => false,
        ]);
    }

    protected function getHeaderActions(): array
    {
        if ($this->isImporting) {
            return [
                Action::make('stopImport')
                    ->label('Stop Import')
                    ->color('danger')
                    ->icon('heroicon-o-stop-circle')
                    ->action(fn () => $this->stopImport()),
            ];
        }

        $actions = [];
        $counts = $this->getCounts();

        if ($counts['types'] > 0) {
            $actions[] = Action::make('clearTypes')
                ->label('Clear Types')
                ->color('danger')
                ->outlined()
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->modalDescription('Are you sure you want to delete all Types? This may affect Moves and Pokemon.')
                ->action(function () {
                    Type::query()->delete();
                    Notification::make()
                        ->title('Data Cleared')
                        ->success()
                        ->body('Types have been deleted successfully')
                        ->send();
                });
        }

        if ($counts['pokemon'] > 0) {
            $actions[] = Action::make('clearPokemon')
                ->label('Clear Pokemon')
                ->color('danger')
                ->outlined()
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->modalDescription('Are you sure you want to delete all Pokemon and related data?')
                ->action(function () {
                    $this->clearPokemon();
                    Notification::make()
                        ->title('Data Cleared')
                        ->success()
                        ->body('Pokemon have been deleted successfully')
                        ->send();
                });
        }

        return $actions;
    }

    public function getClearAbilitiesAction(): Action
    {
        return Action::make('clearAbilities')
            ->label('Clear Abilities ('.Ability::count().')')
            ->color('danger')
            ->outlined()
            ->icon('heroicon-o-trash')
            ->requiresConfirmation()
            ->modalDescription('Delete all Abilities?')
            ->action(function () {
                Ability::query()->delete();
                Notification::make()
                    ->title('Data Cleared')
                    ->success()
                    ->body('Abilities have been deleted successfully')
                    ->send();
            })
            ->visible(fn () => Ability::count() > 0);
    }

    public function getClearMovesAction(): Action
    {
        return Action::make('clearMoves')
            ->label('Clear Moves ('.Move::count().')')
            ->color('danger')
            ->outlined()
            ->icon('heroicon-o-trash')
            ->requiresConfirmation()
            ->modalDescription('Delete all Moves?')
            ->action(function () {
                Move::query()->delete();
                Notification::make()
                    ->title('Data Cleared')
                    ->success()
                    ->body('Moves have been deleted successfully')
                    ->send();
            })
            ->visible(fn () => Move::count() > 0);
    }

    public function getClearItemsAction(): Action
    {
        return Action::make('clearItems')
            ->label('Clear Items ('.Item::count().')')
            ->color('danger')
            ->outlined()
            ->icon('heroicon-o-trash')
            ->requiresConfirmation()
            ->modalDescription('Delete all Items?')
            ->action(function () {
                Item::query()->delete();
                Notification::make()
                    ->title('Data Cleared')
                    ->success()
                    ->body('Items have been deleted successfully')
                    ->send();
            })
            ->visible(fn () => Item::count() > 0);
    }

    public function getClearSpeciesAction(): Action
    {
        return Action::make('clearSpecies')
            ->label('Clear Species ('.PokemonSpecies::count().')')
            ->color('danger')
            ->outlined()
            ->icon('heroicon-o-trash')
            ->requiresConfirmation()
            ->modalDescription('Delete all Species?')
            ->action(function () {
                PokemonSpecies::query()->delete();
                Notification::make()
                    ->title('Data Cleared')
                    ->success()
                    ->body('Species have been deleted successfully')
                    ->send();
            })
            ->visible(fn () => PokemonSpecies::count() > 0);
    }

    public function getClearEvolutionChainsAction(): Action
    {
        return Action::make('clearEvolutionChains')
            ->label('Clear Evolutions ('.EvolutionChain::count().')')
            ->color('danger')
            ->outlined()
            ->icon('heroicon-o-trash')
            ->requiresConfirmation()
            ->modalDescription('Delete all Evolution Chains?')
            ->action(function () {
                Evolution::query()->delete();
                EvolutionChain::query()->delete();
                Notification::make()
                    ->title('Data Cleared')
                    ->success()
                    ->body('Evolution Chains have been deleted successfully')
                    ->send();
            })
            ->visible(fn () => EvolutionChain::count() > 0);
    }

    protected function getFormSchema(): array
    {
        $counts = $this->getCounts();

        return [
            Section::make('Import Configuration')
                ->description('Configure import settings and select data types to import')
                ->schema([
                    Section::make('API Request Parameters')
                        ->schema([
                            Grid::make(3)
                                ->schema([
                                    TextInput::make('delay')
                                        ->label('Delay (ms)')
                                        ->helperText('Delay between API requests')
                                        ->numeric()
                                        ->default(100)
                                        ->minValue(0)
                                        ->maxValue(2000)
                                        ->required(),

                                    TextInput::make('limit')
                                        ->label('Limit per Page')
                                        ->helperText('Items fetched per request')
                                        ->numeric()
                                        ->default(50)
                                        ->minValue(1)
                                        ->maxValue(100)
                                        ->required(),

                                    TextInput::make('maxPokemon')
                                        ->label('Max Pokemon')
                                        ->helperText('Leave empty for all')
                                        ->numeric()
                                        ->minValue(1)
                                        ->maxValue(10000),
                                ]),
                        ])
                        ->collapsible()
                        ->collapsed(),

                    Section::make('Select Data Types')
                        ->description('Choose which data types to import (dependencies will be validated)')
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    Checkbox::make('importTypes')
                                        ->label('Types')
                                        ->helperText($counts['types'].' in database')
                                        ->disabled(! $this->canImportTypes()),

                                    Checkbox::make('importAbilities')
                                        ->label('Abilities')
                                        ->helperText($counts['abilities'].' in database')
                                        ->disabled(! $this->canImportAbilities()),

                                    Checkbox::make('importMoves')
                                        ->label('Moves')
                                        ->helperText($this->canImportMoves() ? $counts['moves'].' in database' : $this->getDependencyMessage('moves'))
                                        ->disabled(! $this->canImportMoves()),

                                    Checkbox::make('importItems')
                                        ->label('Items')
                                        ->helperText($counts['items'].' in database')
                                        ->disabled(! $this->canImportItems()),

                                    Checkbox::make('importSpecies')
                                        ->label('Pokemon Species')
                                        ->helperText($counts['species'].' in database')
                                        ->disabled(! $this->canImportSpecies()),

                                    Checkbox::make('importEvolutionChains')
                                        ->label('Evolution Chains')
                                        ->helperText($this->canImportEvolutionChains() ? EvolutionChain::count().' in database' : $this->getDependencyMessage('evolution_chains'))
                                        ->disabled(! $this->canImportEvolutionChains()),

                                    Checkbox::make('importPokemon')
                                        ->label('Pokemon')
                                        ->helperText($this->canImportPokemon() ? $counts['pokemon'].' in database' : $this->getDependencyMessage('pokemon'))
                                        ->disabled(! $this->canImportPokemon())
                                        ->columnSpan(2),
                                ]),
                        ])
                        ->collapsible()
                        ->collapsed(),

                    Actions::make([
                        Action::make('startImport')
                            ->label('Start Import')
                            ->icon('heroicon-o-arrow-down-tray')
                            ->size('lg')
                            ->submit('startImport'),
                    ])
                        ->alignEnd()
                        ->fullWidth(),
                ])
                ->collapsible(),
        ];
    }

    protected function getFormStatePath(): string
    {
        return 'data';
    }

    public function startImport(): void
    {
        $data = $this->form->getState();

        // Validate at least one type is selected
        if (! ($data['importTypes'] ?? false) && ! ($data['importAbilities'] ?? false) && ! ($data['importMoves'] ?? false) &&
            ! ($data['importItems'] ?? false) && ! ($data['importSpecies'] ?? false) && ! ($data['importEvolutionChains'] ?? false) &&
            ! ($data['importPokemon'] ?? false)) {
            Notification::make()
                ->title('Import Error')
                ->danger()
                ->body('Please select at least one data type to import')
                ->send();

            return;
        }

        $this->isImporting = true;
        $this->resetProgress();

        // Reset the progress service
        app(SeederProgressService::class)->reset();

        // Build the command to run seeders in the background
        $seeders = [];
        if ($data['importTypes'] ?? false) {
            $seeders[] = 'db:seed --class=Database\\\\Seeders\\\\TypeSeeder';
        }
        if ($data['importAbilities'] ?? false) {
            $seeders[] = 'db:seed --class=Database\\\\Seeders\\\\AbilitySeeder';
        }
        if ($data['importMoves'] ?? false) {
            $seeders[] = 'db:seed --class=Database\\\\Seeders\\\\MoveSeeder';
        }
        if ($data['importItems'] ?? false) {
            $seeders[] = 'db:seed --class=Database\\\\Seeders\\\\ItemSeeder';
        }
        if ($data['importSpecies'] ?? false) {
            $seeders[] = 'db:seed --class=Database\\\\Seeders\\\\PokemonSpeciesSeeder';
        }
        if ($data['importEvolutionChains'] ?? false) {
            $seeders[] = 'db:seed --class=Database\\\\Seeders\\\\EvolutionChainSeeder';
        }
        if ($data['importPokemon'] ?? false) {
            $seeders[] = 'db:seed --class=Database\\\\Seeders\\\\PokemonSeeder';
        }

        // Build command to run all seeders sequentially
        $seederCommands = array_map(function ($seeder) {
            return sprintf('php %s/artisan %s', base_path(), $seeder);
        }, $seeders);

        $command = sprintf(
            '(%s && php %s/artisan cache:put seeder_progress \'{"progress":{"current_step":"complete","total":0,"current":0,"message":"Import complete!"},"successCount":0,"errorCount":0}\' 3600) > /dev/null 2>&1 &',
            implode(' && ', $seederCommands),
            base_path()
        );

        // Run in background
        exec($command);
    }

    public function updateProgress(): void
    {
        if (! $this->isImporting) {
            return;
        }

        // Read progress from cache
        $progressService = app(SeederProgressService::class);
        $data = $progressService->getProgress();

        $this->progress = $data['progress'] ?? $this->progress;
        $this->successCount = $data['successCount'] ?? 0;
        $this->errorCount = $data['errorCount'] ?? 0;

        // Auto-stop when complete
        if (($this->progress['current_step'] ?? '') === 'complete') {
            $this->stopImport();
            Notification::make()
                ->title('Import Complete!')
                ->success()
                ->body('Data has been imported successfully')
                ->duration(5000)
                ->send();
        }
    }

    public function stopImport(): void
    {
        $this->isImporting = false;
    }

    private function resetProgress(): void
    {
        $this->progress = [
            'current_step' => 'start',
            'total' => 0,
            'current' => 0,
            'message' => 'Ready to import',
        ];
        $this->successCount = 0;
        $this->errorCount = 0;
    }

    public function getProgressPercentage(): float
    {
        if (! isset($this->progress['total']) || $this->progress['total'] === 0) {
            return 0;
        }

        return round(($this->progress['current'] / $this->progress['total']) * 100, 1);
    }

    public function getStepLabel(): string
    {
        return match ($this->progress['current_step'] ?? 'start') {
            'start' => 'Starting...',
            'types' => 'Importing Types',
            'abilities' => 'Importing Abilities',
            'moves' => 'Importing Moves',
            'items' => 'Importing Items',
            'species' => 'Importing Pokemon Species',
            'evolution_chains' => 'Importing Evolution Chains',
            'pokemon' => 'Importing Pokemon',
            'complete' => 'Import Complete!',
            default => 'Ready',
        };
    }

    // Dependency checking methods
    public function canImportTypes(): bool
    {
        return true; // Types have no dependencies
    }

    public function canImportAbilities(): bool
    {
        return true; // Abilities have no dependencies
    }

    public function canImportMoves(): bool
    {
        return Type::count() > 0; // Moves depend on Types
    }

    public function canImportItems(): bool
    {
        return true; // Items have no dependencies
    }

    public function canImportSpecies(): bool
    {
        return true; // Species have no dependencies
    }

    public function canImportEvolutionChains(): bool
    {
        return PokemonSpecies::count() > 0; // Evolution chains depend on Species
    }

    public function canImportPokemon(): bool
    {
        return Type::count() > 0
            && Ability::count() > 0
            && Move::count() > 0
            && Item::count() > 0
            && PokemonSpecies::count() > 0; // Pokemon depend on everything
    }

    // Get dependency message for disabled imports
    public function getDependencyMessage(string $type): string
    {
        return match ($type) {
            'moves' => Type::count() === 0 ? 'Requires Types to be imported first' : '',
            'evolution_chains' => PokemonSpecies::count() === 0 ? 'Requires Species to be imported first' : '',
            'pokemon' => $this->getPokemonDependencies(),
            default => '',
        };
    }

    private function getPokemonDependencies(): string
    {
        $missing = [];
        if (Type::count() === 0) {
            $missing[] = 'Types';
        }
        if (Ability::count() === 0) {
            $missing[] = 'Abilities';
        }
        if (Move::count() === 0) {
            $missing[] = 'Moves';
        }
        if (Item::count() === 0) {
            $missing[] = 'Items';
        }
        if (PokemonSpecies::count() === 0) {
            $missing[] = 'Species';
        }

        return ! empty($missing) ? 'Requires: '.implode(', ', $missing) : '';
    }

    // Get current database counts
    public function getCounts(): array
    {
        return [
            'types' => Type::count(),
            'abilities' => Ability::count(),
            'moves' => Move::count(),
            'items' => Item::count(),
            'species' => PokemonSpecies::count(),
            'pokemon' => Pokemon::count(),
        ];
    }

    // Mass clear/delete method for Pokemon (used by action)
    private function clearPokemon(): void
    {
        // Clear related data first
        PokemonStat::query()->delete();
        PokemonGameIndex::query()->delete();

        // Clear pivot tables (handled by sync)
        \DB::table('pokemon_type')->truncate();
        \DB::table('ability_pokemon')->truncate();
        \DB::table('move_pokemon')->truncate();
        \DB::table('pokemon_item')->truncate();

        // Clear pokemon
        Pokemon::query()->delete();
    }
}

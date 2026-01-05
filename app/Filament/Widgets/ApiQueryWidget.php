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

class ApiQueryWidget extends Widget implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    protected string $view = 'filament.widgets.api-query-widget';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return true;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('testAction')
                ->label('Test Action')
                ->color('primary')
                ->icon('heroicon-o-beaker')
                ->action(fn() => 'Hello World!'),
        ];
    }
}

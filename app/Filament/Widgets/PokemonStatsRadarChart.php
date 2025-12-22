<?php

namespace App\Filament\Widgets;

use App\Models\Pokemon;
use Filament\Widgets\ChartWidget;

class PokemonStatsRadarChart extends ChartWidget
{
    public Pokemon $record;

    protected ?string $heading = 'Base Stats';

    protected ?string $maxHeight = '400px';

    protected static bool $isLazy = false;

    protected function getType(): string
    {
        return 'radar';
    }

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => $this->record->name,
                    'data' => [
                        $this->record->hpStat ?? 0,
                        $this->record->attackStat ?? 0,
                        $this->record->defenseStat ?? 0,
                        $this->record->specialAttackStat ?? 0,
                        $this->record->specialDefenseStat ?? 0,
                        $this->record->speedStat ?? 0,
                    ],
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                    'pointBackgroundColor' => 'rgb(59, 130, 246)',
                    'pointBorderColor' => '#fff',
                    'pointHoverBackgroundColor' => '#fff',
                    'pointHoverBorderColor' => 'rgb(59, 130, 246)',
                ],
            ],
            'labels' => ['HP', 'Attack', 'Defense', 'Sp. Atk', 'Sp. Def', 'Speed'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'r' => [
                    'beginAtZero' => true,
                    'min' => 0,
                    'max' => 255,
                    'ticks' => [
                        'stepSize' => 50,
                    ],
                ],
                'x' => [
                    'display' => false,
                ],
                'y' => [
                    'display' => false,
                ]
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'maintainAspectRatio' => false,
        ];
    }
}

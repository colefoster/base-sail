<?php

namespace App\Filament\Widgets;

use App\Models\Pokemon;
use App\Providers\Filament\MyAppColors;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class PokemonStatsRadarChart extends ChartWidget
{
    protected  ?string $heading = 'Base Stats';

    protected  ?string $maxHeight = '300px';

    public ?Pokemon $record = null;

    public static function canView(): bool
    {
        // This widget is only meant to be used within resource pages with a record
        // The Livewire component version is used in Pokemon pages
        return false;
    }

    protected function getData(): array
    {
        if (! $this->record) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $typeColor = $this->getTypeColor();
        $rgb = $this->hexToRgb($typeColor);
        $rgbString = "rgb({$rgb[0]}, {$rgb[1]}, {$rgb[2]})";
        $rgbaString = "rgba({$rgb[0]}, {$rgb[1]}, {$rgb[2]}, 0.2)";

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
                    'backgroundColor' => $rgbaString,
                    'borderColor' => $rgbString,
                    'borderWidth' => 2,
                    'pointBackgroundColor' => $rgbString,
                    'pointBorderColor' => '#fff',
                    'pointHoverBackgroundColor' => '#fff',
                    'pointHoverBorderColor' => $rgbString,
                ],
            ],
            'labels' => ['HP', 'ATK', 'DEF', 'SPA', 'SPD', 'SPE'],
        ];
    }

    protected function getType(): string
    {
        return 'radar';
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<JS
        {
            scales: {
                r: {
                    beginAtZero: true,
                    min: 0,
                    max: 200,
                    ticks: {
                        stepSize: 100,
                        display: false,
                    },
                    grid: {
                        color: '#FF0000',
                    },
                    angleLines: {
                        color: '#FF00FF',
                    },
                    pointLabels: {
                        color: '#00FF00',
                        font: {
                            size: 12,
                            weight: 500,
                        },
                    },
                },
            },
            plugins: {
                legend: {
                    display: false,
                },
            },
            maintainAspectRatio: true,
        }
        JS);
    }

    protected function getTypeColor(): string
    {
        if (! $this->record) {
            return '#3b82f6';
        }

        $primaryType = $this->record->types()->wherePivot('slot', 1)->first();

        if (! $primaryType) {
            return '#3b82f6';
        }

        $colors = MyAppColors::loadAppColors();
        $typeName = strtolower($primaryType->name);

        return $colors[$typeName] ?? '#3b82f6';
    }

    protected function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }
}

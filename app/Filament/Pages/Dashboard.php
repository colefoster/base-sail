<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DatabaseStatsOverview;
use App\Filament\Widgets\RunSeedersWidget;
use App\Filament\Widgets\ApiQueryWidget;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Artisan;

class Dashboard extends BaseDashboard
{
    protected function getHeaderActions(): array
    {
        return [
            Action::make('apiTester')
                ->label('API Tester')
                ->icon('heroicon-o-globe-alt')
                ->url(fn () => route('filament.admin.pages.tools-api-tester'))
                ->color('gray'),
        ];
    }

    public function getWidgets(): array
    {
        return [
            DatabaseStatsOverview::class,
            RunSeedersWidget::class,
            ApiQueryWidget::class,

        ];
    }
}

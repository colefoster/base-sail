<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ApiQueryWidget;
use App\Filament\Widgets\RunSeedersWidget;
use Filament\Pages\Page;

class AdminTools extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected string $view = 'filament.pages.admin-tools';

    protected static ?string $navigationLabel = 'Admin Tools';

    protected static ?string $title = 'Admin Tools';

    protected static ?int $navigationSort = 100;

    public static function canAccess(): bool
    {
        // Only admins can access this page
        return auth()->user()?->is_admin ?? false;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            RunSeedersWidget::class,
            ApiQueryWidget::class,
        ];
    }

    public function getWidgets(): array
    {
        return $this->getHeaderWidgets();
    }
}
<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Schema;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Schemas\Components\Section;
use Filament\Support\Colors\Color;

class Login extends BaseLogin
{
    protected static ?string $title = 'test';

    protected function getAuthenticateFormAction(): Action
    {
        return Action::make('guestLogin')
            ->label('Guest login');

    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Customize your login form schema here
                Section::make('Login')->schema([
                    $this->getEmailFormComponent(),
                    $this->getPasswordFormComponent(),
                    $this->getRememberFormComponent(),
                    Actions::make(
                        [
                            Action::make('authenticate')
                                ->label(__('filament-panels::auth/pages/login.form.actions.authenticate.label'))
                                ->submit('authenticate'),
                        ]
                    )->fullWidth()
                ])->collapsed(),

                TextEntry::make('or')->alignCenter()->color(Color::Gray)

            ]);

    }
}

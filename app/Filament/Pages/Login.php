<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Size;
use Illuminate\Support\Facades\Auth;

class Login extends BaseLogin
{
    protected static ?string $title = 'test';

    protected function getAuthenticateFormAction(): Action
    {
        return Action::make('guestLogin')
            ->label('Guest Access')
            ->size(Size::ExtraLarge)
            ->action(function () {
                if (Auth::attempt(['email' => 'guest@example.com', 'password' => 'password'])) {
                    return redirect()->intended(filament()->getUrl());
                }

                Notification::make()
                    ->title('Guest login failed')
                    ->danger()
                    ->send();

                return null;
            })
            ->color('');

    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Customize your login form schema here
                Section::make('User Access')
                    ->schema([
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getRememberFormComponent(),
                        Actions::make(
                            [
                                Action::make('authenticate')
                                    ->label(__('filament-panels::auth/pages/login.form.actions.authenticate.label'))
                                    ->submit('authenticate'),
                            ]
                        )->fullWidth(),
                    ])->collapsed(),

                View::make('filament.components.login.custom-or')
                    ->columnSpanFull(),

            ]);

    }
}

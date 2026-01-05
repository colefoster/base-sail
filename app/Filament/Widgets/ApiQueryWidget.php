<?php

namespace App\Filament\Widgets;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Http;

class ApiQueryWidget extends Widget implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    protected string $view = 'filament.widgets.api-query-widget';

    protected int|string|array $columnSpan = 'full';

    public ?array $data = [];
    public ?string $responseBody = null;
    public ?int $statusCode = null;
    public ?string $statusText = null;
    public ?array $responseHeaders = null;

    public static function canView(): bool
    {
        // Only show on Admin Tools page, not on dashboard
        return false;
    }

    public function mount(): void
    {
        $this->form->fill([
            'method' => 'GET',
            'url' => 'https://pokeapi.co/api/v2/pokemon/1',
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Generic API Tester')
                ->description('Test any API endpoint and view the response')
                ->icon('heroicon-o-beaker')
                ->schema([
                    Grid::make(4)
                        ->schema([
                            Select::make('method')
                                ->label('HTTP Method')
                                ->options([
                                    'GET' => 'GET',
                                    'POST' => 'POST',
                                    'PUT' => 'PUT',
                                    'PATCH' => 'PATCH',
                                    'DELETE' => 'DELETE',
                                ])
                                ->default('GET')
                                ->required()
                                ->columnSpan(1),

                            TextInput::make('url')
                                ->label('API URL')
                                ->placeholder('https://api.example.com/endpoint')
                                ->url()
                                ->required()
                                ->columnSpan(3),
                        ]),

                    Textarea::make('headers')
                        ->label('Headers (JSON)')
                        ->placeholder('{"Authorization": "Bearer token", "Content-Type": "application/json"}')
                        ->rows(3)
                        ->helperText('Optional: Enter headers as JSON object')
                        ->columnSpanFull(),

                    Textarea::make('body')
                        ->label('Request Body (JSON)')
                        ->placeholder('{"key": "value"}')
                        ->rows(5)
                        ->helperText('Optional: Enter request body as JSON (for POST, PUT, PATCH)')
                        ->visible(fn($get) => in_array($get('method'), ['POST', 'PUT', 'PATCH']))
                        ->columnSpanFull(),

                    Actions::make([
                        Action::make('query')
                            ->label('Execute Request')
                            ->icon('heroicon-o-play')
                            ->color('primary')
                            ->action(function () {
                                $this->executeQuery();
                            }),

                        Action::make('clear')
                            ->label('Clear Response')
                            ->icon('heroicon-o-x-mark')
                            ->color('gray')
                            ->visible(fn() => $this->responseBody !== null)
                            ->action(function () {
                                $this->responseBody = null;
                                $this->statusCode = null;
                                $this->statusText = null;
                                $this->responseHeaders = null;
                                Notification::make()
                                    ->title('Response Cleared')
                                    ->success()
                                    ->send();
                            }),
                    ])
                    ->alignEnd(),
                ]),
        ];
    }

    protected function getFormStatePath(): string
    {
        return 'data';
    }

    public function executeQuery(): void
    {
        $data = $this->form->getState();

        try {
            // Parse headers if provided
            $headers = [];
            if (!empty($data['headers'])) {
                $headersArray = json_decode($data['headers'], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $headers = $headersArray;
                } else {
                    throw new \Exception('Invalid JSON in headers');
                }
            }

            // Parse body if provided
            $body = null;
            if (!empty($data['body']) && in_array($data['method'], ['POST', 'PUT', 'PATCH'])) {
                $bodyArray = json_decode($data['body'], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $body = $bodyArray;
                } else {
                    throw new \Exception('Invalid JSON in request body');
                }
            }

            // Make the HTTP request
            $httpClient = Http::timeout(30)->withHeaders($headers);

            $response = match($data['method']) {
                'GET' => $httpClient->get($data['url']),
                'POST' => $httpClient->post($data['url'], $body ?? []),
                'PUT' => $httpClient->put($data['url'], $body ?? []),
                'PATCH' => $httpClient->patch($data['url'], $body ?? []),
                'DELETE' => $httpClient->delete($data['url']),
                default => throw new \Exception('Invalid HTTP method'),
            };

            // Store response data
            $this->statusCode = $response->status();
            $this->statusText = $response->reason();
            $this->responseHeaders = $response->headers();

            // Format the response body
            $body = $response->json();

            // If not JSON, use raw body
            if ($body === null) {
                $this->responseBody = $response->body();
            } else {
                $this->responseBody = json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }

            Notification::make()
                ->title('Request Successful')
                ->success()
                ->body("HTTP {$response->status()} {$response->reason()} - {$data['method']} {$data['url']}")
                ->send();

        } catch (\Exception $e) {
            $this->statusCode = null;
            $this->statusText = 'Error';
            $this->responseBody = "Error: {$e->getMessage()}";

            Notification::make()
                ->title('Request Failed')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }
}

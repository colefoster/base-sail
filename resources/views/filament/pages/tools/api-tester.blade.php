<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section>
            <form wire:submit="performRequest">
                {{ $this->form }}
            </form>
        </x-filament::section>

        @if ($response)
            <div class="space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <x-filament::section
                        class="@if($response['statusCode'] >= 200 && $response['statusCode'] < 300)
                            bg-success-50 dark:bg-success-900/20 border-success-200 dark:border-success-800
                        @elseif($response['statusCode'] >= 400)
                            bg-danger-50 dark:bg-danger-900/20 border-danger-200 dark:border-danger-800
                        @else
                            bg-info-50 dark:bg-info-900/20 border-info-200 dark:border-info-800
                        @endif"
                    >
                        <div>
                            <p class="text-xs font-medium uppercase text-gray-600 dark:text-gray-400">Status Code</p>
                            <p class="mt-1 text-2xl font-bold">{{ $response['statusCode'] ?? 'N/A' }}</p>
                        </div>
                    </x-filament::section>

                    <x-filament::section class="border-blue-200 bg-blue-50 dark:border-blue-800 dark:bg-blue-900/20">
                        <div>
                            <p class="text-xs font-medium uppercase text-gray-600 dark:text-gray-400">Size</p>
                            <p class="mt-1 text-2xl font-bold">{{ $response['metadata']['contentLength'] ?? 'N/A' }}</p>
                        </div>
                    </x-filament::section>

                    <x-filament::section class="border-purple-200 bg-purple-50 dark:border-purple-800 dark:bg-purple-900/20">
                        <div>
                            <p class="text-xs font-medium uppercase text-gray-600 dark:text-gray-400">Time</p>
                            <p class="mt-1 text-2xl font-bold">{{ $response['metadata']['executionTime'] ?? 0 }}ms</p>
                        </div>
                    </x-filament::section>

                    <x-filament::section class="border-amber-200 bg-amber-50 dark:border-amber-800 dark:bg-amber-900/20">
                        <div>
                            <p class="text-xs font-medium uppercase text-gray-600 dark:text-gray-400">Content Type</p>
                            <p class="mt-1 break-words text-sm font-mono">{{ Str::limit($response['metadata']['contentType'] ?? 'N/A', 30) }}</p>
                        </div>
                    </x-filament::section>
                </div>

                <x-filament::section
                    heading="Response Headers"
                    collapsible
                    collapsed
                >
                    <div class="max-h-96 space-y-3 overflow-y-auto">
                        @forelse($response['headers'] as $key => $value)
                            <div class="border-b border-gray-200 pb-2 last:border-0 dark:border-gray-700">
                                <p class="text-sm font-mono font-bold text-gray-900 dark:text-white">{{ $key }}</p>
                                <p class="break-words text-sm font-mono text-gray-600 dark:text-gray-400">{{ $value }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">No headers available</p>
                        @endforelse
                    </div>
                </x-filament::section>

                <x-filament::section heading="Response Body">
                    <div class="overflow-x-auto rounded-lg bg-gray-900 p-4 dark:bg-gray-950">
                        <pre class="text-sm font-mono text-gray-100"><code>{{ $this->getFormattedResponse() }}</code></pre>
                    </div>
                </x-filament::section>
            </div>
        @elseif($isLoading)
            <x-filament::section class="py-12 text-center">
                <div class="flex flex-col items-center gap-4">
                    <div class="animate-spin">
                        <x-filament::icon
                            icon="heroicon-o-arrow-path"
                            class="h-8 w-8 text-primary-600"
                        />
                    </div>
                    <p class="text-gray-600 dark:text-gray-400">Testing API request...</p>
                </div>
            </x-filament::section>
        @else
            <x-filament::section class="py-12 text-center">
                <x-filament::icon
                    icon="heroicon-o-globe-alt"
                    class="mx-auto mb-4 h-12 w-12 text-gray-400"
                />
                <p class="text-gray-600 dark:text-gray-400">Enter a URL and click "Test Request" to see the response</p>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>

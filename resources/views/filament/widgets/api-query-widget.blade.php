<x-filament-widgets::widget>
    {{ $this->form }}

    @if ($responseBody)
        <div class="fi-fo-component-ctn" style="margin-top: 1.5rem;">
            {{-- Response Headers Section - Full Width --}}
            @if ($responseHeaders)
                <x-filament::section
                    collapsible
                    collapsed
                >
                    <x-slot name="heading">
                        Response Headers
                    </x-slot>

                    <x-slot name="description">
                        HTTP response headers from the API
                    </x-slot>

                    @include('filament.widgets.partials.response-headers', ['headers' => $responseHeaders])
                </x-filament::section>
            @endif

            {{-- Two Column Layout for Keys and Body --}}
            <div class="fi-fo-grid" style="display: grid; gap: 1.5rem; margin-top: 1.5rem;">
                <style>
                    @media (min-width: 1024px) {
                        .fi-api-widget-grid {
                            grid-template-columns: repeat(2, minmax(0, 1fr));
                        }
                    }
                </style>
                <div class="fi-api-widget-grid" style="display: grid; gap: 1.5rem;">
                    {{-- Left Column: Response Keys --}}
                    @if ($keyStructure)
                        <x-filament::section
                            collapsible
                            :collapsed="false"
                        >
                            <x-slot name="heading">
                                Response Keys
                            </x-slot>

                            <x-slot name="description">
                                Top-level keys from the JSON response
                            </x-slot>

                            @include('filament.widgets.partials.response-keys', ['keyStructure' => $keyStructure])
                        </x-filament::section>
                    @endif

                    {{-- Right Column: Response Body --}}
                    <x-filament::section
                        collapsible
                        :collapsed="false"
                    >
                        <x-slot name="heading">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <span>Response Body</span>
                                @if ($statusCode)
                                    <x-filament::badge
                                        :color="$statusCode >= 200 && $statusCode < 300 ? 'success' : ($statusCode >= 400 ? 'danger' : 'warning')"
                                    >
                                        {{ $statusCode }} {{ $statusText }}
                                    </x-filament::badge>
                                @endif
                            </div>
                        </x-slot>

                        <x-slot name="headerEnd">
                            <x-filament::button
                                color="gray"
                                size="sm"
                                icon="heroicon-o-clipboard-document"
                                x-data="{ copied: false }"
                                x-on:click="
                                    navigator.clipboard.writeText(@js($responseBody));
                                    copied = true;
                                    setTimeout(() => copied = false, 2000);
                                "
                            >
                                <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                            </x-filament::button>
                        </x-slot>

                        @include('filament.widgets.partials.response-body', [
                            'responseBody' => $responseBody,
                            'statusCode' => $statusCode,
                            'statusText' => $statusText
                        ])
                    </x-filament::section>
                </div>
            </div>
        </div>
    @endif
</x-filament-widgets::widget>

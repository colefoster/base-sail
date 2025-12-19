<x-filament-widgets::widget>
    <x-filament::section
        heading="PokéAPI Data Importer"
        description="Import Pokemon, Moves, Abilities, Items, and related data from PokéAPI with real-time progress tracking"
    >
        <x-slot name="headerEnd">
            <div class="flex gap-2">
                @foreach($this->getHeaderActions() as $action)
                    {{ $action }}
                @endforeach
            </div>
        </x-slot>

        @if (!$isImporting)
            {{-- Import Form --}}
            <form wire:submit="startImport">
                {{ $this->form }}
            </form>

            {{-- Quick Clear Actions Section --}}
            <x-filament::section
                heading="Quick Clear Actions"
                description="Quickly delete specific data types from the database"
                class="mt-6"
                collapsible
                collapsed
            >
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    {{ $this->getClearAbilitiesAction }}
                    {{ $this->getClearMovesAction }}
                    {{ $this->getClearItemsAction }}
                    {{ $this->getClearSpeciesAction }}
                    {{ $this->getClearEvolutionChainsAction }}
                </div>
            </x-filament::section>
        @else
            {{-- Progress Display with Polling --}}
            <div wire:poll.250ms="updateProgress">
                {{-- Current Step --}}
                <div class="mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                        {{ $this->getStepLabel() }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                        {{ $progress['message'] ?? 'Initializing...' }}
                    </p>
                </div>

                {{-- Progress Bar --}}
                <div class="mb-6">
                    <div class="flex justify-between items-baseline mb-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Progress
                        </span>
                        <div class="text-right">
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $progress['current'] ?? 0 }} / {{ $progress['total'] ?? 0 }}
                            </span>
                            <span class="ml-2 text-lg font-bold text-primary-600 dark:text-primary-400">
                                {{ $this->getProgressPercentage() }}%
                            </span>
                        </div>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                        <div
                            class="bg-primary-500 h-3 rounded-full transition-all duration-500"
                            style="width: {{ $this->getProgressPercentage() }}%"
                        ></div>
                    </div>
                </div>

                {{-- Stats Grid --}}
                <div class="grid grid-cols-2 gap-4">
                    <x-filament::section
                        class="bg-success-50 dark:bg-success-900/20 border-success-200 dark:border-success-800"
                    >
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0">
                                <x-filament::icon
                                    icon="heroicon-o-check-circle"
                                    class="w-8 h-8 text-success-600 dark:text-success-400"
                                />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-success-600 dark:text-success-400">Success</p>
                                <p class="text-2xl font-bold text-success-700 dark:text-success-300">{{ $successCount }}</p>
                            </div>
                        </div>
                    </x-filament::section>

                    <x-filament::section
                        class="bg-danger-50 dark:bg-danger-900/20 border-danger-200 dark:border-danger-800"
                    >
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0">
                                <x-filament::icon
                                    icon="heroicon-o-x-circle"
                                    class="w-8 h-8 text-danger-600 dark:text-danger-400"
                                />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-danger-600 dark:text-danger-400">Errors</p>
                                <p class="text-2xl font-bold text-danger-700 dark:text-danger-300">{{ $errorCount }}</p>
                            </div>
                        </div>
                    </x-filament::section>
                </div>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>

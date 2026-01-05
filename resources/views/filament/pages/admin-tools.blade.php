<x-filament-panels::page>
    <div class="space-y-6">
        <div class="text-sm text-gray-600 dark:text-gray-400">
            Administrative tools for managing Pokemon data imports and API testing.
        </div>

        @foreach ($this->getWidgets() as $widget)
            @livewire($widget)
        @endforeach
    </div>
</x-filament-panels::page>
<x-filament-widgets::widget>
    <x-filament::section
        heading="API Query Tester"
        description="Test and query the PokéAPI directly from the dashboard"
    >
        <x-slot name="headerEnd">
            <div class="flex gap-2">
                @foreach($this->getHeaderActions() as $action)
                    {{ $action }}
                @endforeach
            </div>
        </x-slot>

        <div class="py-8 text-center">
            <x-filament::icon
                icon="heroicon-o-beaker"
                class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4"
            />
            <p class="text-lg font-medium text-gray-700 dark:text-gray-300">
                API Query Widget
            </p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                This widget is under development. Use it to test API queries.
            </p>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
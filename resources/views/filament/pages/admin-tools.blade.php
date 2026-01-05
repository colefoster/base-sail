<x-filament-panels::page>
    <x-filament::tabs wire:model="activeTab">
        @foreach ($this->getTabs() as $tabKey => $tab)
            <x-filament::tabs.item
                :alpine-active="'$wire.activeTab === \'' . $tabKey . '\''"
                :wire:click="'$set(\'activeTab\', \'' . $tabKey . '\')'"
                :icon="$tab['icon']"
            >
                {{ $tab['label'] }}
            </x-filament::tabs.item>
        @endforeach
    </x-filament::tabs>

    <div class="mt-6">
        @foreach ($this->getVisibleWidgets() as $widget)
            @livewire($widget, key($widget . '-' . $activeTab))
        @endforeach
    </div>
</x-filament-panels::page>
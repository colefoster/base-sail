<div>
    <x-filament::fieldset>
        <x-slot name="label">
            Sprites
        </x-slot>

        <div class="space-y-4">


            <div class="flex flex-col gap-2">
                <x-filament::button.group>
                    <x-filament::button
                        :color="$variant === 'default' ? 'primary' : 'gray'"
                        :outlined="$variant !== 'default'"
                        wire:click="setVariant('default')"
                        size="sm"
                    >
                        Regular
                    </x-filament::button>
                    <x-filament::button
                        :color="$variant === 'shiny' ? 'primary' : 'gray'"
                        :outlined="$variant !== 'shiny'"
                        wire:click="setVariant('shiny')"
                        size="sm"
                    >
                        Shiny
                    </x-filament::button>
                </x-filament::button.group>
            </div>

            {{-- Sprite Display --}}
            @if($this->spriteUrl)
                <div class="flex justify-center items-center p-6 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
                    <img
                        src="{{ $this->spriteUrl }}"
                        alt="Pokemon sprite"
                        class="w-auto h-auto max-w-full transition-all duration-300 ease-in-out"
                        style="image-rendering: pixelated;"
                    >
                </div>
            @endif
        </div>
    </x-filament::fieldset>
</div>

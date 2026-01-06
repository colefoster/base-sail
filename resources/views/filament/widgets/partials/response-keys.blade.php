<div class="fi-infolist-entry-wrapper">
    <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
        @foreach (array_keys($keyStructure) as $key)
            <x-filament::badge
                color="primary"
                size="md"
            >
                {{ $key }}
            </x-filament::badge>
        @endforeach
    </div>

    <div style="margin-top: 0.75rem; font-size: 0.75rem; line-height: 1rem; opacity: 0.7;">
        {{ count($keyStructure) }} {{ Str::plural('key', count($keyStructure)) }} found
    </div>
</div>

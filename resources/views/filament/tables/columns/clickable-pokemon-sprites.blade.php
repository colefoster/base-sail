@php
    $pokemon = $getState() ?? [];
@endphp

<div class="fi-ta-image flex flex-wrap gap-1.5">
    @foreach ($pokemon as $poke)
        <a
            href="{{ $poke['url'] }}"
            class="transition hover:scale-110"
            title="{{ ucwords(str_replace('-', ' ', $poke['name'])) }}"
            wire:navigate
        >
            <img
                src="{{ $poke['sprite'] }}"
                alt="{{ $poke['name'] }}"
                class="rounded-full ring-2 ring-white dark:ring-gray-900"
            />
        </a>
    @endforeach
</div>

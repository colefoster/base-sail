<template>
    <div
        data-test="pokemon-card"
        class="flex flex-col items-center p-4 border-2 rounded-lg transition-all"
        :class="{
            'cursor-pointer hover:shadow-lg hover:border-blue-400': clickable,
            'border-gray-300 dark:border-gray-600': !clickable
        }"
        :tabindex="clickable ? 0 : undefined"
        @click="handleClick"
        @keydown.enter="handleClick"
    >
        <img
            v-if="pokemon?.sprite"
            :src="pokemon.sprite"
            :alt="pokemon.name"
            data-test="pokemon-sprite"
            class="w-24 h-24 mb-2"
        />
        <h3 data-test="pokemon-name" class="font-semibold text-gray-900 dark:text-white">
            Poke: {{ pokemon.name }}
        </h3>
        <span data-test="pokemon-id" class="text-xs text-gray-500 mb-2">
            #{{ pokemon.id }}
        </span>
        <div class="flex gap-1 flex-wrap">
            <span
                v-for="type in pokemon.types"
                :key="type"
                data-test="pokemon-type"
                class="text-xs px-2 py-1 rounded capitalize bg-gray-200 dark:bg-gray-700"
            >
                {{ type }}
            </span>
        </div>

        <!-- Optional Stats Display -->
        <div v-if="showStats && pokemon.stats" class="mt-3 w-full text-xs space-y-1 text-gray-700 dark:text-gray-300">
            <div class="flex justify-between">
                <span>HP:</span>
                <span class="font-bold">{{ pokemon.stats.hp }}</span>
            </div>
            <div class="flex justify-between">
                <span>Attack:</span>
                <span class="font-bold">{{ pokemon.stats.attack }}</span>
            </div>
            <div class="flex justify-between">
                <span>Defense:</span>
                <span class="font-bold">{{ pokemon.stats.defense }}</span>
            </div>
            <div class="flex justify-between">
                <span>Speed:</span>
                <span class="font-bold">{{ pokemon.stats.speed }}</span>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    pokemon: {
        type: Object,
        required: true
    },
    clickable: {
        type: Boolean,
        default: false
    },
    showStats: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['click']);

function handleClick() {
    if (props.clickable) {
        emit('click', props.pokemon);
    }
}
</script>

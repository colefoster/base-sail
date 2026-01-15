<template>
    <Head title="Team Builder"/>

    <div class="min-h-screen bg-zinc-950">
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div
                class="bg-white dark:bg-zinc-900 shadow-xl rounded-xl overflow-hidden ring-1 ring-zinc-950/5 dark:ring-white/10">
                <div class="px-6 py-8">
                    <h1 class="text-4xl font-bold text-zinc-950 dark:text-white mb-2">
                        Team Builder
                    </h1>
                    <p class="text-zinc-600 dark:text-zinc-400 mb-6">
                        Possible subtitle
                    </p>

                    <AutoComplete
                        v-model="pokemonStore.selectedPokemon"
                        :suggestions="filteredPokemon"
                        :placeholder="`Search ${pokemonStore.formattedFormat} Pokemon...`"
                        dropdown
                        showClear
                        @complete="search"
                        size="Large"
                    />
                </div>
            </div>
            <div
                v-if="pokemonStore.selectedPokemon"
                class="bg-white dark:bg-zinc-900 shadow-xl rounded-xl overflow-hidden ring-1 ring-zinc-950/5 dark:ring-white/10 mt-4">
                <div class="px-6 py-8">
                    <h2 class="text-2xl font-bold text-zinc-950 dark:text-white">
                        {{ pokemonStore.selectedPokemon }}
                    </h2>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {ref, onMounted} from 'vue';
import {Head} from '@inertiajs/vue3';
import {usePokemonStore} from '../stores/usePokemonStore';

import AutoComplete from 'primevue/autocomplete';

const pokemonStore = usePokemonStore();
const filteredPokemon = ref([]);

const search = (event) => {
    const query = event.query.toLowerCase();
    filteredPokemon.value = pokemonStore.pokemon.filter(name =>
        name.toLowerCase().includes(query)
    );
};

const handleAddToTeam = async () => {
    if (!pokemonStore.selectedPokemon) return;

    const success = pokemonStore.addToTeam(pokemonStore.selectedPokemon);
    if (success) {
        pokemonStore.selectedPokemon = null; // Reset selection
    }
};
</script>

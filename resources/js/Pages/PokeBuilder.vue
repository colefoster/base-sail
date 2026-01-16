<script setup>

import { ref, onMounted } from 'vue';
import {useTeambuilderStore} from "../stores/useTeambuilderStore.js";

const teambuilderStore = useTeambuilderStore();
onMounted(async () => {
    alert("Hello Poke Builder!");
    await teambuilderStore.fetchPokemonNamesInFormat();
})

</script>
<template>
    <Head title="Team Builder" />

    <div class="min-h-screen bg-zinc-950">
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-zinc-900 shadow-xl rounded-xl overflow-hidden ring-1 ring-zinc-950/5 dark:ring-white/10">
                <div class="px-6 py-8">
                    <h1 class="text-4xl font-bold text-zinc-950 dark:text-white mb-2">
                        Team Builder
                    </h1>
                    <p class="text-zinc-600 dark:text-zinc-400 mb-6">
                        Possible subtitle
                    </p>

                    <AutoComplete v-model="value" dropdown :suggestions="items" @complete="search" />
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>

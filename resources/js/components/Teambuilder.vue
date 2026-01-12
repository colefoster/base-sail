<script setup>
import { ref, onMounted, computed } from 'vue';
import { useTeamStore } from '@/stores/teamStore';
import TeamSlot from './TeamSlot.vue';
import PokemonCard from './PokemonCard.vue';
import { fetchPokemon } from '@/services/pokemonApi';

const teamStore = useTeamStore();

const showSelector = ref(false);
const selectedSlot = ref(null);
const availablePokemon = ref([]);
const loading = ref(false);
const error = ref(null);
const searchQuery = ref('');
const currentPage = ref(1);
const totalPages = ref(1);

// Filter out Pokemon already on the team
const filteredPokemon = computed(() => {
    const teamIds = teamStore.team.filter(p => p !== null).map(p => p.id);
    let filtered = availablePokemon.value.filter(p => !teamIds.includes(p.id));

    // Apply search filter
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(p => p.name.toLowerCase().includes(query));
    }

    return filtered;
});

// Fetch Pokemon from the API
async function loadPokemon(page = 1) {
    loading.value = true;
    error.value = null;

    try {
        const response = await fetchPokemon({ page });
        availablePokemon.value = response.data;
        currentPage.value = response.meta.current_page;
        totalPages.value = response.meta.last_page;
    } catch (err) {
        error.value = 'Failed to load Pokemon. Please try again.';
        console.error('Error loading Pokemon:', err);
    } finally {
        loading.value = false;
    }
}

function openSelector(slotIndex) {
    selectedSlot.value = slotIndex;
    showSelector.value = true;
}

function selectPokemon(pokemon) {
    teamStore.addPokemon(pokemon, selectedSlot.value);
    showSelector.value = false;
    selectedSlot.value = null;
}

function nextPage() {
    if (currentPage.value < totalPages.value) {
        loadPokemon(currentPage.value + 1);
    }
}

function previousPage() {
    if (currentPage.value > 1) {
        loadPokemon(currentPage.value - 1);
    }
}

// Load Pokemon on mount
onMounted(() => {
    loadPokemon();
});
</script>

<template>
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                        Pokemon Team Builder
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400">
                        Build your perfect team of 6 Pokemon
                    </p>
                </div>
                <a
                    href="/login"
                    class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors flex items-center gap-2"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Admin Dashboard
                </a>
            </div>
        </div>

        <!-- Team Stats -->
        <div class="mb-6 flex items-center justify-between">
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Team: {{ teamStore.teamCount }} / 6 Pokemon
            </div>
            <button
                @click="teamStore.clearTeam"
                v-if="!teamStore.isEmpty"
                class="px-4 py-2 text-sm bg-red-500 text-white rounded hover:bg-red-600 transition"
            >
                Clear Team
            </button>
        </div>

        <!-- Team Slots Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-8">
            <TeamSlot
                v-for="(pokemon, index) in teamStore.team"
                :key="index"
                :pokemon="pokemon"
                :slot-number="index + 1"
                @add="openSelector(index)"
                @remove="teamStore.removePokemon(index)"
            />
        </div>

        <!-- Pokemon Selector Modal -->
        <div
            v-if="showSelector"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
            @click="showSelector = false"
        >
            <div
                class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-4xl w-full max-h-[90vh] overflow-y-auto"
                @click.stop
            >
                <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">
                    Select a Pokemon
                </h2>

                <!-- Search Bar -->
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Search Pokemon..."
                    class="w-full px-4 py-2 mb-4 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                />

                <!-- Loading State -->
                <div v-if="loading" class="text-center py-8">
                    <p class="text-gray-600 dark:text-gray-400">Loading Pokemon...</p>
                </div>

                <!-- Error State -->
                <div v-else-if="error" class="text-center py-8">
                    <p class="text-red-600 dark:text-red-400">{{ error }}</p>
                    <button
                        @click="loadPokemon()"
                        class="mt-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                    >
                        Retry
                    </button>
                </div>

                <!-- Pokemon Grid -->
                <div v-else-if="filteredPokemon.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mb-4">
                    <PokemonCard
                        v-for="pokemon in filteredPokemon"
                        :key="pokemon.id"
                        :pokemon="pokemon"
                        :clickable="true"
                        @click="selectPokemon(pokemon)"
                    />
                </div>

                <!-- Empty State -->
                <div v-else class="text-center py-8">
                    <p class="text-gray-600 dark:text-gray-400">
                        {{ searchQuery ? 'No Pokemon found matching your search.' : 'No Pokemon available.' }}
                    </p>
                </div>

                <!-- Pagination -->
                <div v-if="!loading && totalPages > 1" class="flex items-center justify-between mt-4">
                    <button
                        @click="previousPage"
                        :disabled="currentPage === 1"
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white rounded hover:bg-gray-300 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Previous
                    </button>
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        Page {{ currentPage }} of {{ totalPages }}
                    </span>
                    <button
                        @click="nextPage"
                        :disabled="currentPage === totalPages"
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white rounded hover:bg-gray-300 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Next
                    </button>
                </div>

                <!-- Cancel Button -->
                <button
                    @click="showSelector = false"
                    class="mt-4 w-full py-2 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white rounded hover:bg-gray-300 dark:hover:bg-gray-600 transition"
                >
                    Cancel
                </button>
            </div>
        </div>
    </div>
</template>

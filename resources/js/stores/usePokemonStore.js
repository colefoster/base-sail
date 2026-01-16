import {defineStore} from 'pinia';
import {ref} from 'vue';

export const usePokemonStore = defineStore('pokemon', () => {
    // State
    const pokemon = ref([]);
    const loading = ref(false);
    const error = ref(null);
    const loadTime = ref(null);
    const pagination = ref({
        current_page: 1,
        last_page: 1,
        per_page: 20,
        total: 0
    });

    // Actions
    async function fetchPokemon(search = '') {
        loading.value = true;
        error.value = null;
        const startTime = performance.now();

        try {
            const params = new URLSearchParams();

            if (search) {
                params.append('search', search);
            }

            const response = await fetch(`/api/pokemon?${params}`);

            if (!response.ok) {
                throw new Error('Failed to fetch Pokemon');
            }

            const data = await response.json();
            const endTime = performance.now();
            loadTime.value = ((endTime - startTime) / 1000).toFixed(2);

            // If response is an array, it's all Pokemon
            if (Array.isArray(data)) {
                pokemon.value = data;
                pagination.value = {
                    current_page: 1,
                    last_page: 1,
                    per_page: data.length,
                    total: data.length
                };
            } else {
                // Paginated response
                pokemon.value = data.data;
                pagination.value = {
                    current_page: data.current_page,
                    last_page: data.last_page,
                    per_page: data.per_page,
                    total: data.total
                };
            }
        } catch (err) {
            error.value = err.message;
            console.error('Error fetching Pokemon:', err);
        } finally {
            loading.value = false;
        }
    }

    async function fetchPokemonById(apiId) {
        loading.value = true;
        error.value = null;

        try {
            const response = await fetch(`/api/pokemon/${apiId}`);

            if (!response.ok) {
                throw new Error('Failed to fetch Pokemon details');
            }

            return await response.json();
        } catch (err) {
            error.value = err.message;
            console.error('Error fetching Pokemon:', err);
            return null;
        } finally {
            loading.value = false;
        }
    }

    async function searchPokemon(query) {
        if (!query) {
            return [];
        }

        loading.value = true;
        error.value = null;

        try {
            const response = await fetch(`/api/pokemon/search?q=${encodeURIComponent(query)}`);

            if (!response.ok) {
                throw new Error('Failed to search Pokemon');
            }

            return await response.json();
        } catch (err) {
            error.value = err.message;
            console.error('Error searching Pokemon:', err);
            return [];
        } finally {
            loading.value = false;
        }
    }

    return {
        // State
        pokemon,
        loading,
        error,
        loadTime,
        pagination,

        // Actions
        fetchPokemon,
        fetchPokemonById,
        searchPokemon,
    };
});

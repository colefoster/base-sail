import {defineStore} from 'pinia';
import {ref, computed} from 'vue';

export const useTeambuilderStore = defineStore('teambuilder', () => {
    // State
    const availablePokemon = ref([]); // Array of {name, usage} objects
    const selectedPokemon = ref(null);
    const selectedPokemonData = ref(null);
    const selectedPokemonSets = ref(null);
    const currentFormat = ref('gen9ou');
    const loading = ref(false);
    const error = ref(null);
    const loadTime = ref(null);
    const sortByUsage = ref(true); // Default to sorting by usage

    // Team state
    const team = ref([]);
    const maxTeamSize = 6;

    // Getters
    const hasFullTeam = computed(() => team.value.length >= maxTeamSize);
    const teamCount = computed(() => team.value.length);
    const sortedAvailablePokemon = computed(() => {
        if (!availablePokemon.value.length) return [];

        const sorted = [...availablePokemon.value];
        if (sortByUsage.value) {
            sorted.sort((a, b) => (b.usage ?? 0) - (a.usage ?? 0));
        } else {
            sorted.sort((a, b) => a.name.localeCompare(b.name));
        }
        return sorted;
    });
    const formattedFormat = computed(() => {
        // "gen9ou" -> "Gen 9 OU"
        const match = currentFormat.value.match(/^(gen)(\d+)(.*)$/i);
        if (match) {
            const tier = match[3].toUpperCase() || '';
            return `Gen ${match[2]} ${tier}`.trim();
        }
        return currentFormat.value;
    });

    // Actions
    async function setFormat(format) {
        if (format === currentFormat.value) return;

        currentFormat.value = format;
        clearSelection();
        await fetchPokemonNamesInFormat(format);
    }

    async function fetchPokemonNamesInFormat(format = null) {
        const targetFormat = format || currentFormat.value;
        loading.value = true;
        error.value = null;
        const startTime = performance.now();

        try {
            // Fetch names with usage data
            const response = await fetch(`/api/formats/${targetFormat}/names/usage?sort=usage`);

            if (!response.ok) {
                throw new Error('Failed to fetch Pokemon names');
            }

            const data = await response.json();
            const endTime = performance.now();
            loadTime.value = ((endTime - startTime) / 1000).toFixed(2);

            availablePokemon.value = data;
        } catch (err) {
            error.value = err.message;
            console.error('Error fetching Pokemon names:', err);
        } finally {
            loading.value = false;
        }
    }

    function setSortByUsage(value) {
        sortByUsage.value = value;
    }

    async function fetchCombinedByName(name, format = null) {
        if (!name) return null;

        loading.value = true;
        error.value = null;

        const targetFormat = format || currentFormat.value;

        try {
            const response = await fetch(
                `/api/formats/${targetFormat}/combined/search?q=${encodeURIComponent(name)}`
            );

            if (!response.ok) {
                throw new Error('Failed to fetch Pokemon data');
            }

            const data = await response.json();

            // Find exact match or first result
            const match = data.find(item =>
                item.name.toLowerCase() === name.toLowerCase()
            ) || data[0];

            if (match) {
                selectedPokemon.value = name;
                selectedPokemonData.value = match.pokemon;
                selectedPokemonSets.value = match.sets;
                return match;
            }

            return null;
        } catch (err) {
            error.value = err.message;
            console.error('Error fetching Pokemon data:', err);
            return null;
        } finally {
            loading.value = false;
        }
    }

    async function fetchSetsByFormat(format = null) {
        const targetFormat = format || currentFormat.value;
        loading.value = true;
        error.value = null;

        try {
            const response = await fetch(`/api/formats/${targetFormat}/sets`);

            if (!response.ok) {
                throw new Error('Failed to fetch sets');
            }

            return await response.json();
        } catch (err) {
            error.value = err.message;
            console.error('Error fetching sets:', err);
            return null;
        } finally {
            loading.value = false;
        }
    }

    function clearSelection() {
        selectedPokemon.value = null;
        selectedPokemonData.value = null;
        selectedPokemonSets.value = null;
    }

    // Team management
    function addToTeam(pokemonData) {
        if (team.value.length >= maxTeamSize) {
            error.value = 'Team is full! Maximum 6 Pokemon allowed.';
            return false;
        }

        // Check if Pokemon is already in team
        if (team.value.find(p => p.api_id === pokemonData.api_id)) {
            error.value = 'This Pokemon is already in your team!';
            return false;
        }

        team.value.push(pokemonData);
        saveTeamToStorage();
        return true;
    }

    function removeFromTeam(apiId) {
        const index = team.value.findIndex(p => p.api_id === apiId);
        if (index !== -1) {
            team.value.splice(index, 1);
            saveTeamToStorage();
        }
    }

    function clearTeam() {
        team.value = [];
        saveTeamToStorage();
    }

    function isInTeam(apiId) {
        return team.value.some(p => p.api_id === apiId);
    }

    function saveTeamToStorage() {
        try {
            localStorage.setItem(`team_${currentFormat.value}`, JSON.stringify(team.value));
        } catch (err) {
            console.error('Error saving team to storage:', err);
        }
    }

    function loadTeamFromStorage() {
        try {
            const saved = localStorage.getItem(`team_${currentFormat.value}`);
            if (saved) {
                team.value = JSON.parse(saved);
            }
        } catch (err) {
            console.error('Error loading team from storage:', err);
        }
    }

    // Initialize on store creation
    loadTeamFromStorage();
    fetchPokemonNamesInFormat();

    return {
        // State
        availablePokemon,
        selectedPokemon,
        selectedPokemonData,
        selectedPokemonSets,
        currentFormat,
        loading,
        error,
        loadTime,
        team,
        maxTeamSize,
        sortByUsage,

        // Getters
        hasFullTeam,
        teamCount,
        formattedFormat,
        sortedAvailablePokemon,

        // Actions
        setFormat,
        fetchPokemonNamesInFormat,
        fetchCombinedByName,
        fetchSetsByFormat,
        clearSelection,
        addToTeam,
        removeFromTeam,
        clearTeam,
        isInTeam,
        loadTeamFromStorage,
        setSortByUsage,
    };
});

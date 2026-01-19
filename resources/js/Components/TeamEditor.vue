<script setup>
import SelectButton from 'primevue/selectbutton';
import ScrollPanel from 'primevue/scrollpanel';
import Menu from 'primevue/menu';
import Divider from 'primevue/divider';

import Tabs from 'primevue/tabs';
import TabList from 'primevue/tablist';
import Tab from 'primevue/tab';
import TabPanels from 'primevue/tabpanels';
import TabPanel from 'primevue/tabpanel';

import Panel from 'primevue/panel';
import Button from 'primevue/button';
import PokemonSelector from '../Components/PokemonSelector.vue';
import {useTeambuilderStore} from '../stores/useTeambuilderStore';

const teambuilderStore = useTeambuilderStore();
import {ref, computed} from 'vue';
import Badge from 'primevue/badge';

// Format options
const formatOptions = [
    {label: 'Gen 9 OU', value: 'gen9ou'},
    {label: 'Gen 9 UU', value: 'gen9uu'},
    {label: 'Gen 9 RU', value: 'gen9ru'},
    {label: 'Gen 9 NU', value: 'gen9nu'},
    {label: 'Gen 9 Ubers', value: 'gen9ubers'},
    {label: 'Gen 9 AG', value: 'gen9ag'},
    {label: 'Gen 9 Doubles OU', value: 'gen9doublesou'},
];

const selectedFormat = ref(teambuilderStore.currentFormat);

const onFormatChange = () => {
    teambuilderStore.setFormat(selectedFormat.value);
    clearSelection();
};

// Teams data (placeholder)
const teams = ref([
    {id: 1, name: 'OU Rain Team', format: 'gen9ou', pokemonCount: 6},
    {id: 2, name: 'Hyper Offense', format: 'gen9ou', pokemonCount: 5},
    {id: 3, name: 'Stall Squad', format: 'gen9uu', pokemonCount: 6},
    {id: 4, name: 'Sun Team', format: 'gen9ou', pokemonCount: 3},
    {id: 5, name: 'Trick Room', format: 'gen9doublesou', pokemonCount: 4},
    {id: 6, name: 'Balance Core', format: 'gen9ru', pokemonCount: 2},
    {id: 7, name: 'Weather Wars', format: 'gen9uu', pokemonCount: 6},
]);

// Format display names
const formatLabels = {
    'gen9ou': 'Gen 9 OU',
    'gen9uu': 'Gen 9 UU',
    'gen9ru': 'Gen 9 RU',
    'gen9nu': 'Gen 9 NU',
    'gen9ubers': 'Gen 9 Ubers',
    'gen9ag': 'Gen 9 AG',
    'gen9doublesou': 'Gen 9 Doubles OU',
};

// Group teams by format
const teamsByFormat = computed(() => {
    const grouped = {};
    teams.value.forEach(team => {
        if (!grouped[team.format]) {
            grouped[team.format] = [];
        }
        grouped[team.format].push(team);
    });
    return grouped;
});

const selectedTeam = ref(null);

const selectTeam = (team) => {
    selectedTeam.value = team;
};

const addNewTeam = () => {
    // Placeholder for add team logic
};

const getBadgeSeverity = (count) => {
    if (count === 6) return 'success';
    if (count >= 4) return 'warn';
    return 'danger';
};

</script>

<template>
    <div class="flex h-screen">
        <!-- Left Sidebar Menu -->
        <div class="w-72 bg-surface-50 dark:bg-surface-900 border-r border-surface-200 dark:border-surface-700 flex flex-col">
            <!-- Header with Add Button -->
            <div class="p-4 border-b border-surface-200 dark:border-surface-700">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-lg font-semibold text-surface-900 dark:text-surface-0">Teams</h2>
                </div>
                <Button
                    label="Add New Team"
                    icon="pi pi-plus"
                    class="w-full"
                    severity="primary"
                    @click="addNewTeam"
                />
            </div>

            <!-- Scrollable Teams List Grouped by Format -->
            <ScrollPanel class="flex-1" style="width: 100%;">
                <div class="p-2">
                    <template v-for="(formatTeams, format) in teamsByFormat" :key="format">
                        <!-- Format Header -->
                        <div class="px-2 py-2 mt-2 first:mt-0">
                            <span class="text-xs font-bold uppercase tracking-wider text-surface-500 dark:text-surface-400">
                                {{ formatLabels[format] || format }}
                            </span>
                        </div>

                        <!-- Teams in this format -->
                        <div
                            v-for="(team, index) in formatTeams"
                            :key="team.id"
                            @click="selectTeam(team)"
                            class="p-3 mb-2 rounded-lg cursor-pointer transition-colors duration-200 border"
                            :class="[
                                selectedTeam?.id === team.id
                                    ? 'bg-primary-100 dark:bg-primary-900 border-primary-400 dark:border-primary-600'
                                    : 'bg-surface-0 dark:bg-surface-800 hover:bg-surface-100 dark:hover:bg-surface-700 border-surface-200 dark:border-surface-700'
                            ]"
                        >
                            <div class="flex items-center justify-between">
                                <div class="font-medium text-surface-900 dark:text-surface-0 truncate">
                                    {{ team.name }}
                                </div>
                                <Badge
                                    :value="`${team.pokemonCount}/6`"
                                    :severity="getBadgeSeverity(team.pokemonCount)"
                                />
                            </div>
                        </div>

                        <!-- Separator between formats -->
                        <Divider class="my-2" />
                    </template>
                </div>
            </ScrollPanel>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 overflow-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <h1 class="text-4xl font-bold text-zinc-950 dark:text-white mb-2">
                    Team Builder
                </h1>
                <Select
                    v-model="teambuilderStore.currentFormat"
                    :options="formatOptions"
                    optionLabel="label"
                    optionValue="value"
                    placeholder="Select Format"
                    class="w-64"
                    @change="onFormatChange"
                />
                <div class="max-w-7xl mx-auto py-8">
                    <!-- Team Editor Component -->
                    <div v-if="selectedTeam" class="p-4 bg-surface-100 dark:bg-surface-800 rounded-lg">
                        <h2 class="text-2xl font-semibold text-surface-900 dark:text-surface-0 mb-4">
                            Editing: {{ selectedTeam.name }}
                        </h2>
                        <!-- Team editing content goes here -->
                    </div>
                    <div v-else class="p-4 text-surface-500 dark:text-surface-400">
                        Select a team from the sidebar or create a new one.
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>

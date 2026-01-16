<script setup>
import SelectButton from 'primevue/selectbutton';
import Splitter from 'primevue/splitter';
import SplitterPanel from 'primevue/splitterpanel';
import ScrollPanel from 'primevue/scrollpanel';

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
import {ref} from 'vue';
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

</script>

<template>
    <div class="max-w-7xl mx-auto  px-4 sm:px-6 lg:px-8">
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
        <div class="max-w-7xl mx-auto py-8 ">
            <Tabs value="0" scrollable>
                <TabList>
                    <Tab v-for="tab in scrollableTabs" :key="tab.title" :value="tab.value">
                        {{ tab.title }}
                    </Tab>
                </TabList>
                <TabPanels>
                    <TabPanel v-for="tab in scrollableTabs" :key="tab.content" :value="tab.value">
                        <p class="m-0">{{ tab.content }}</p>
                    </TabPanel>
                </TabPanels>
            </Tabs>
            <Splitter style="height: 600px">
                <SplitterPanel class="" :size="10" :minSize="10">
                    <Splitter layout="vertical">
                        <SplitterPanel
                            v-if="!teambuilderStore.isTeamFull" :size="20" :minSize="10">
                            <Button>add</Button>
                        </SplitterPanel>
                        <SplitterPanel
                            v-for="(poke, index) in teambuilderStore.team"
                            :key="index"
                        >
                            poke here
                        </SplitterPanel>

                    </Splitter>

                </SplitterPanel>
                <SplitterPanel :size="80">
                    <Splitter layout="vertical">
                        <SplitterPanel class="flex items-center justify-center" :size="1" :min-size="1"> Team Name Here</SplitterPanel>
                        <SplitterPanel :size="85">
                            <ScrollPanel style="width: 100%; height: 590px">
                                    <PokemonSelector @pokemon-added="onPokemonAdded"/>
                            </ScrollPanel>
                        </SplitterPanel>
                    </Splitter>
                </SplitterPanel>
            </Splitter>


        </div>

    </div>


</template>

<style scoped>

</style>

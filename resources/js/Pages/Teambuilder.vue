<template>
    <Head title="Team Builder"/>

    <div class="min-h-screen bg-zinc-950">
        <!-- MegaMenu with Template Slots -->
        <MegaMenu :model="menuItems" class="mb-6 border-0 bg-zinc-100 dark:bg-zinc-800 !rounded-none">
            <template #start>
                <img width="35" height="40" src="../../../public/images/special-ball-96.png" class="h-8"/>
            </template>
            <template #item="{ item, hasSubmenu }">
                <a v-if="item.url" :href="item.url" :target="item.target"
                   class="flex items-center gap-2 px-3 py-2 cursor-pointer hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded transition-colors">
                    <span :class="item.icon" class="text-zinc-500 dark:text-zinc-400"></span>
                    <span class="text-zinc-700 dark:text-zinc-300">{{ item.label }}</span>
                    <span v-if="hasSubmenu" class="pi pi-angle-down ml-auto text-zinc-400"></span>
                </a>
                <a v-else
                   class="flex items-center gap-2 px-3 py-2 cursor-pointer hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded transition-colors"
                   @click="item.command && item.command()">
                    <span :class="item.icon" class="text-zinc-500 dark:text-zinc-400"></span>
                    <span class="text-zinc-700 dark:text-zinc-300">{{ item.label }}</span>
                    <span v-if="hasSubmenu" class="pi pi-angle-down ml-auto text-zinc-400"></span>
                </a>
            </template>
            <template #end>
                <div class="flex items-center gap-2 px-2">
                    <span class="text-xs text-zinc-500 dark:text-zinc-400">
                        Team: {{ teambuilderStore.team?.length || 0 }}/6
                    </span>
                    <Button icon="pi pi-trash" severity="secondary" text rounded size="small"
                            @click="teambuilderStore.clearTeam()" :disabled="!teambuilderStore.team?.length"/>
                </div>
            </template>
        </MegaMenu>

        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <PokemonSelector @pokemon-added="onPokemonAdded"/>
        </div>
    </div>
</template>

<script setup>
import {ref} from 'vue';
import {Head} from '@inertiajs/vue3';
import {useTeambuilderStore} from '../stores/useTeambuilderStore';

import Button from 'primevue/button';
import MegaMenu from 'primevue/megamenu';
import PokemonSelector from '../Components/PokemonSelector.vue';

const teambuilderStore = useTeambuilderStore();

// MegaMenu items
const menuItems = ref([
    {
        label: 'Formats',
        icon: 'pi pi-fw pi-list',
        items: [
            [
                {
                    label: 'Smogon Tiers',
                    items: [
                        {
                            label: 'OU (OverUsed)',
                            icon: 'pi pi-fw pi-star',
                            command: () => teambuilderStore.setFormat('gen9ou')
                        },
                        {
                            label: 'UU (UnderUsed)',
                            icon: 'pi pi-fw pi-star',
                            command: () => teambuilderStore.setFormat('gen9uu')
                        },
                        {
                            label: 'RU (RarelyUsed)',
                            icon: 'pi pi-fw pi-star',
                            command: () => teambuilderStore.setFormat('gen9ru')
                        },
                        {
                            label: 'NU (NeverUsed)',
                            icon: 'pi pi-fw pi-star',
                            command: () => teambuilderStore.setFormat('gen9nu')
                        }
                    ]
                }
            ],
            [
                {
                    label: 'Special Formats',
                    items: [
                        {label: 'Ubers', icon: 'pi pi-fw pi-bolt', command: () => teambuilderStore.setFormat('gen9ubers')},
                        {
                            label: 'Anything Goes',
                            icon: 'pi pi-fw pi-exclamation-circle',
                            command: () => teambuilderStore.setFormat('gen9ag')
                        },
                        {
                            label: 'Doubles OU',
                            icon: 'pi pi-fw pi-users',
                            command: () => teambuilderStore.setFormat('gen9doublesou')
                        },
                        {
                            label: 'VGC 2024',
                            icon: 'pi pi-fw pi-trophy',
                            command: () => teambuilderStore.setFormat('gen9vgc2024')
                        }
                    ]
                }
            ],
            [
                {
                    label: 'Past Generations',
                    items: [
                        {
                            label: 'Gen 8 OU',
                            icon: 'pi pi-fw pi-history',
                            command: () => teambuilderStore.setFormat('gen8ou')
                        },
                        {
                            label: 'Gen 7 OU',
                            icon: 'pi pi-fw pi-history',
                            command: () => teambuilderStore.setFormat('gen7ou')
                        },
                        {
                            label: 'Gen 6 OU',
                            icon: 'pi pi-fw pi-history',
                            command: () => teambuilderStore.setFormat('gen6ou')
                        },
                        {
                            label: 'Gen 5 OU',
                            icon: 'pi pi-fw pi-history',
                            command: () => teambuilderStore.setFormat('gen5ou')
                        }
                    ]
                }
            ]
        ]
    },
    {
        label: 'Types',
        icon: 'pi pi-fw pi-palette',
        items: [
            [
                {
                    label: 'Physical Types',
                    items: [
                        {label: 'Normal', icon: 'pi pi-fw pi-circle'},
                        {label: 'Fighting', icon: 'pi pi-fw pi-circle'},
                        {label: 'Flying', icon: 'pi pi-fw pi-circle'},
                        {label: 'Ground', icon: 'pi pi-fw pi-circle'},
                        {label: 'Rock', icon: 'pi pi-fw pi-circle'},
                        {label: 'Bug', icon: 'pi pi-fw pi-circle'}
                    ]
                }
            ],
            [
                {
                    label: 'Special Types',
                    items: [
                        {label: 'Fire', icon: 'pi pi-fw pi-circle'},
                        {label: 'Water', icon: 'pi pi-fw pi-circle'},
                        {label: 'Grass', icon: 'pi pi-fw pi-circle'},
                        {label: 'Electric', icon: 'pi pi-fw pi-circle'},
                        {label: 'Psychic', icon: 'pi pi-fw pi-circle'},
                        {label: 'Ice', icon: 'pi pi-fw pi-circle'}
                    ]
                }
            ],
            [
                {
                    label: 'Other Types',
                    items: [
                        {label: 'Dragon', icon: 'pi pi-fw pi-circle'},
                        {label: 'Dark', icon: 'pi pi-fw pi-circle'},
                        {label: 'Steel', icon: 'pi pi-fw pi-circle'},
                        {label: 'Fairy', icon: 'pi pi-fw pi-circle'},
                        {label: 'Ghost', icon: 'pi pi-fw pi-circle'},
                        {label: 'Poison', icon: 'pi pi-fw pi-circle'}
                    ]
                }
            ]
        ]
    },
    {
        label: 'Tools',
        icon: 'pi pi-fw pi-wrench',
        items: [
            [
                {
                    label: 'Team Analysis',
                    items: [
                        {label: 'Type Coverage', icon: 'pi pi-fw pi-chart-pie'},
                        {label: 'Weaknesses', icon: 'pi pi-fw pi-exclamation-triangle'},
                        {label: 'Speed Tiers', icon: 'pi pi-fw pi-sort-amount-up'}
                    ]
                }
            ],
            [
                {
                    label: 'Export',
                    items: [
                        {label: 'Export to Showdown', icon: 'pi pi-fw pi-external-link'},
                        {label: 'Copy to Clipboard', icon: 'pi pi-fw pi-copy'},
                        {label: 'Save Team', icon: 'pi pi-fw pi-save'}
                    ]
                }
            ]
        ]
    },
    {
        label: 'Resources',
        icon: 'pi pi-fw pi-book',
        items: [
            [
                {
                    label: 'Guides',
                    items: [
                        {
                            label: 'Smogon Dex',
                            icon: 'pi pi-fw pi-external-link',
                            url: 'https://www.smogon.com/dex/sv/',
                            target: '_blank'
                        },
                        {
                            label: 'Damage Calculator',
                            icon: 'pi pi-fw pi-calculator',
                            url: 'https://calc.pokemonshowdown.com/',
                            target: '_blank'
                        },
                        {
                            label: 'Usage Stats',
                            icon: 'pi pi-fw pi-chart-bar',
                            url: 'https://www.smogon.com/stats/',
                            target: '_blank'
                        }
                    ]
                }
            ]
        ]
    }
]);

const onPokemonAdded = (pokemon) => {
    // Handle any page-level logic when a Pokemon is added
    console.log('Pokemon added to team:', pokemon.name);
};
</script>

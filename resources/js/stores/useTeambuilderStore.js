import {defineStore} from 'pinia';
import {ref, computed} from 'vue';

export const useTeamBuilderStore = defineStore('pokemon', () => {
    const selectedFormat = ref('gen9ou');

    function getListOfPokemonInSelectedTier() {
        return []
    }

    return{
        //State (variables)
        selectedFormat,

        //Getters (computed)

        //Functions (actions)
        getListOfPokemonInSelectedTier
    };
});


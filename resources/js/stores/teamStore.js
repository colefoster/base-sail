import {ref, computed} from 'vue';
import {defineStore} from 'pinia';


export const useTeamStore = defineStore('counter', () => {
    const team = ref([null,
        null,
        null,
        null,
        null,
        null])

    const teamCount = computed(() => team.value.filter(value => value !== null).length)

    return { team, teamCount }
})

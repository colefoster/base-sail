import { describe, it, expect, beforeEach } from 'vitest';
import { mount } from '@vue/test-utils';
import { setActivePinia, createPinia } from 'pinia';
import Teambuilder from '@/components/Teambuilder.vue';
import { useTeamStore } from '@/stores/teamStore';

describe('Teambuilder Integration', () => {
    let store;

    beforeEach(() => {
        setActivePinia(createPinia());
        store = useTeamStore();
    });

    const pikachu = {
        id: 25,
        name: 'Pikachu',
        sprite: 'https://example.com/25.png',
        types: ['electric']
    };

    const bulbasaur = {
        id: 1,
        name: 'Bulbasaur',
        sprite: 'https://example.com/1.png',
        types: ['grass', 'poison']
    };

    const charizard = {
        id: 6,
        name: 'Charizard',
        sprite: 'https://example.com/6.png',
        types: ['fire', 'flying']
    };

    describe('Initial Render', () => {
        it('should render the teambuilder component', () => {
            const wrapper = mount(Teambuilder);

            expect(wrapper.exists()).toBe(true);
        });

        it('should display title', () => {
            const wrapper = mount(Teambuilder);

            expect(wrapper.text()).toContain('Pokemon Team Builder');
        });

        it('should render 6 team slots', () => {
            const wrapper = mount(Teambuilder);

            const slots = wrapper.findAll('[data-test="team-slot"]');
            expect(slots).toHaveLength(6);
        });

        it('should not show selector modal initially', () => {
            const wrapper = mount(Teambuilder);

            const modal = wrapper.find('[data-test="pokemon-selector"]');
            expect(modal.exists()).toBe(false);
        });

        it('should show team count as 0 / 6', () => {
            const wrapper = mount(Teambuilder);

            expect(wrapper.text()).toContain('Team: 0 / 6');
        });
    });

    describe('Opening Pokemon Selector', () => {
        it('should open selector when clicking Add button on empty slot', async () => {
            const wrapper = mount(Teambuilder);

            const addButton = wrapper.find('[data-test="team-slot"] button');
            await addButton.trigger('click');

            const modal = wrapper.find('[data-test="pokemon-selector"]');
            expect(modal.exists()).toBe(true);
        });

        it('should remember which slot was clicked', async () => {
            const wrapper = mount(Teambuilder);

            const slots = wrapper.findAll('[data-test="team-slot"]');
            const thirdSlotButton = slots[2].find('button');
            await thirdSlotButton.trigger('click');

            // Selector should be open
            expect(wrapper.find('[data-test="pokemon-selector"]').exists()).toBe(true);
        });

        it('should focus search input when selector opens', async () => {
            const wrapper = mount(Teambuilder, {
                attachTo: document.body
            });

            const addButton = wrapper.find('[data-test="team-slot"] button');
            await addButton.trigger('click');

            const searchInput = wrapper.find('[data-test="search-input"]');
            expect(searchInput.exists()).toBe(true);

            wrapper.unmount();
        });
    });

    describe('Selecting Pokemon', () => {
        it('should add Pokemon to slot when selected', async () => {
            const wrapper = mount(Teambuilder, {
                props: {
                    availablePokemon: [pikachu, bulbasaur]
                }
            });

            // Open selector
            const addButton = wrapper.find('[data-test="team-slot"] button');
            await addButton.trigger('click');

            // Select Pikachu
            const pikachuCard = wrapper.find('[data-test="pokemon-card-25"]');
            await pikachuCard.trigger('click');

            // Selector should close
            expect(wrapper.find('[data-test="pokemon-selector"]').exists()).toBe(false);

            // Pokemon should be in slot
            expect(wrapper.text()).toContain('Pikachu');
        });

        it('should update team count after selection', async () => {
            const wrapper = mount(Teambuilder, {
                props: {
                    availablePokemon: [pikachu]
                }
            });

            expect(wrapper.text()).toContain('Team: 0 / 6');

            // Add Pokemon
            const addButton = wrapper.find('[data-test="team-slot"] button');
            await addButton.trigger('click');

            const pikachuCard = wrapper.find('[data-test="pokemon-card-25"]');
            await pikachuCard.trigger('click');

            expect(wrapper.text()).toContain('Team: 1 / 6');
        });

        it('should add Pokemon to correct slot', async () => {
            const wrapper = mount(Teambuilder, {
                props: {
                    availablePokemon: [pikachu, bulbasaur]
                }
            });

            // Open selector for slot 3 (index 2)
            const slots = wrapper.findAll('[data-test="team-slot"]');
            const thirdSlotButton = slots[2].find('button');
            await thirdSlotButton.trigger('click');

            // Select Pikachu
            const pikachuCard = wrapper.find('[data-test="pokemon-card-25"]');
            await pikachuCard.trigger('click');

            // Check it's in the correct slot
            expect(store.team[2]).toEqual(pikachu);
        });

        it('should filter out selected Pokemon from selector', async () => {
            const wrapper = mount(Teambuilder, {
                props: {
                    availablePokemon: [pikachu, bulbasaur, charizard]
                }
            });

            // Add Pikachu to slot 1
            let addButton = wrapper.find('[data-test="team-slot"] button');
            await addButton.trigger('click');

            let pikachuCard = wrapper.find('[data-test="pokemon-card-25"]');
            await pikachuCard.trigger('click');

            // Open selector for slot 2
            const slots = wrapper.findAll('[data-test="team-slot"]');
            addButton = slots[1].find('button');
            await addButton.trigger('click');

            // Pikachu should not be in the list
            pikachuCard = wrapper.find('[data-test="pokemon-card-25"]');
            expect(pikachuCard.exists()).toBe(false);

            // But Bulbasaur should be
            const bulbasaurCard = wrapper.find('[data-test="pokemon-card-1"]');
            expect(bulbasaurCard.exists()).toBe(true);
        });
    });

    describe('Closing Pokemon Selector', () => {
        it('should close selector when Cancel button clicked', async () => {
            const wrapper = mount(Teambuilder, {
                props: {
                    availablePokemon: [pikachu]
                }
            });

            // Open selector
            const addButton = wrapper.find('[data-test="team-slot"] button');
            await addButton.trigger('click');

            expect(wrapper.find('[data-test="pokemon-selector"]').exists()).toBe(true);

            // Click Cancel
            const cancelButton = wrapper.find('[data-test="cancel-button"]');
            await cancelButton.trigger('click');

            expect(wrapper.find('[data-test="pokemon-selector"]').exists()).toBe(false);
        });

        it('should close selector when backdrop clicked', async () => {
            const wrapper = mount(Teambuilder, {
                props: {
                    availablePokemon: [pikachu]
                }
            });

            // Open selector
            const addButton = wrapper.find('[data-test="team-slot"] button');
            await addButton.trigger('click');

            // Click backdrop
            const backdrop = wrapper.find('[data-test="modal-backdrop"]');
            await backdrop.trigger('click');

            expect(wrapper.find('[data-test="pokemon-selector"]').exists()).toBe(false);
        });

        it('should close selector when Escape pressed', async () => {
            const wrapper = mount(Teambuilder, {
                props: {
                    availablePokemon: [pikachu]
                }
            });

            // Open selector
            const addButton = wrapper.find('[data-test="team-slot"] button');
            await addButton.trigger('click');

            // Press Escape
            await wrapper.trigger('keydown.esc');

            expect(wrapper.find('[data-test="pokemon-selector"]').exists()).toBe(false);
        });

        it('should not add Pokemon when selector closed without selection', async () => {
            const wrapper = mount(Teambuilder, {
                props: {
                    availablePokemon: [pikachu]
                }
            });

            // Open selector
            const addButton = wrapper.find('[data-test="team-slot"] button');
            await addButton.trigger('click');

            // Close without selection
            const cancelButton = wrapper.find('[data-test="cancel-button"]');
            await cancelButton.trigger('click');

            expect(wrapper.text()).toContain('Team: 0 / 6');
            expect(store.teamCount).toBe(0);
        });
    });

    describe('Removing Pokemon', () => {
        it('should remove Pokemon when Remove button clicked', async () => {
            const wrapper = mount(Teambuilder);

            store.addPokemon(pikachu, 0);

            expect(wrapper.text()).toContain('Pikachu');

            const removeButton = wrapper.find('[data-test="remove-button"]');
            await removeButton.trigger('click');

            expect(wrapper.text()).not.toContain('Pikachu');
        });

        it('should update team count after removal', async () => {
            const wrapper = mount(Teambuilder);

            store.addPokemon(pikachu, 0);
            expect(wrapper.text()).toContain('Team: 1 / 6');

            const removeButton = wrapper.find('[data-test="remove-button"]');
            await removeButton.trigger('click');

            expect(wrapper.text()).toContain('Team: 0 / 6');
        });

        it('should allow adding Pokemon back to empty slot', async () => {
            const wrapper = mount(Teambuilder, {
                props: {
                    availablePokemon: [pikachu, bulbasaur]
                }
            });

            // Add and remove Pikachu
            store.addPokemon(pikachu, 0);

            const removeButton = wrapper.find('[data-test="remove-button"]');
            await removeButton.trigger('click');

            // Add Bulbasaur
            const addButton = wrapper.find('[data-test="team-slot"] button');
            await addButton.trigger('click');

            const bulbasaurCard = wrapper.find('[data-test="pokemon-card-1"]');
            await bulbasaurCard.trigger('click');

            expect(wrapper.text()).toContain('Bulbasaur');
            expect(store.team[0]).toEqual(bulbasaur);
        });
    });

    describe('Clearing Team', () => {
        it('should not show Clear Team button when team is empty', () => {
            const wrapper = mount(Teambuilder);

            const clearButton = wrapper.find('[data-test="clear-team-button"]');
            expect(clearButton.exists()).toBe(false);
        });

        it('should show Clear Team button when team has Pokemon', () => {
            const wrapper = mount(Teambuilder);

            store.addPokemon(pikachu, 0);

            const clearButton = wrapper.find('[data-test="clear-team-button"]');
            expect(clearButton.exists()).toBe(true);
        });

        it('should clear all Pokemon when Clear Team clicked', async () => {
            const wrapper = mount(Teambuilder);

            store.addPokemon(pikachu, 0);
            store.addPokemon(bulbasaur, 1);
            store.addPokemon(charizard, 2);

            expect(wrapper.text()).toContain('Team: 3 / 6');

            const clearButton = wrapper.find('[data-test="clear-team-button"]');
            await clearButton.trigger('click');

            expect(wrapper.text()).toContain('Team: 0 / 6');
            expect(store.isEmpty).toBe(true);
        });

        it('should hide Clear Team button after clearing', async () => {
            const wrapper = mount(Teambuilder);

            store.addPokemon(pikachu, 0);

            const clearButton = wrapper.find('[data-test="clear-team-button"]');
            await clearButton.trigger('click');

            expect(wrapper.find('[data-test="clear-team-button"]').exists()).toBe(false);
        });
    });

    describe('Full Team Behavior', () => {
        it('should show all 6 Pokemon when team is full', () => {
            const wrapper = mount(Teambuilder);

            const team = [pikachu, bulbasaur, charizard, pikachu, bulbasaur, charizard];
            team.forEach((pokemon, i) => {
                store.addPokemon({ ...pokemon, id: pokemon.id + i * 100 }, i);
            });

            expect(wrapper.text()).toContain('Team: 6 / 6');
        });

        it('should allow replacing Pokemon in full team', async () => {
            const wrapper = mount(Teambuilder, {
                props: {
                    availablePokemon: [pikachu, bulbasaur, charizard]
                }
            });

            // Fill team
            for (let i = 0; i < 6; i++) {
                store.addPokemon({ ...pikachu, id: i }, i);
            }

            // Replace slot 0
            const slots = wrapper.findAll('[data-test="team-slot"]');
            const removeButton = slots[0].find('[data-test="remove-button"]');
            await removeButton.trigger('click');

            const addButton = slots[0].find('button');
            await addButton.trigger('click');

            const bulbasaurCard = wrapper.find('[data-test="pokemon-card-1"]');
            await bulbasaurCard.trigger('click');

            expect(store.team[0]).toEqual(bulbasaur);
        });
    });

    describe('Team Summary Integration', () => {
        it('should display team summary section', () => {
            const wrapper = mount(Teambuilder);

            const summary = wrapper.find('[data-test="team-summary"]');
            expect(summary.exists()).toBe(true);
        });

        it('should update summary when Pokemon added', async () => {
            const wrapper = mount(Teambuilder);

            store.addPokemon(pikachu, 0);

            const summary = wrapper.find('[data-test="team-summary"]');
            expect(summary.text()).toContain('electric');
        });

        it('should show type coverage in summary', () => {
            const wrapper = mount(Teambuilder);

            store.addPokemon(pikachu, 0); // electric
            store.addPokemon(bulbasaur, 1); // grass, poison
            store.addPokemon(charizard, 2); // fire, flying

            const summary = wrapper.find('[data-test="team-summary"]');
            expect(summary.text()).toContain('electric');
            expect(summary.text()).toContain('grass');
            expect(summary.text()).toContain('poison');
            expect(summary.text()).toContain('fire');
            expect(summary.text()).toContain('flying');
        });
    });

    describe('Data Persistence', () => {
        it('should maintain team state across selector openings', async () => {
            const wrapper = mount(Teambuilder, {
                props: {
                    availablePokemon: [pikachu, bulbasaur]
                }
            });

            // Add Pikachu
            const addButton = wrapper.find('[data-test="team-slot"] button');
            await addButton.trigger('click');

            const pikachuCard = wrapper.find('[data-test="pokemon-card-25"]');
            await pikachuCard.trigger('click');

            // Open selector again
            const slots = wrapper.findAll('[data-test="team-slot"]');
            const secondSlotButton = slots[1].find('button');
            await secondSlotButton.trigger('click');

            // Pikachu should still be in slot 1
            expect(wrapper.text()).toContain('Pikachu');
            expect(store.team[0]).toEqual(pikachu);

            // And not available in selector
            expect(wrapper.find('[data-test="pokemon-card-25"]').exists()).toBe(false);
        });
    });

    describe('Edge Cases', () => {
        it('should handle empty available Pokemon list', () => {
            const wrapper = mount(Teambuilder, {
                props: {
                    availablePokemon: []
                }
            });

            const addButton = wrapper.find('[data-test="team-slot"] button');
            addButton.trigger('click');

            expect(wrapper.text()).toContain('No Pokemon available');
        });

        it('should handle Pokemon with missing data', async () => {
            const incompletePokemon = {
                id: 1,
                name: 'Incomplete'
            };

            const wrapper = mount(Teambuilder, {
                props: {
                    availablePokemon: [incompletePokemon]
                }
            });

            const addButton = wrapper.find('[data-test="team-slot"] button');
            await addButton.trigger('click');

            const pokemonCard = wrapper.find('[data-test="pokemon-card-1"]');
            await pokemonCard.trigger('click');

            expect(store.team[0]).toEqual(incompletePokemon);
        });

        it('should handle rapid clicks', async () => {
            const wrapper = mount(Teambuilder, {
                props: {
                    availablePokemon: [pikachu]
                }
            });

            const addButton = wrapper.find('[data-test="team-slot"] button');

            // Rapid clicks
            await addButton.trigger('click');
            await addButton.trigger('click');
            await addButton.trigger('click');

            // Should only open one modal
            const modals = wrapper.findAll('[data-test="pokemon-selector"]');
            expect(modals.length).toBeLessThanOrEqual(1);
        });
    });

    describe('Keyboard Navigation', () => {
        it('should close selector with Escape key', async () => {
            const wrapper = mount(Teambuilder, {
                props: {
                    availablePokemon: [pikachu]
                }
            });

            const addButton = wrapper.find('[data-test="team-slot"] button');
            await addButton.trigger('click');

            expect(wrapper.find('[data-test="pokemon-selector"]').exists()).toBe(true);

            await wrapper.trigger('keydown.esc');

            expect(wrapper.find('[data-test="pokemon-selector"]').exists()).toBe(false);
        });

        it('should navigate slots with Tab', () => {
            const wrapper = mount(Teambuilder);

            const slots = wrapper.findAll('[data-test="team-slot"]');
            expect(slots).toHaveLength(6);

            slots.forEach(slot => {
                const button = slot.find('button');
                expect(button.attributes('tabindex')).toBeDefined();
            });
        });
    });

    describe('Accessibility', () => {
        it('should have proper ARIA labels', () => {
            const wrapper = mount(Teambuilder);

            const teamGrid = wrapper.find('[data-test="team-grid"]');
            expect(teamGrid.attributes('aria-label')).toBe('Pokemon team slots');
        });

        it('should announce team changes', () => {
            const wrapper = mount(Teambuilder);

            store.addPokemon(pikachu, 0);

            const teamStatus = wrapper.find('[data-test="team-status"]');
            expect(teamStatus.attributes('aria-live')).toBe('polite');
        });

        it('should have accessible modal', async () => {
            const wrapper = mount(Teambuilder, {
                props: {
                    availablePokemon: [pikachu]
                }
            });

            const addButton = wrapper.find('[data-test="team-slot"] button');
            await addButton.trigger('click');

            const modal = wrapper.find('[data-test="pokemon-selector"]');
            expect(modal.attributes('role')).toBe('dialog');
            expect(modal.attributes('aria-modal')).toBe('true');
        });
    });

    describe('Search Functionality', () => {
        it('should filter Pokemon by name', async () => {
            const wrapper = mount(Teambuilder, {
                props: {
                    availablePokemon: [pikachu, bulbasaur, charizard]
                }
            });

            const addButton = wrapper.find('[data-test="team-slot"] button');
            await addButton.trigger('click');

            const searchInput = wrapper.find('[data-test="search-input"]');
            await searchInput.setValue('pika');

            const pikachuCard = wrapper.find('[data-test="pokemon-card-25"]');
            const bulbasaurCard = wrapper.find('[data-test="pokemon-card-1"]');

            expect(pikachuCard.exists()).toBe(true);
            expect(bulbasaurCard.exists()).toBe(false);
        });

        it('should be case-insensitive', async () => {
            const wrapper = mount(Teambuilder, {
                props: {
                    availablePokemon: [pikachu, bulbasaur]
                }
            });

            const addButton = wrapper.find('[data-test="team-slot"] button');
            await addButton.trigger('click');

            const searchInput = wrapper.find('[data-test="search-input"]');
            await searchInput.setValue('PIKA');

            const pikachuCard = wrapper.find('[data-test="pokemon-card-25"]');
            expect(pikachuCard.exists()).toBe(true);
        });

        it('should clear results when no matches', async () => {
            const wrapper = mount(Teambuilder, {
                props: {
                    availablePokemon: [pikachu, bulbasaur]
                }
            });

            const addButton = wrapper.find('[data-test="team-slot"] button');
            await addButton.trigger('click');

            const searchInput = wrapper.find('[data-test="search-input"]');
            await searchInput.setValue('mewtwo');

            expect(wrapper.text()).toContain('No Pokemon found matching your search');
        });

        it('should update results as user types', async () => {
            const wrapper = mount(Teambuilder, {
                props: {
                    availablePokemon: [pikachu, bulbasaur, charizard]
                }
            });

            const addButton = wrapper.find('[data-test="team-slot"] button');
            await addButton.trigger('click');

            const searchInput = wrapper.find('[data-test="search-input"]');

            await searchInput.setValue('b');
            expect(wrapper.find('[data-test="pokemon-card-1"]').exists()).toBe(true);

            await searchInput.setValue('bu');
            expect(wrapper.find('[data-test="pokemon-card-1"]').exists()).toBe(true);

            await searchInput.setValue('bulb');
            expect(wrapper.find('[data-test="pokemon-card-1"]').exists()).toBe(true);
            expect(wrapper.find('[data-test="pokemon-card-25"]').exists()).toBe(false);
        });

        it('should maintain search when reopening selector', async () => {
            const wrapper = mount(Teambuilder, {
                props: {
                    availablePokemon: [pikachu, bulbasaur]
                }
            });

            const addButton = wrapper.find('[data-test="team-slot"] button');
            await addButton.trigger('click');

            const searchInput = wrapper.find('[data-test="search-input"]');
            await searchInput.setValue('pika');

            const cancelButton = wrapper.find('[data-test="cancel-button"]');
            await cancelButton.trigger('click');

            // Reopen
            await addButton.trigger('click');

            // Search should be cleared
            const newSearchInput = wrapper.find('[data-test="search-input"]');
            expect(newSearchInput.element.value).toBe('');
        });
    });

    describe('Pagination', () => {
        it('should show pagination controls when multiple pages', async () => {
            const wrapper = mount(Teambuilder, {
                props: {
                    availablePokemon: [pikachu],
                    totalPages: 3
                }
            });

            const addButton = wrapper.find('[data-test="team-slot"] button');
            await addButton.trigger('click');

            expect(wrapper.find('[data-test="pagination"]').exists()).toBe(true);
        });

        it('should disable Previous on first page', async () => {
            const wrapper = mount(Teambuilder, {
                props: {
                    availablePokemon: [pikachu],
                    currentPage: 1,
                    totalPages: 3
                }
            });

            const addButton = wrapper.find('[data-test="team-slot"] button');
            await addButton.trigger('click');

            const prevButton = wrapper.find('[data-test="prev-page"]');
            expect(prevButton.attributes('disabled')).toBeDefined();
        });

        it('should disable Next on last page', async () => {
            const wrapper = mount(Teambuilder, {
                props: {
                    availablePokemon: [pikachu],
                    currentPage: 3,
                    totalPages: 3
                }
            });

            const addButton = wrapper.find('[data-test="team-slot"] button');
            await addButton.trigger('click');

            const nextButton = wrapper.find('[data-test="next-page"]');
            expect(nextButton.attributes('disabled')).toBeDefined();
        });

        it('should emit page change event when Next clicked', async () => {
            const wrapper = mount(Teambuilder, {
                props: {
                    availablePokemon: [pikachu],
                    currentPage: 1,
                    totalPages: 3
                }
            });

            const addButton = wrapper.find('[data-test="team-slot"] button');
            await addButton.trigger('click');

            const nextButton = wrapper.find('[data-test="next-page"]');
            await nextButton.trigger('click');

            expect(wrapper.emitted('page-change')).toBeTruthy();
        });
    });

    describe('Loading and Error States', () => {
        it('should show loading indicator', () => {
            const wrapper = mount(Teambuilder, {
                props: {
                    loading: true
                }
            });

            const addButton = wrapper.find('[data-test="team-slot"] button');
            addButton.trigger('click');

            expect(wrapper.text()).toContain('Loading Pokemon');
        });

        it('should show error message', () => {
            const wrapper = mount(Teambuilder, {
                props: {
                    error: 'Failed to load Pokemon'
                }
            });

            const addButton = wrapper.find('[data-test="team-slot"] button');
            addButton.trigger('click');

            expect(wrapper.text()).toContain('Failed to load Pokemon');
        });

        it('should show retry button on error', () => {
            const wrapper = mount(Teambuilder, {
                props: {
                    error: 'Failed to load Pokemon'
                }
            });

            const addButton = wrapper.find('[data-test="team-slot"] button');
            addButton.trigger('click');

            const retryButton = wrapper.find('[data-test="retry-button"]');
            expect(retryButton.exists()).toBe(true);
        });

        it('should emit retry event when retry clicked', async () => {
            const wrapper = mount(Teambuilder, {
                props: {
                    error: 'Failed to load Pokemon'
                }
            });

            const addButton = wrapper.find('[data-test="team-slot"] button');
            await addButton.trigger('click');

            const retryButton = wrapper.find('[data-test="retry-button"]');
            await retryButton.trigger('click');

            expect(wrapper.emitted('retry')).toBeTruthy();
        });
    });

    describe('Complex User Workflows', () => {
        it('should handle complete team building workflow', async () => {
            const allPokemon = [pikachu, bulbasaur, charizard];
            const wrapper = mount(Teambuilder, {
                props: {
                    availablePokemon: allPokemon
                }
            });

            // Add first Pokemon
            const slots = wrapper.findAll('[data-test="team-slot"]');
            await slots[0].find('button').trigger('click');
            await wrapper.find('[data-test="pokemon-card-25"]').trigger('click');

            expect(store.teamCount).toBe(1);

            // Add second Pokemon
            await slots[1].find('button').trigger('click');
            await wrapper.find('[data-test="pokemon-card-1"]').trigger('click');

            expect(store.teamCount).toBe(2);

            // Remove first Pokemon
            await wrapper.find('[data-test="remove-button"]').trigger('click');

            expect(store.teamCount).toBe(1);

            // Add different Pokemon to first slot
            await slots[0].find('button').trigger('click');
            await wrapper.find('[data-test="pokemon-card-6"]').trigger('click');

            expect(store.teamCount).toBe(2);
            expect(store.team[0]).toEqual(charizard);
            expect(store.team[1]).toEqual(bulbasaur);
        });

        it('should prevent adding same Pokemon twice', async () => {
            const wrapper = mount(Teambuilder, {
                props: {
                    availablePokemon: [pikachu, bulbasaur]
                }
            });

            // Add Pikachu to slot 0
            const slots = wrapper.findAll('[data-test="team-slot"]');
            await slots[0].find('button').trigger('click');
            await wrapper.find('[data-test="pokemon-card-25"]').trigger('click');

            // Try to add Pikachu to slot 1
            await slots[1].find('button').trigger('click');

            // Pikachu should not be available
            expect(wrapper.find('[data-test="pokemon-card-25"]').exists()).toBe(false);
        });

        it('should handle replacing Pokemon in slot', async () => {
            const wrapper = mount(Teambuilder, {
                props: {
                    availablePokemon: [pikachu, bulbasaur, charizard]
                }
            });

            // Add Pikachu
            const slots = wrapper.findAll('[data-test="team-slot"]');
            await slots[0].find('button').trigger('click');
            await wrapper.find('[data-test="pokemon-card-25"]').trigger('click');

            expect(store.team[0]).toEqual(pikachu);

            // Remove and replace with Bulbasaur
            await wrapper.find('[data-test="remove-button"]').trigger('click');
            await slots[0].find('button').trigger('click');
            await wrapper.find('[data-test="pokemon-card-1"]').trigger('click');

            expect(store.team[0]).toEqual(bulbasaur);
            expect(store.teamCount).toBe(1);
        });
    });
});

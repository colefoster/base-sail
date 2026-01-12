import axios from 'axios';

const API_BASE = '/api/pokemon';

/**
 * Fetch all Pokemon from the database
 * @param {Object} params - Query parameters (search, type, generation, page)
 * @returns {Promise} Axios response with Pokemon data
 */
export async function fetchPokemon(params = {}) {
    const response = await axios.get(API_BASE, { params });
    return response.data;
}

/**
 * Fetch a single Pokemon by ID
 * @param {number} id - Pokemon API ID
 * @returns {Promise} Axios response with Pokemon data
 */
export async function fetchPokemonById(id) {
    const response = await axios.get(`${API_BASE}/${id}`);
    return response.data.data;
}

/**
 * Fetch all available types for filtering
 * @returns {Promise} Axios response with type names
 */
export async function fetchTypes() {
    const response = await axios.get(`${API_BASE}/types`);
    return response.data;
}

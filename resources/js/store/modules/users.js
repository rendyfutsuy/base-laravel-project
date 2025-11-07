import userManagementService from '../../services/userManagement';

const state = {
    users: [],
    pagination: null,
    loading: false,
};

const getters = {
    users: (state) => state.users,
    pagination: (state) => state.pagination,
    loading: (state) => state.loading,
};

const mutations = {
    SET_USERS(state, users) {
        state.users = users;
    },
    SET_PAGINATION(state, pagination) {
        state.pagination = pagination;
    },
    SET_LOADING(state, loading) {
        state.loading = loading;
    },
};

const actions = {
    async fetchUsers({ commit }, params = {}) {
        commit('SET_LOADING', true);
        try {
            // Same as testing: getJson() returns { data: [...], links: {...}, meta: {...} }
            const response = await userManagementService.getUsers(params);
            
            // Laravel ResourceCollection format: { data: [...], links: {...}, meta: {...} }
            commit('SET_USERS', response.data || []);
            commit('SET_PAGINATION', response.meta || null);
            return { success: true, data: response };
        } catch (error) {
            const errorMessage = error.response?.data?.message || 
                                error.response?.data?.error || 
                                error.message || 
                                'Failed to fetch users';
            return { success: false, error: errorMessage };
        } finally {
            commit('SET_LOADING', false);
        }
    },

    async createUser({ dispatch }, userData) {
        try {
            await userManagementService.createUser(userData);
            await dispatch('fetchUsers');
            return { success: true };
        } catch (error) {
            const errorMessage = error.response?.data?.message || error.response?.data?.errors || 'Failed to create user';
            return { success: false, error: errorMessage };
        }
    },

    async updateUser({ dispatch }, { id, ...userData }) {
        try {
            await userManagementService.updateUser(id, userData);
            await dispatch('fetchUsers');
            return { success: true };
        } catch (error) {
            const errorMessage = error.response?.data?.message || error.response?.data?.errors || 'Failed to update user';
            return { success: false, error: errorMessage };
        }
    },

    async deleteUser({ dispatch }, id) {
        try {
            await userManagementService.deleteUser(id);
            await dispatch('fetchUsers');
            return { success: true };
        } catch (error) {
            const errorMessage = error.response?.data?.message || 'Failed to delete user';
            return { success: false, error: errorMessage };
        }
    },
};

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions,
};

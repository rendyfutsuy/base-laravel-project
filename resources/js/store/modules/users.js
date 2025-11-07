import api from '../../services/api';

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
    ADD_USER(state, user) {
        state.users.unshift(user);
    },
    UPDATE_USER(state, user) {
        const index = state.users.findIndex((u) => u.id === user.id);
        if (index !== -1) {
            state.users.splice(index, 1, user);
        }
    },
    REMOVE_USER(state, userId) {
        state.users = state.users.filter((u) => u.id !== userId);
    },
};

const actions = {
    async fetchUsers({ commit }, params = {}) {
        commit('SET_LOADING', true);
        try {
            const response = await api.get('/api/v1/user-management/users', { params });
            commit('SET_USERS', response.data.data || []);
            commit('SET_PAGINATION', {
                current_page: response.data.meta?.current_page || 1,
                last_page: response.data.meta?.last_page || 1,
                per_page: response.data.meta?.per_page || 10,
                total: response.data.meta?.total || 0,
            });
            return { success: true, data: response.data };
        } catch (error) {
            return { success: false, error: error.response?.data?.message || 'Failed to fetch users' };
        } finally {
            commit('SET_LOADING', false);
        }
    },
    
    async createUser({ commit }, userData) {
        try {
            const response = await api.post('/api/v1/user-management/users', userData);
            commit('ADD_USER', response.data);
            return { success: true, data: response.data };
        } catch (error) {
            return { success: false, error: error.response?.data?.message || 'Failed to create user' };
        }
    },
    
    async updateUser({ commit }, { id, ...userData }) {
        try {
            const response = await api.put(`/api/v1/user-management/users/${id}`, userData);
            commit('UPDATE_USER', response.data);
            return { success: true, data: response.data };
        } catch (error) {
            return { success: false, error: error.response?.data?.message || 'Failed to update user' };
        }
    },
    
    async deleteUser({ commit }, userId) {
        try {
            await api.delete(`/api/v1/user-management/users/${userId}`);
            commit('REMOVE_USER', userId);
            return { success: true };
        } catch (error) {
            return { success: false, error: error.response?.data?.message || 'Failed to delete user' };
        }
    },
    
    async fetchUser({ commit }, userId) {
        try {
            const response = await api.get(`/api/v1/user-management/users/${userId}`);
            return { success: true, data: response.data };
        } catch (error) {
            return { success: false, error: error.response?.data?.message || 'Failed to fetch user' };
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


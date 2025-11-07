import api from '../../services/api';

const state = {
    roles: [],
    pagination: null,
    loading: false,
};

const getters = {
    roles: (state) => state.roles,
    pagination: (state) => state.pagination,
    loading: (state) => state.loading,
};

const mutations = {
    SET_ROLES(state, roles) {
        state.roles = roles;
    },
    SET_PAGINATION(state, pagination) {
        state.pagination = pagination;
    },
    SET_LOADING(state, loading) {
        state.loading = loading;
    },
    ADD_ROLE(state, role) {
        state.roles.unshift(role);
    },
    UPDATE_ROLE(state, role) {
        const index = state.roles.findIndex((r) => r.id === role.id);
        if (index !== -1) {
            state.roles.splice(index, 1, role);
        }
    },
    REMOVE_ROLE(state, roleId) {
        state.roles = state.roles.filter((r) => r.id !== roleId);
    },
};

const actions = {
    async fetchRoles({ commit }, params = {}) {
        commit('SET_LOADING', true);
        try {
            const response = await api.get('/api/hierarchy/roles', { params });
            commit('SET_ROLES', response.data.data || []);
            commit('SET_PAGINATION', {
                current_page: response.data.meta?.current_page || 1,
                last_page: response.data.meta?.last_page || 1,
                per_page: response.data.meta?.per_page || 10,
                total: response.data.meta?.total || 0,
            });
            return { success: true, data: response.data };
        } catch (error) {
            return { success: false, error: error.response?.data?.message || 'Failed to fetch roles' };
        } finally {
            commit('SET_LOADING', false);
        }
    },
    
    async createRole({ commit }, roleData) {
        try {
            const response = await api.post('/api/hierarchy/roles', roleData);
            commit('ADD_ROLE', response.data);
            return { success: true, data: response.data };
        } catch (error) {
            return { success: false, error: error.response?.data?.message || 'Failed to create role' };
        }
    },
    
    async updateRole({ commit }, { id, ...roleData }) {
        try {
            const response = await api.put(`/api/hierarchy/roles/${id}`, roleData);
            commit('UPDATE_ROLE', response.data);
            return { success: true, data: response.data };
        } catch (error) {
            return { success: false, error: error.response?.data?.message || 'Failed to update role' };
        }
    },
    
    async deleteRole({ commit }, roleId) {
        try {
            await api.delete(`/api/hierarchy/roles/${roleId}`);
            commit('REMOVE_ROLE', roleId);
            return { success: true };
        } catch (error) {
            return { success: false, error: error.response?.data?.message || 'Failed to delete role' };
        }
    },
    
    async syncPermissions({ commit }, { roleId, permissions }) {
        try {
            const response = await api.post(`/api/hierarchy/roles/sync/${roleId}`, { permissions });
            commit('UPDATE_ROLE', response.data);
            return { success: true, data: response.data };
        } catch (error) {
            return { success: false, error: error.response?.data?.message || 'Failed to sync permissions' };
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


import api from '../../services/api';

const state = {
    permissions: [],
    pagination: null,
    loading: false,
};

const getters = {
    permissions: (state) => state.permissions,
    pagination: (state) => state.pagination,
    loading: (state) => state.loading,
};

const mutations = {
    SET_PERMISSIONS(state, permissions) {
        state.permissions = permissions;
    },
    SET_PAGINATION(state, pagination) {
        state.pagination = pagination;
    },
    SET_LOADING(state, loading) {
        state.loading = loading;
    },
    ADD_PERMISSION(state, permission) {
        state.permissions.unshift(permission);
    },
    UPDATE_PERMISSION(state, permission) {
        const index = state.permissions.findIndex((p) => p.id === permission.id);
        if (index !== -1) {
            state.permissions.splice(index, 1, permission);
        }
    },
    REMOVE_PERMISSION(state, permissionId) {
        state.permissions = state.permissions.filter((p) => p.id !== permissionId);
    },
};

const actions = {
    async fetchPermissions({ commit }, params = {}) {
        commit('SET_LOADING', true);
        try {
            const response = await api.get('/api/hierarchy/permissions', { params });
            commit('SET_PERMISSIONS', response.data.data || []);
            commit('SET_PAGINATION', {
                current_page: response.data.meta?.current_page || 1,
                last_page: response.data.meta?.last_page || 1,
                per_page: response.data.meta?.per_page || 10,
                total: response.data.meta?.total || 0,
            });
            return { success: true, data: response.data };
        } catch (error) {
            return { success: false, error: error.response?.data?.message || 'Failed to fetch permissions' };
        } finally {
            commit('SET_LOADING', false);
        }
    },
    
    async createPermission({ commit }, permissionData) {
        try {
            const response = await api.post('/api/hierarchy/permissions', permissionData);
            commit('ADD_PERMISSION', response.data);
            return { success: true, data: response.data };
        } catch (error) {
            return { success: false, error: error.response?.data?.message || 'Failed to create permission' };
        }
    },
    
    async updatePermission({ commit }, { id, ...permissionData }) {
        try {
            const response = await api.put(`/api/hierarchy/permissions/${id}`, permissionData);
            commit('UPDATE_PERMISSION', response.data);
            return { success: true, data: response.data };
        } catch (error) {
            return { success: false, error: error.response?.data?.message || 'Failed to update permission' };
        }
    },
    
    async deletePermission({ commit }, permissionId) {
        try {
            await api.delete(`/api/hierarchy/permissions/${permissionId}`);
            commit('REMOVE_PERMISSION', permissionId);
            return { success: true };
        } catch (error) {
            return { success: false, error: error.response?.data?.message || 'Failed to delete permission' };
        }
    },
    
    async resyncRoles({ commit }, { permissionId, roles }) {
        try {
            const response = await api.post(`/api/hierarchy/permissions/resync/${permissionId}`, { roles });
            commit('UPDATE_PERMISSION', response.data);
            return { success: true, data: response.data };
        } catch (error) {
            return { success: false, error: error.response?.data?.message || 'Failed to resync roles' };
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


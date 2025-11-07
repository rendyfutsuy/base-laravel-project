import hierarchyService from '../../services/hierarchy';

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
};

const actions = {
    async fetchPermissions({ commit }, params = {}) {
        commit('SET_LOADING', true);
        try {
            // Same as testing: getJson() returns { data: [...], links: {...}, meta: {...} }
            const response = await hierarchyService.getPermissions(params);
            
            // Laravel ResourceCollection format: { data: [...], links: {...}, meta: {...} }
            commit('SET_PERMISSIONS', response.data || []);
            commit('SET_PAGINATION', response.meta || null);
            return { success: true, data: response };
        } catch (error) {
            const errorMessage = error.response?.data?.message || 
                                error.response?.data?.error || 
                                error.message || 
                                'Failed to fetch permissions';
            return { success: false, error: errorMessage };
        } finally {
            commit('SET_LOADING', false);
        }
    },

    async createPermission({ dispatch }, permissionData) {
        try {
            await hierarchyService.createPermission(permissionData);
            await dispatch('fetchPermissions');
            return { success: true };
        } catch (error) {
            const errorMessage = error.response?.data?.message || error.response?.data?.errors || 'Failed to create permission';
            return { success: false, error: errorMessage };
        }
    },

    async updatePermission({ dispatch }, { id, ...permissionData }) {
        try {
            await hierarchyService.updatePermission(id, permissionData);
            await dispatch('fetchPermissions');
            return { success: true };
        } catch (error) {
            const errorMessage = error.response?.data?.message || error.response?.data?.errors || 'Failed to update permission';
            return { success: false, error: errorMessage };
        }
    },

    async deletePermission({ dispatch }, id) {
        try {
            await hierarchyService.deletePermission(id);
            await dispatch('fetchPermissions');
            return { success: true };
        } catch (error) {
            const errorMessage = error.response?.data?.message || 'Failed to delete permission';
            return { success: false, error: errorMessage };
        }
    },

    async syncRolesToPermission({ dispatch }, { permissionId, roleIds }) {
        try {
            await hierarchyService.syncRolesToPermission(permissionId, roleIds);
            await dispatch('fetchPermissions');
            return { success: true };
        } catch (error) {
            const errorMessage = error.response?.data?.message || 'Failed to sync roles';
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

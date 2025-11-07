import hierarchyService from '../../services/hierarchy';

const state = {
    roles: [],
    permissions: [],
    pagination: null,
    loading: false,
};

const getters = {
    roles: (state) => state.roles,
    permissions: (state) => state.permissions,
    pagination: (state) => state.pagination,
    loading: (state) => state.loading,
};

const mutations = {
    SET_ROLES(state, roles) {
        state.roles = roles;
    },
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
    async fetchRoles({ commit }, params = {}) {
        commit('SET_LOADING', true);
        try {
            // Same as testing: getJson() returns { data: [...], links: {...}, meta: {...} }
            const response = await hierarchyService.getRoles(params);
            
            // Laravel ResourceCollection format: { data: [...], links: {...}, meta: {...} }
            commit('SET_ROLES', response.data || []);
            commit('SET_PAGINATION', response.meta || null);
            return { success: true, data: response };
        } catch (error) {
            const errorMessage = error.response?.data?.message || 
                                error.response?.data?.error || 
                                error.message || 
                                'Failed to fetch roles';
            return { success: false, error: errorMessage };
        } finally {
            commit('SET_LOADING', false);
        }
    },

    async fetchPermissions({ commit }, params = {}) {
        try {
            // Same as testing: getJson() returns { data: [...], links: {...}, meta: {...} }
            const response = await hierarchyService.getPermissions(params);
            
            // Laravel ResourceCollection format: { data: [...], links: {...}, meta: {...} }
            commit('SET_PERMISSIONS', response.data || []);
            return { success: true, data: response };
        } catch (error) {
            const errorMessage = error.response?.data?.message || 
                                error.response?.data?.error || 
                                error.message || 
                                'Failed to fetch permissions';
            return { success: false, error: errorMessage };
        }
    },

    async createRole({ dispatch }, roleData) {
        try {
            await hierarchyService.createRole(roleData);
            await dispatch('fetchRoles');
            return { success: true };
        } catch (error) {
            const errorMessage = error.response?.data?.message || error.response?.data?.errors || 'Failed to create role';
            return { success: false, error: errorMessage };
        }
    },

    async updateRole({ dispatch }, { id, ...roleData }) {
        try {
            await hierarchyService.updateRole(id, roleData);
            await dispatch('fetchRoles');
            return { success: true };
        } catch (error) {
            const errorMessage = error.response?.data?.message || error.response?.data?.errors || 'Failed to update role';
            return { success: false, error: errorMessage };
        }
    },

    async deleteRole({ dispatch }, id) {
        try {
            await hierarchyService.deleteRole(id);
            await dispatch('fetchRoles');
            return { success: true };
        } catch (error) {
            const errorMessage = error.response?.data?.message || 'Failed to delete role';
            return { success: false, error: errorMessage };
        }
    },

    async syncPermissionsToRole({ dispatch }, { roleId, permissionIds }) {
        try {
            await hierarchyService.syncPermissionsToRole(roleId, permissionIds);
            await dispatch('fetchRoles');
            return { success: true };
        } catch (error) {
            const errorMessage = error.response?.data?.message || 'Failed to sync permissions';
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

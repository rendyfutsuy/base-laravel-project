import api from './api';

const hierarchyService = {
    // Roles
    async getRoles(params = {}) {
        const response = await api.get('/api/v1/hierarchy/roles', { params });
        return response.data;
    },

    async getRole(id) {
        const response = await api.get(`/api/v1/hierarchy/roles/${id}`);
        return response.data;
    },

    async createRole(roleData) {
        const response = await api.post('/api/v1/hierarchy/roles', roleData);
        return response.data;
    },

    async updateRole(id, roleData) {
        const response = await api.put(`/api/v1/hierarchy/roles/${id}`, roleData);
        return response.data;
    },

    async deleteRole(id) {
        const response = await api.delete(`/api/v1/hierarchy/roles/${id}`);
        return response.data;
    },

    async syncPermissionsToRole(roleId, permissionIds) {
        const response = await api.post(`/api/v1/hierarchy/roles/sync/${roleId}`, {
            permissions: permissionIds,
        });
        return response.data;
    },

    async syncRoleToUsers(roleId, userIds) {
        // Note: This endpoint requires both user and role IDs in the path
        // For now, we'll use the first user ID from the array
        const userId = Array.isArray(userIds) ? userIds[0] : userIds;
        const response = await api.post(`/api/v1/hierarchy/roles/user/resync/${userId}/${roleId}`, {
            users: userIds,
        });
        return response.data;
    },

    // Permissions
    async getPermissions(params = {}) {
        const response = await api.get('/api/v1/hierarchy/permissions', { params });
        return response.data;
    },

    async getPermission(id) {
        const response = await api.get(`/api/v1/hierarchy/permissions/${id}`);
        return response.data;
    },

    async createPermission(permissionData) {
        const response = await api.post('/api/v1/hierarchy/permissions', permissionData);
        return response.data;
    },

    async updatePermission(id, permissionData) {
        const response = await api.put(`/api/v1/hierarchy/permissions/${id}`, permissionData);
        return response.data;
    },

    async deletePermission(id) {
        const response = await api.delete(`/api/v1/hierarchy/permissions/${id}`);
        return response.data;
    },

    async syncRolesToPermission(permissionId, roleIds) {
        const response = await api.post(`/api/v1/hierarchy/permissions/resync/${permissionId}`, {
            roles: roleIds,
        });
        return response.data;
    },

    async syncPermissionToUsers(permissionId, userIds) {
        const response = await api.post(`/api/v1/hierarchy/permissions/resync/${permissionId}/to-user`, {
            users: userIds,
        });
        return response.data;
    },
};

export default hierarchyService;


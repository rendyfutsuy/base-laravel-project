import api from './api';

const userManagementService = {
    // User Management
    async getUsers(params = {}) {
        const response = await api.get('/api/v1/user-management/users', { params });
        return response.data;
    },

    async getUser(id) {
        const response = await api.get(`/api/v1/user-management/users/${id}`);
        return response.data;
    },

    async createUser(userData) {
        const response = await api.post('/api/v1/user-management/users', userData);
        return response.data;
    },

    async updateUser(id, userData) {
        const response = await api.put(`/api/v1/user-management/users/${id}`, userData);
        return response.data;
    },

    async deleteUser(id) {
        const response = await api.delete(`/api/v1/user-management/users/${id}`);
        return response.data;
    },

    // Staff Management
    async getStaffs(params = {}) {
        const response = await api.get('/api/v1/user-management/staff', { params });
        return response.data;
    },

    async getStaff(id) {
        const response = await api.get(`/api/v1/user-management/staff/${id}`);
        return response.data;
    },

    async createStaff(staffData) {
        const response = await api.post('/api/v1/user-management/staff', staffData);
        return response.data;
    },

    async updateStaff(id, staffData) {
        const response = await api.put(`/api/v1/user-management/staff/${id}`, staffData);
        return response.data;
    },

    async deleteStaff(id) {
        const response = await api.delete(`/api/v1/user-management/staff/${id}`);
        return response.data;
    },

    // Admin Management
    async getAdmins(params = {}) {
        const response = await api.get('/api/v1/user-management/admin', { params });
        return response.data;
    },

    async getAdmin(id) {
        const response = await api.get(`/api/v1/user-management/admin/${id}`);
        return response.data;
    },

    async createAdmin(adminData) {
        const response = await api.post('/api/v1/user-management/admin', adminData);
        return response.data;
    },

    async updateAdmin(id, adminData) {
        const response = await api.put(`/api/v1/user-management/admin/${id}`, adminData);
        return response.data;
    },

    async deleteAdmin(id) {
        const response = await api.delete(`/api/v1/user-management/admin/${id}`);
        return response.data;
    },
};

export default userManagementService;


import api from '../../services/api';

const state = {
    user: null,
    token: localStorage.getItem('access_token') || null,
    isAuthenticated: !!localStorage.getItem('access_token'),
};

const getters = {
    isAuthenticated: (state) => state.isAuthenticated,
    user: (state) => state.user,
    token: (state) => state.token,
};

const mutations = {
    SET_USER(state, user) {
        state.user = user;
    },
    SET_TOKEN(state, token) {
        state.token = token;
        state.isAuthenticated = !!token;
        if (token) {
            localStorage.setItem('access_token', token);
        } else {
            localStorage.removeItem('access_token');
        }
    },
    LOGOUT(state) {
        state.user = null;
        state.token = null;
        state.isAuthenticated = false;
        localStorage.removeItem('access_token');
    },
};

const actions = {
    async login({ commit }, credentials) {
        try {
            const response = await api.post('/api/authentication/login', credentials);
            const { access_token, id, name, email, roles } = response.data;
            
            commit('SET_TOKEN', access_token);
            commit('SET_USER', { id, name, email, roles });
            
            // Set axios default header
            api.defaults.headers.common['Authorization'] = `Bearer ${access_token}`;
            
            // Fetch full profile
            await this.dispatch('auth/fetchProfile');
            
            return { success: true };
        } catch (error) {
            return { success: false, error: error.response?.data?.message || 'Login failed' };
        }
    },
    
    async register({ commit }, userData) {
        try {
            const response = await api.post('/api/authentication/register', userData);
            return { success: true, data: response.data };
        } catch (error) {
            return { success: false, error: error.response?.data?.errors || 'Registration failed' };
        }
    },
    
    async logout({ commit }) {
        try {
            await api.post('/api/authentication/logout');
        } catch (error) {
            console.error('Logout error:', error);
        } finally {
            commit('LOGOUT');
            delete api.defaults.headers.common['Authorization'];
        }
    },
    
    async fetchProfile({ commit }) {
        try {
            const response = await api.get('/api/authentication/profile');
            commit('SET_USER', response.data);
            return { success: true, data: response.data };
        } catch (error) {
            return { success: false, error: error.response?.data?.message || 'Failed to fetch profile' };
        }
    },
    
    async updateProfile({ commit }, profileData) {
        try {
            const response = await api.put('/api/authentication/profile/detail', profileData);
            commit('SET_USER', response.data);
            return { success: true, data: response.data };
        } catch (error) {
            return { success: false, error: error.response?.data?.message || 'Failed to update profile' };
        }
    },
    
    async changePassword({ commit }, passwordData) {
        try {
            const response = await api.post('/api/authentication/profile/password', passwordData);
            return { success: true, data: response.data };
        } catch (error) {
            return { success: false, error: error.response?.data?.message || 'Failed to change password' };
        }
    },
    
    initAuth({ commit }) {
        const token = localStorage.getItem('access_token');
        if (token) {
            commit('SET_TOKEN', token);
            api.defaults.headers.common['Authorization'] = `Bearer ${token}`;
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


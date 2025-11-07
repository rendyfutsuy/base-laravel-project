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
            console.log('Login attempt with credentials:', { email: credentials.email });
            
            const response = await api.post('/api/v1/authentication/login', credentials);
            
            console.log('Login response:', response);
            console.log('Login response data:', response.data);
            
            // Handle different response structures
            let responseData = response.data;
            
            // If response has data property, use it
            if (responseData && responseData.data) {
                responseData = responseData.data;
            }
            
            // Extract tokens and user data
            const { access_token, id, name, email, refresh_token } = responseData || {};
            
            if (!access_token) {
                console.error('No access token in response:', responseData);
                return { success: false, error: 'Invalid response: No access token received' };
            }
            
            commit('SET_TOKEN', access_token);
            commit('SET_USER', { id, name, email });
            
            // Store refresh token
            if (refresh_token) {
                localStorage.setItem('refresh_token', refresh_token);
            }
            
            // Set axios default header
            api.defaults.headers.common['Authorization'] = `Bearer ${access_token}`;
            
            // Fetch full profile
            await this.dispatch('auth/fetchProfile');
            
            return { success: true };
        } catch (error) {
            console.error('Login error:', error);
            console.error('Login error response:', error.response);
            
            let errorMessage = 'Login failed';
            
            if (error.response) {
                if (error.response.data) {
                    if (typeof error.response.data === 'string') {
                        errorMessage = error.response.data;
                    } else if (error.response.data.message) {
                        errorMessage = error.response.data.message;
                    } else if (error.response.data.error) {
                        errorMessage = error.response.data.error;
                    } else if (error.response.data.errors) {
                        errorMessage = Array.isArray(error.response.data.errors) 
                            ? error.response.data.errors.join(', ') 
                            : JSON.stringify(error.response.data.errors);
                    }
                } else {
                    errorMessage = error.response.statusText || `HTTP ${error.response.status}`;
                }
            } else if (error.message) {
                errorMessage = error.message;
            }
            
            return { success: false, error: errorMessage };
        }
    },
    
    async register({ commit }, userData) {
        try {
            const response = await api.post('/api/v1/authentication/register', userData);
            return { success: true, data: response.data };
        } catch (error) {
            const errorMessage = error.response?.data?.message || error.response?.data?.errors || 'Registration failed';
            return { success: false, error: errorMessage };
        }
    },
    
    async logout({ commit }) {
        try {
            await api.post('/api/v1/authentication/logout');
        } catch (error) {
            console.error('Logout error:', error);
        } finally {
            commit('LOGOUT');
            localStorage.removeItem('refresh_token');
            delete api.defaults.headers.common['Authorization'];
        }
    },
    
    async fetchProfile({ commit }) {
        try {
            const response = await api.get('/api/v1/authentication/profile');
            commit('SET_USER', response.data);
            return { success: true, data: response.data };
        } catch (error) {
            // Throw error so router guard can catch it
            const errorMessage = error.response?.data?.message || 'Failed to fetch profile';
            throw new Error(errorMessage);
        }
    },
    
    async updateProfile({ commit }, profileData) {
        try {
            const response = await api.put('/api/v1/authentication/profile/detail', profileData);
            commit('SET_USER', response.data);
            return { success: true, data: response.data };
        } catch (error) {
            const errorMessage = error.response?.data?.message || error.response?.data?.errors || 'Failed to update profile';
            return { success: false, error: errorMessage };
        }
    },
    
    async changePassword({ commit }, passwordData) {
        try {
            const response = await api.post('/api/v1/authentication/profile/password', passwordData);
            return { success: true, data: response.data };
        } catch (error) {
            const errorMessage = error.response?.data?.message || error.response?.data?.errors || 'Failed to change password';
            return { success: false, error: errorMessage };
        }
    },
    
    initAuth({ commit, state }) {
        return new Promise((resolve) => {
            const token = localStorage.getItem('access_token');
            if (token && !state.isAuthenticated) {
                commit('SET_TOKEN', token);
                api.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            }
            resolve();
        });
    },
};

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions,
};


import api from '../../services/api';

const state = {
    notifications: [],
    pagination: null,
    unreadCount: 0,
    loading: false,
};

const getters = {
    notifications: (state) => state.notifications,
    pagination: (state) => state.pagination,
    unreadCount: (state) => state.unreadCount,
    loading: (state) => state.loading,
};

const mutations = {
    SET_NOTIFICATIONS(state, notifications) {
        state.notifications = notifications;
    },
    SET_PAGINATION(state, pagination) {
        state.pagination = pagination;
    },
    SET_UNREAD_COUNT(state, count) {
        state.unreadCount = count;
    },
    SET_LOADING(state, loading) {
        state.loading = loading;
    },
    REMOVE_NOTIFICATION(state, notificationId) {
        state.notifications = state.notifications.filter((n) => n.id !== notificationId);
    },
    MARK_AS_READ(state, notificationId) {
        const notification = state.notifications.find((n) => n.id === notificationId);
        if (notification) {
            notification.is_read = true;
            state.unreadCount = Math.max(0, state.unreadCount - 1);
        }
    },
};

const actions = {
    async fetchNotifications({ commit }, params = {}) {
        commit('SET_LOADING', true);
        try {
            const response = await api.get('/api/v1/mobile/notification', { params });
            commit('SET_NOTIFICATIONS', response.data.data || []);
            commit('SET_UNREAD_COUNT', response.data.unread_counter || 0);
            commit('SET_PAGINATION', {
                current_page: response.data.meta?.current_page || 1,
                last_page: response.data.meta?.last_page || 1,
                per_page: response.data.meta?.per_page || 10,
                total: response.data.meta?.total || 0,
            });
            return { success: true, data: response.data };
        } catch (error) {
            return { success: false, error: error.response?.data?.message || 'Failed to fetch notifications' };
        } finally {
            commit('SET_LOADING', false);
        }
    },
    
    async markAsRead({ commit }, notificationId) {
        try {
            await api.get('/api/v1/mobile/notification/read', { params: { id: notificationId } });
            commit('MARK_AS_READ', notificationId);
            return { success: true };
        } catch (error) {
            return { success: false, error: error.response?.data?.message || 'Failed to mark as read' };
        }
    },
    
    async deleteNotification({ commit }, notificationId) {
        try {
            await api.delete('/api/v1/mobile/notification', { params: { id: notificationId } });
            commit('REMOVE_NOTIFICATION', notificationId);
            return { success: true };
        } catch (error) {
            return { success: false, error: error.response?.data?.message || 'Failed to delete notification' };
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


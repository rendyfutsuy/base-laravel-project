import notificationService from '../../services/notification';

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
    MARK_AS_READ(state, notificationId) {
        const notification = state.notifications.find(n => n.id === notificationId);
        if (notification) {
            notification.is_read = true;
            state.unreadCount = Math.max(0, state.unreadCount - 1);
        }
    },
    REMOVE_NOTIFICATION(state, notificationId) {
        const index = state.notifications.findIndex(n => n.id === notificationId);
        if (index !== -1) {
            const notification = state.notifications[index];
            if (!notification.is_read) {
                state.unreadCount = Math.max(0, state.unreadCount - 1);
            }
            state.notifications.splice(index, 1);
        }
    },
};

const actions = {
    async fetchNotifications({ commit }, params = {}) {
        commit('SET_LOADING', true);
        try {
            // Same as testing: getJson() returns { data: [...], links: {...}, meta: {...}, unread_counter: ... }
            const response = await notificationService.getNotifications(params);
            
            // Laravel ResourceCollection format: { data: [...], links: {...}, meta: {...}, unread_counter: ... }
            commit('SET_NOTIFICATIONS', response.data || []);
            commit('SET_PAGINATION', response.meta || null);
            commit('SET_UNREAD_COUNT', response.unread_counter || 0);
            return { success: true, data: response };
        } catch (error) {
            const errorMessage = error.response?.data?.message || 
                                error.response?.data?.error || 
                                error.message || 
                                'Failed to fetch notifications';
            return { success: false, error: errorMessage };
        } finally {
            commit('SET_LOADING', false);
        }
    },

    async markAsRead({ commit }, notificationId) {
        try {
            await notificationService.markAsRead(notificationId);
            commit('MARK_AS_READ', notificationId);
            return { success: true };
        } catch (error) {
            const errorMessage = error.response?.data?.message || 'Failed to mark as read';
            return { success: false, error: errorMessage };
        }
    },

    async deleteNotification({ commit }, notificationId) {
        try {
            await notificationService.deleteNotification(notificationId);
            commit('REMOVE_NOTIFICATION', notificationId);
            return { success: true };
        } catch (error) {
            const errorMessage = error.response?.data?.message || 'Failed to delete notification';
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

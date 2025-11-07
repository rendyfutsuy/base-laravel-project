import api from './api';

const notificationService = {
    async getNotifications(params = {}) {
        const response = await api.get('/api/v1/notification', { params });
        return response.data;
    },

    async markAsRead(notificationId) {
        const response = await api.post('/api/v1/notification/read', { id: notificationId });
        return response.data;
    },

    async deleteNotification(notificationId) {
        const response = await api.post('/api/v1/notification/delete', { id: notificationId });
        return response.data;
    },

    async getUnreadCount() {
        const response = await api.get('/api/v1/notification', { params: { row: 1, page: 1 } });
        return response.data?.unread_counter || 0;
    },
};

export default notificationService;


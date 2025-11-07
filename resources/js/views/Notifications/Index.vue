<template>
    <div class="notifications-page">
        <div class="page-header">
            <h2>Notifications</h2>
            <div class="header-info">
                <span class="unread-badge" v-if="unreadCount > 0">
                    {{ unreadCount }} unread
                </span>
            </div>
        </div>
        
        <div class="notifications-list">
            <div v-if="loading" class="loading-state">
                <p>Loading notifications...</p>
            </div>
            
            <div v-else-if="notifications.length === 0" class="empty-state">
                <p>No notifications found</p>
            </div>
            
            <div v-else class="notification-items">
                <div
                    v-for="notification in notifications"
                    :key="notification.id"
                    :class="['notification-item', { 'unread': !notification.is_read }]"
                >
                    <div class="notification-content">
                        <h3 class="notification-title">{{ notification.title }}</h3>
                        <p class="notification-message">{{ notification.message }}</p>
                        <div class="notification-meta">
                            <span class="notification-type">{{ notification.type }}</span>
                            <span class="notification-date">{{ formatDate(notification.sent_at) }}</span>
                        </div>
                    </div>
                    
                    <div class="notification-actions">
                        <button
                            v-if="!notification.is_read"
                            @click="handleMarkAsRead(notification.id)"
                            class="btn btn-sm btn-mark-read"
                        >
                            Mark as Read
                        </button>
                        <button
                            @click="handleDeleteNotification(notification.id)"
                            class="btn btn-sm btn-delete"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div v-if="pagination" class="pagination">
            <button
                @click="fetchNotifications({ page: pagination.current_page - 1 })"
                :disabled="pagination.current_page === 1"
                class="btn btn-sm"
            >
                Previous
            </button>
            <span>Page {{ pagination.current_page }} of {{ pagination.last_page }}</span>
            <button
                @click="fetchNotifications({ page: pagination.current_page + 1 })"
                :disabled="pagination.current_page === pagination.last_page"
                class="btn btn-sm"
            >
                Next
            </button>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';

export default {
    name: 'Notifications',
    computed: {
        ...mapGetters('notifications', ['notifications', 'pagination', 'unreadCount', 'loading']),
    },
    mounted() {
        this.fetchNotifications();
    },
    methods: {
        ...mapActions('notifications', ['fetchNotifications', 'markAsRead', 'deleteNotification']),
        
        formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
            });
        },
        
        async handleMarkAsRead(notificationId) {
            const result = await this.markAsRead(notificationId);
            if (result.success) {
                this.fetchNotifications();
            } else {
                alert(result.error);
            }
        },
        
        async handleDeleteNotification(notificationId) {
            if (confirm('Are you sure you want to delete this notification?')) {
                const result = await this.deleteNotification(notificationId);
                if (result.success) {
                    this.fetchNotifications();
                } else {
                    alert(result.error);
                }
            }
        },
    },
};
</script>

<style scoped>
.notifications-page {
    max-width: 1000px;
    margin: 0 auto;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.page-header h2 {
    font-size: 24px;
    font-weight: 600;
    color: #333;
}

.header-info {
    display: flex;
    align-items: center;
    gap: 16px;
}

.unread-badge {
    background: #ef4444;
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.notifications-list {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.loading-state,
.empty-state {
    padding: 48px;
    text-align: center;
    color: #999;
}

.notification-items {
    display: flex;
    flex-direction: column;
}

.notification-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 20px;
    border-bottom: 1px solid #eee;
    transition: background-color 0.3s;
}

.notification-item:hover {
    background-color: #f8f9fa;
}

.notification-item.unread {
    background-color: #f0f9ff;
    border-left: 4px solid #667eea;
}

.notification-content {
    flex: 1;
}

.notification-title {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
}

.notification-message {
    font-size: 14px;
    color: #666;
    margin-bottom: 12px;
    line-height: 1.5;
}

.notification-meta {
    display: flex;
    gap: 16px;
    font-size: 12px;
    color: #999;
}

.notification-type {
    background: #e5e7eb;
    padding: 4px 8px;
    border-radius: 4px;
    font-weight: 500;
}

.notification-date {
    color: #999;
}

.notification-actions {
    display: flex;
    gap: 8px;
}

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 12px;
}

.btn-mark-read {
    background: #10b981;
    color: white;
}

.btn-mark-read:hover {
    background: #059669;
}

.btn-delete {
    background: #ef4444;
    color: white;
}

.btn-delete:hover {
    background: #dc2626;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 16px;
    margin-top: 24px;
}
</style>


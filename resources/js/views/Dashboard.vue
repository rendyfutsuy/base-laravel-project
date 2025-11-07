<template>
    <div class="dashboard">
        <div class="dashboard-header">
            <h2>Welcome to Dashboard</h2>
            <p>Manage your application from here</p>
        </div>
        
        <div class="dashboard-stats">
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-info">
                    <h3>{{ stats.users || 0 }}</h3>
                    <p>Total Users</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">🔐</div>
                <div class="stat-info">
                    <h3>{{ stats.roles || 0 }}</h3>
                    <p>Total Roles</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">🔑</div>
                <div class="stat-info">
                    <h3>{{ stats.permissions || 0 }}</h3>
                    <p>Total Permissions</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">🔔</div>
                <div class="stat-info">
                    <h3>{{ stats.notifications || 0 }}</h3>
                    <p>Unread Notifications</p>
                </div>
            </div>
        </div>
        
        <div class="dashboard-actions">
            <h3>Quick Actions</h3>
            <div class="action-grid">
                <router-link to="/users" class="action-card">
                    <span class="action-icon">👥</span>
                    <span class="action-text">Manage Users</span>
                </router-link>
                
                <router-link to="/roles" class="action-card">
                    <span class="action-icon">🔐</span>
                    <span class="action-text">Manage Roles</span>
                </router-link>
                
                <router-link to="/permissions" class="action-card">
                    <span class="action-icon">🔑</span>
                    <span class="action-text">Manage Permissions</span>
                </router-link>
                
                <router-link to="/notifications" class="action-card">
                    <span class="action-icon">🔔</span>
                    <span class="action-text">View Notifications</span>
                </router-link>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
    name: 'Dashboard',
    data() {
        return {
            stats: {
                users: 0,
                roles: 0,
                permissions: 0,
                notifications: 0,
            },
        };
    },
    computed: {
        ...mapGetters('auth', ['user']),
    },
    mounted() {
        this.fetchStats();
    },
    methods: {
        async fetchStats() {
            // Fetch stats from API
            // This is a placeholder - implement based on your API
            this.stats = {
                users: 0,
                roles: 0,
                permissions: 0,
                notifications: 0,
            };
        },
    },
};
</script>

<style scoped>
.dashboard {
    max-width: 1200px;
    margin: 0 auto;
}

.dashboard-header {
    margin-bottom: 32px;
}

.dashboard-header h2 {
    font-size: 28px;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
}

.dashboard-header p {
    color: #666;
    font-size: 14px;
}

.dashboard-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    display: flex;
    align-items: center;
    gap: 20px;
}

.stat-icon {
    font-size: 48px;
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
}

.stat-info h3 {
    font-size: 32px;
    font-weight: 600;
    color: #333;
    margin-bottom: 4px;
}

.stat-info p {
    color: #666;
    font-size: 14px;
}

.dashboard-actions {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.dashboard-actions h3 {
    font-size: 20px;
    font-weight: 600;
    color: #333;
    margin-bottom: 20px;
}

.action-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}

.action-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    padding: 24px;
    background: #f8f9fa;
    border-radius: 8px;
    text-decoration: none;
    color: #333;
    transition: all 0.3s;
}

.action-card:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    transform: translateY(-4px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.action-icon {
    font-size: 32px;
}

.action-text {
    font-size: 14px;
    font-weight: 500;
}
</style>


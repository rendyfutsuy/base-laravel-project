<template>
    <div class="dashboard-layout">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Base Laravel</h2>
                <p class="version">v12.0</p>
            </div>
            
            <nav class="sidebar-nav">
                <router-link to="/" class="nav-item" exact>
                    <span class="nav-icon">📊</span>
                    <span class="nav-text">Dashboard</span>
                </router-link>
                
                <router-link to="/profile" class="nav-item">
                    <span class="nav-icon">👤</span>
                    <span class="nav-text">Profile</span>
                </router-link>
                
                <div class="nav-section">
                    <h3 class="nav-section-title">User Management</h3>
                    <router-link to="/users" class="nav-item">
                        <span class="nav-icon">👥</span>
                        <span class="nav-text">Users</span>
                    </router-link>
                    <router-link to="/staffs" class="nav-item">
                        <span class="nav-icon">👨‍💼</span>
                        <span class="nav-text">Staffs</span>
                    </router-link>
                    <router-link to="/superadmins" class="nav-item">
                        <span class="nav-icon">👑</span>
                        <span class="nav-text">Superadmins</span>
                    </router-link>
                </div>
                
                <div class="nav-section">
                    <h3 class="nav-section-title">Hierarchy</h3>
                    <router-link to="/roles" class="nav-item">
                        <span class="nav-icon">🔐</span>
                        <span class="nav-text">Roles</span>
                    </router-link>
                    <router-link to="/permissions" class="nav-item">
                        <span class="nav-icon">🔑</span>
                        <span class="nav-text">Permissions</span>
                    </router-link>
                </div>
                
                <router-link to="/notifications" class="nav-item">
                    <span class="nav-icon">🔔</span>
                    <span class="nav-text">Notifications</span>
                </router-link>
            </nav>
            
            <div class="sidebar-footer">
                <button @click="handleLogout" class="btn-logout">
                    <span class="nav-icon">🚪</span>
                    <span class="nav-text">Logout</span>
                </button>
            </div>
        </aside>
        
        <main class="main-content">
            <header class="topbar">
                <div class="topbar-left">
                    <h1 class="page-title">{{ pageTitle }}</h1>
                </div>
                <div class="topbar-right">
                    <div class="user-info">
                        <span class="user-name">{{ user?.name || 'User' }}</span>
                        <span class="user-email">{{ user?.email || '' }}</span>
                    </div>
                </div>
            </header>
            
            <div class="content-wrapper">
                <router-view />
            </div>
        </main>
    </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';

export default {
    name: 'DashboardLayout',
    computed: {
        ...mapGetters('auth', ['user']),
        pageTitle() {
            const titles = {
                Dashboard: 'Dashboard',
                Profile: 'Profile',
                Users: 'User Management',
                Staffs: 'Staff Management',
                Superadmins: 'Superadmin Management',
                Roles: 'Role Management',
                Permissions: 'Permission Management',
                Notifications: 'Notifications',
            };
            return titles[this.$route.name] || 'Dashboard';
        },
    },
    methods: {
        ...mapActions('auth', ['logout']),
        
        async handleLogout() {
            await this.logout();
            this.$router.push({ name: 'Login' });
        },
    },
    mounted() {
        this.$store.dispatch('auth/fetchProfile');
    },
};
</script>

<style scoped>
.dashboard-layout {
    display: flex;
    min-height: 100vh;
    background-color: #f5f5f5;
}

.sidebar {
    width: 260px;
    background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    flex-direction: column;
    position: fixed;
    height: 100vh;
    overflow-y: auto;
}

.sidebar-header {
    padding: 24px 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.sidebar-header h2 {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 4px;
}

.sidebar-header .version {
    font-size: 12px;
    opacity: 0.8;
}

.sidebar-nav {
    flex: 1;
    padding: 20px 0;
}

.nav-section {
    margin-top: 20px;
}

.nav-section-title {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 1px;
    padding: 0 20px;
    margin-bottom: 12px;
    opacity: 0.7;
    font-weight: 600;
}

.nav-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 20px;
    color: white;
    text-decoration: none;
    transition: all 0.3s;
    border-left: 3px solid transparent;
}

.nav-item:hover {
    background-color: rgba(255, 255, 255, 0.1);
}

.nav-item.router-link-active {
    background-color: rgba(255, 255, 255, 0.15);
    border-left-color: white;
}

.nav-icon {
    font-size: 18px;
    width: 24px;
    text-align: center;
}

.nav-text {
    font-size: 14px;
    font-weight: 500;
}

.sidebar-footer {
    padding: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.btn-logout {
    display: flex;
    align-items: center;
    gap: 12px;
    width: 100%;
    padding: 12px 20px;
    background: rgba(255, 255, 255, 0.1);
    border: none;
    border-radius: 8px;
    color: white;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-logout:hover {
    background: rgba(255, 255, 255, 0.2);
}

.main-content {
    flex: 1;
    margin-left: 260px;
    display: flex;
    flex-direction: column;
}

.topbar {
    background: white;
    padding: 20px 32px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.page-title {
    font-size: 24px;
    font-weight: 600;
    color: #333;
}

.topbar-right {
    display: flex;
    align-items: center;
    gap: 20px;
}

.user-info {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
}

.user-name {
    font-size: 14px;
    font-weight: 500;
    color: #333;
}

.user-email {
    font-size: 12px;
    color: #666;
}

.content-wrapper {
    flex: 1;
    padding: 32px;
    overflow-y: auto;
}
</style>


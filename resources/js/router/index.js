import Vue from 'vue';
import VueRouter from 'vue-router';
import store from '../store';

Vue.use(VueRouter);

const routes = [
    {
        path: '/login',
        name: 'Login',
        component: () => import('../views/Auth/Login.vue'),
        meta: { requiresAuth: false },
    },
    {
        path: '/register',
        name: 'Register',
        component: () => import('../views/Auth/Register.vue'),
        meta: { requiresAuth: false },
    },
    {
        path: '/',
        component: () => import('../layouts/DashboardLayout.vue'),
        meta: { requiresAuth: true },
        children: [
            {
                path: '',
                name: 'Dashboard',
                component: () => import('../views/Dashboard.vue'),
            },
            {
                path: 'profile',
                name: 'Profile',
                component: () => import('../views/Auth/Profile.vue'),
            },
            {
                path: 'users',
                name: 'Users',
                component: () => import('../views/UserManagement/Users.vue'),
            },
            {
                path: 'staffs',
                name: 'Staffs',
                component: () => import('../views/UserManagement/Staffs.vue'),
            },
            {
                path: 'superadmins',
                name: 'Superadmins',
                component: () => import('../views/UserManagement/Superadmins.vue'),
            },
            {
                path: 'roles',
                name: 'Roles',
                component: () => import('../views/Hierarchy/Roles.vue'),
            },
            {
                path: 'permissions',
                name: 'Permissions',
                component: () => import('../views/Hierarchy/Permissions.vue'),
            },
            {
                path: 'notifications',
                name: 'Notifications',
                component: () => import('../views/Notifications/Index.vue'),
            },
        ],
    },
];

const router = new VueRouter({
    mode: 'history',
    base: process.env.BASE_URL,
    routes,
});

router.beforeEach(async (to, from, next) => {
    // Initialize auth if token exists but not authenticated yet
    const token = localStorage.getItem('access_token');
    let isAuthenticated = store.getters['auth/isAuthenticated'];
    
    if (token && !isAuthenticated) {
        // Initialize auth state
        await store.dispatch('auth/initAuth');
        isAuthenticated = store.getters['auth/isAuthenticated'];
        
        // Try to fetch profile to verify token
        if (isAuthenticated) {
            try {
                await store.dispatch('auth/fetchProfile');
                isAuthenticated = store.getters['auth/isAuthenticated'];
            } catch (error) {
                // Token invalid, clear it
                console.error('Auth error:', error);
                store.dispatch('auth/logout');
                isAuthenticated = false;
            }
        }
    }
    
    // Check authentication requirement
    if (to.meta.requiresAuth && !isAuthenticated) {
        // Redirect to login if not authenticated
        next({ name: 'Login' });
    } else if ((to.name === 'Login' || to.name === 'Register') && isAuthenticated) {
        // Redirect authenticated users away from login/register
        next({ name: 'Dashboard' });
    } else {
        next();
    }
});

export default router;


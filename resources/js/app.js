import './bootstrap';
import Vue from 'vue';
import router from './router';
import store from './store';
import App from './App.vue';

Vue.config.productionTip = false;

// Initialize auth before mounting Vue app
const initApp = async () => {
    await store.dispatch('auth/initAuth');
    
    // If token exists, fetch user profile
    if (store.getters['auth/isAuthenticated']) {
        try {
            await store.dispatch('auth/fetchProfile');
        } catch (error) {
            // Token invalid, clear it
            store.dispatch('auth/logout');
        }
    }
    
    // Mount Vue app after auth initialization
    new Vue({
        router,
        store,
        render: h => h(App),
    }).$mount('#app');
};

initApp();

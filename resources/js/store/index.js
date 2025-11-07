import Vue from 'vue';
import Vuex from 'vuex';
import auth from './modules/auth';
import users from './modules/users';
import roles from './modules/roles';
import permissions from './modules/permissions';
import notifications from './modules/notifications';

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        auth,
        users,
        roles,
        permissions,
        notifications,
    },
});


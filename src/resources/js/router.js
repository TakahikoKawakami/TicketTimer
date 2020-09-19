// import Vue from 'vue';
import VueRouter from 'vue-router';

import Home from '@/components/ticket/TicketViewComponent'
import Login from '@/components/auth/LoginComponent'
import Register from '@/components/auth/RegisterComponent'
import Nav from '@/components/NavComponent'
// import User from '@/components/pages/User'

Vue.use(VueRouter);

const routes = [
    { path: '/', component: Home, meta: { requiresAuth: true } },
    { path: '/ticket/:id', name: "Ticket", component: Home, meta: {requiresAuth: true} },
    { path: '/login', component: Login },
    { path: '/register', component: Register },
    // { path: '/user', component: User, meta: { requiresAuth: true } }
];

const router = new VueRouter({
    mode: 'history',
    routes
});

router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.requiresAuth)) {
        if (state.isLogin === false) {
            next({
                path: '/login',
                query: { redirect: to.fullPath }
            })
        } else {
            next()
        }
    } else {
        next();
    }
});

export default router;

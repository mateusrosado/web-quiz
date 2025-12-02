import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

import Ranking from '../pages/Ranking.vue';
import Login from '../pages/Login.vue';
import Register from '../pages/Register.vue';
import QuizHistory from '../pages/QuizHistory.vue';
import Game from '../pages/Game.vue';
import AdminPanel from '../pages/AdminPanel.vue';
import UserDashboard from '../pages/UserDashboard.vue';

const routes = [
    { path: '/', component: Ranking, name: 'Ranking' },
    { path: '/login', component: Login, name: 'Login' },
    { path: '/register', component: Register, name: 'Register' },
    {
        path: '/quiz/:id',
        component: QuizHistory,
        meta: { requiresAuth: true }
    },
    { 
        path: '/game', 
        component: Game, 
        meta: { requiresAuth: true } 
    },
    { path: '/profile',
        component: UserDashboard,
        meta: { requiresAuth: true }
    },
    { 
        path: '/admin',
        component: AdminPanel,
        meta: { requiresAuth: true, requiresAdmin: true }
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const authStore = useAuthStore();

    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        return next('/login');
    }

    if (to.meta.requiresAdmin && !authStore.isAdmin) {
        return next('/');
    }

    next();
});

export default router;
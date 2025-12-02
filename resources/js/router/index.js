import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const routes = [
    { 
        path: '/', 
        name: 'Ranking',
        component: () => import('../pages/Ranking.vue') 
    },
    { 
        path: '/login', 
        name: 'Login',
        component: () => import('../pages/Login.vue') 
    },
    { 
        path: '/register', 
        name: 'Register',
        component: () => import('../pages/Register.vue') 
    },
    {
        path: '/quiz/:id',
        component: () => import('../pages/QuizHistory.vue'),
        meta: { requiresAuth: true }
    },
    { 
        path: '/game', 
        component: () => import('../pages/Game.vue'),
        meta: { requiresAuth: true, hideNavbar: true } 
    },
    { 
        path: '/profile',
        component: () => import('../pages/UserDashboard.vue'),
        meta: { requiresAuth: true }
    },
    { 
        path: '/admin',
        component: () => import('../pages/AdminPanel.vue'),
        meta: { requiresAuth: true, hideNavbar: true, requiresAdmin: true}
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
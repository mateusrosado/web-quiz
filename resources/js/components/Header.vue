<template>
    <header class="bg-blue-600 text-white shadow-md">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <router-link to="/" class="text-2xl font-bold hover:text-blue-100 transition">
                Web Quiz
            </router-link>

            <nav class="flex items-center gap-4">
                
                <template v-if="!authStore.isAuthenticated">
                    <router-link 
                        to="/login" 
                        class="px-4 py-2 rounded hover:bg-blue-700 transition"
                    >
                        Login
                    </router-link>
                    <router-link 
                        to="/register" 
                        class="px-4 py-2 bg-white text-blue-600 rounded font-semibold hover:bg-blue-50 transition"
                    >
                        Registrar
                    </router-link>
                </template>

                <template v-else>
                    <span class="mr-2">Olá, {{ authStore.user?.name }}</span>
                    
                    <router-link 
                        v-if="authStore.isAdmin"
                        to="/admin" 
                        class="px-3 py-1 bg-yellow-500 rounded hover:bg-yellow-600 text-sm"
                    >
                        Admin
                    </router-link>

                    <button 
                        @click="handleLogout" 
                        class="px-4 py-2 bg-red-500 rounded hover:bg-red-600 transition"
                    >
                        Sair
                    </button>
                </template>

            </nav>
        </div>
    </header>
</template>

<script setup>
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();

const handleLogout = async () => {
    await authStore.logout();
    router.push('/login');
};
</script>
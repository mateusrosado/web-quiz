<template>
    <div class="max-w-md mx-auto mt-10 bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-center">Entrar</h2>
        
        <form @submit.prevent="handleLogin">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">E-mail</label>
                <input 
                    v-model="form.email"
                    type="email" 
                    required
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Senha</label>
                <input 
                    v-model="form.password"
                    type="password" 
                    required
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <p v-if="error" class="text-red-500 text-sm mb-4">{{ error }}</p>

            <button 
                type="submit" 
                class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition disabled:opacity-50"
                :disabled="loading"
            >
                {{ loading ? 'Entrando...' : 'Entrar' }}
            </button>
        </form>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();

const form = reactive({
    email: '',
    password: ''
});

const error = ref('');
const loading = ref(false);

const handleLogin = async () => {
    loading.value = true;
    error.value = '';

    try {
        await authStore.login(form.email, form.password);
        
        router.push('/');
    } catch (e) {
        if (e.response && e.response.status === 422) {
            error.value = 'Dados inválidos. Verifique seu e-mail e senha.';
        } else {
            error.value = 'Ocorreu um erro ao fazer login.';
        }
    } finally {
        loading.value = false;
    }
};
</script>
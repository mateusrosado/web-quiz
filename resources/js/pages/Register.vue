<template>
    <div class="p-10 max-w-md mx-auto bg-white rounded-lg shadow-md mt-10">
        <h1 class="text-2xl mb-6 font-bold text-center">Criar Conta</h1>
        
        <form @submit.prevent="handleRegister" class="flex flex-col gap-4">
            <div>
                <label class="block text-sm font-bold mb-1">Nome</label>
                <input v-model="form.name" type="text" class="w-full border p-2 rounded" required />
            </div>

            <div>
                <label class="block text-sm font-bold mb-1">E-mail</label>
                <input v-model="form.email" type="email" class="w-full border p-2 rounded" required />
            </div>

            <div>
                <label class="block text-sm font-bold mb-1">Senha</label>
                <input v-model="form.password" type="password" class="w-full border p-2 rounded" required />
            </div>

            <div>
                <label class="block text-sm font-bold mb-1">Confirmar Senha</label>
                <input v-model="form.password_confirmation" type="password" class="w-full border p-2 rounded" required />
            </div>
            
            <button 
                type="submit" 
                class="bg-green-600 text-white p-2 rounded hover:bg-green-700 transition font-bold disabled:opacity-50"
                :disabled="loading"
            >
                {{ loading ? 'Cadastrando...' : 'Cadastrar' }}
            </button>

            <p v-if="errors" class="text-red-500 text-sm mt-2 text-center">{{ errors }}</p>
        </form>
    </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const form = reactive({ 
    name: '', 
    email: '', 
    password: '', 
    password_confirmation: '' 
});

const errors = ref('');
const loading = ref(false);

const handleRegister = async () => {
    loading.value = true;
    errors.value = '';

    try {
        await authStore.register(form);
        
        router.push('/');
    } catch (error) {
        if (error.response?.data?.errors) {
            errors.value = Object.values(error.response.data.errors).flat()[0];
        } else {
            errors.value = error.response?.data?.message || 'Erro ao cadastrar. Tente novamente.';
        }
    } finally {
        loading.value = false;
    }
};
</script>
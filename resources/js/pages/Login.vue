<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4 py-10">
        <div class="max-w-md w-full bg-white p-8 rounded-3xl shadow-xl relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-[#094789] to-[#FBC209]"></div>

            <div class="text-center mb-8">
                <h1 class="text-3xl font-black text-[#094789] mb-2">Bem-vindo de volta!</h1>
                <p class="text-gray-500 text-sm">Insira suas credenciais para acessar o game.</p>
            </div>
            
            <form @submit.prevent="handleLogin" class="space-y-6">
                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 ml-1">E-mail</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#094789] transition-colors">
                            <Mail :size="20" />
                        </div>
                        <input 
                            v-model="form.email"
                            type="email" 
                            required
                            placeholder="seu@email.com"
                            class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-[#094789] focus:ring-4 focus:ring-[#094789]/10 outline-none transition-all font-medium text-[#094789]"
                        >
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 ml-1">Senha</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#094789] transition-colors">
                            <Lock :size="20" />
                        </div>
                        <input 
                            v-model="form.password"
                            type="password" 
                            required
                            placeholder="••••••••"
                            class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-[#094789] focus:ring-4 focus:ring-[#094789]/10 outline-none transition-all font-medium text-[#094789]"
                        >
                    </div>
                </div>

                <div v-if="error" class="p-3 rounded-lg bg-red-50 border border-red-100 flex items-center gap-3 text-red-600 text-sm font-medium">
                    <AlertCircle :size="18" class="shrink-0" />
                    {{ error }}
                </div>

                <button 
                    type="submit" 
                    class="w-full bg-[#094789] text-white py-4 rounded-xl font-bold hover:bg-[#073666] transition-all transform hover:-translate-y-1 hover:shadow-lg disabled:opacity-50 disabled:hover:translate-y-0 disabled:hover:shadow-none flex items-center justify-center gap-2"
                    :disabled="loading"
                >
                    <span v-if="loading">Entrando...</span>
                    <span v-else>Entrar no Jogo</span>
                    <ArrowRight v-if="!loading" :size="20" />
                </button>
            </form>

            <div class="mt-8 text-center pt-6 border-t border-gray-100">
                <p class="text-gray-600 text-sm">
                    Não tem uma conta? 
                    <router-link to="/register" class="font-bold text-[#094789] hover:text-[#FBC209] transition-colors">
                        Cadastre-se aqui
                    </router-link>
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';
import { Mail, Lock, ArrowRight, AlertCircle } from 'lucide-vue-next';

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
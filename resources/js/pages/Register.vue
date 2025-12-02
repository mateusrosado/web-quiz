<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4 py-10">
        <div class="max-w-md w-full bg-white p-8 rounded-3xl shadow-xl relative overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-[#FBC209]/10 rounded-bl-full -mr-4 -mt-4 pointer-events-none"></div>
            
            <div class="text-center mb-8 relative">
                <h1 class="text-3xl font-black text-[#094789] mb-2">Crie sua Conta</h1>
                <p class="text-gray-500 text-sm">Entre para o ranking dos mestres do quiz.</p>
            </div>
            
            <form @submit.prevent="handleRegister" class="space-y-5">
                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 ml-1">Nome Completo</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#094789] transition-colors">
                            <User :size="20" />
                        </div>
                        <input 
                            v-model="form.name" 
                            type="text" 
                            required 
                            placeholder="Seu nome de jogador"
                            class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-[#094789] focus:ring-4 focus:ring-[#094789]/10 outline-none transition-all font-medium text-[#094789]"
                        />
                    </div>
                </div>

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
                            placeholder="seu@melhoremail.com"
                            class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-[#094789] focus:ring-4 focus:ring-[#094789]/10 outline-none transition-all font-medium text-[#094789]"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                                placeholder="******"
                                class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-[#094789] focus:ring-4 focus:ring-[#094789]/10 outline-none transition-all font-medium text-[#094789]"
                            />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 ml-1">Confirmar</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#094789] transition-colors">
                                <CheckCircle :size="20" />
                            </div>
                            <input 
                                v-model="form.password_confirmation" 
                                type="password" 
                                required 
                                placeholder="******"
                                class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-[#094789] focus:ring-4 focus:ring-[#094789]/10 outline-none transition-all font-medium text-[#094789]"
                            />
                        </div>
                    </div>
                </div>
                
                <div v-if="errors" class="p-3 rounded-lg bg-red-50 border border-red-100 flex items-center gap-3 text-red-600 text-sm font-medium">
                    <AlertCircle :size="18" class="shrink-0" />
                    {{ errors }}
                </div>

                <button 
                    type="submit" 
                    class="w-full bg-[#094789] text-white py-4 rounded-xl font-bold hover:bg-[#073666] transition-all transform hover:-translate-y-1 hover:shadow-lg disabled:opacity-50 disabled:hover:translate-y-0 disabled:hover:shadow-none flex items-center justify-center gap-2 group"
                    :disabled="loading"
                >
                    <span>{{ loading ? 'Cadastrando...' : 'Criar Conta' }}</span>
                    <Sparkles v-if="!loading" :size="20" class="text-[#FBC209]" />
                </button>
            </form>

            <div class="mt-8 text-center pt-6 border-t border-gray-100">
                <p class="text-gray-600 text-sm">
                    Já tem uma conta? 
                    <router-link to="/login" class="font-bold text-[#094789] hover:text-[#FBC209] transition-colors">
                        Faça Login
                    </router-link>
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import { User, Mail, Lock, CheckCircle, Sparkles, AlertCircle } from 'lucide-vue-next';

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
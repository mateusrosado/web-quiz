<script setup>
import { ref, computed } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useRouter, useRoute } from 'vue-router';
import { Menu, X, LogOut, User, LayoutDashboard, Gamepad2 } from 'lucide-vue-next';

const authStore = useAuthStore();
const router = useRouter();
const route = useRoute();

const mobileMenuOpen = ref(false);
const userDropdownOpen = ref(false);

const handleLogout = async () => {
    mobileMenuOpen.value = false;
    userDropdownOpen.value = false;
    await authStore.logout();
    // A remoção do router.push('/login') aqui é feita no finally do auth.js
};

// Navegação para Perfil
const goToProfile = () => {
    userDropdownOpen.value = false;
    router.push('/profile');
};

// Funções utilitárias (mantidas iguais)
const isActive = (path) => route.path === path;
const mobileLinkClass = (path) => {
    const base = "block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition duration-150 ease-in-out";
    return isActive(path)
        ? `${base} border-[#FBC209] text-[#094789] bg-indigo-50`
        : `${base} border-transparent text-slate-600 hover:text-[#094789] hover:bg-gray-50 hover:border-gray-300`;
};
</script>

<template>
    <nav class="bg-white border-b border-gray-100 fixed top-0 w-full z-50 shadow-lg">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16">

            <div class="flex justify-between items-center h-full relative">
                
                <div class="flex-shrink-0 flex items-center h-full">
                    <router-link to="/">
                        <div class="flex items-center gap-2">
                            <div class="bg-[#094789] text-white p-1.5 rounded-lg">
                                <Gamepad2 :size="24" />
                            </div>
                            <span class="font-bold text-xl text-[#094789] tracking-tight">Web Quiz</span>
                        </div>
                    </router-link>
                </div>

                <div class="absolute left-1/2 transform -translate-x-1/2 h-full hidden sm:flex items-center">
                    <router-link v-if="authStore.isAuthenticated" to="/game" 
                        class="inline-flex items-center px-6 py-2 bg-[#FBC209] border border-transparent rounded-full font-semibold text-sm text-[#094789] uppercase tracking-widest hover:bg-yellow-400 transition shadow-md shadow-yellow-500/30">
                        <Gamepad2 :size="18" class="mr-2" />
                        JOGAR AGORA
                    </router-link>
                </div>
                
                <div class="flex items-center space-x-4 h-full">
                    
                    <template v-if="authStore.isAuthenticated">
                        <router-link v-if="authStore.isAdmin" to="/admin" class="hidden md:flex text-sm font-medium text-slate-500 hover:text-[#094789] items-center gap-1 transition">
                            <LayoutDashboard :size="16" /> Admin
                        </router-link>

                        <div class="relative ml-3" tabindex="0" @focusout="setTimeout(() => { userDropdownOpen = false }, 150)"> 
                            <button @click="userDropdownOpen = !userDropdownOpen" class="flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-[#094789] transition focus:outline-none">
                                <span>{{ authStore.user?.name }}</span>
                                <div class="h-8 w-8 rounded-full bg-[#094789]/10 flex items-center justify-center text-[#094789]">
                                    {{ authStore.user?.name?.charAt(0).toUpperCase() }}
                                </div>
                            </button>
                            
                            <div :class="{ 'block': userDropdownOpen, 'hidden': !userDropdownOpen }" 
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 border border-gray-100">
                                
                                <button @click="goToProfile" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    Meu Perfil
                                </button>
                                <button @click="handleLogout" class="block w-full text-left px-4 py-2 text-sm text-rose-600 hover:bg-gray-50">
                                    <div class="flex items-center gap-2"><LogOut :size="16"/> Sair</div>
                                </button>
                            </div>
                        </div>
                    </template>

                    <template v-else>
                        <router-link to="/login" class="text-sm font-medium text-slate-500 hover:text-[#094789] transition">Entrar</router-link>
                        <router-link to="/register" class="inline-flex items-center px-4 py-2 bg-[#094789] border border-transparent rounded-full font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#0a5aa7] transition shadow-md shadow-blue-900/20">
                            Criar Conta
                        </router-link>
                    </template>
                </div>

                <div class="-mr-2 flex items-center sm:hidden h-full"> 
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-slate-500 hover:bg-gray-100 focus:outline-none transition">
                        <Menu v-if="!mobileMenuOpen" :size="24" />
                        <X v-else :size="24" />
                    </button>
                </div>
            </div>
        </div>

        <div v-show="mobileMenuOpen" class="absolute w-full top-16 left-0 sm:hidden border-t border-gray-200 bg-white shadow-xl">
            <div class="pt-2 pb-3 space-y-1">
                <router-link v-if="authStore.isAuthenticated" to="/game" 
                    class="block w-full text-center px-4 py-3 bg-[#FBC209] text-[#094789] font-semibold rounded-lg" 
                    @click="mobileMenuOpen = false">
                    <div class="flex items-center justify-center gap-2 text-base">
                        <Gamepad2 :size="18"/> JOGAR AGORA
                    </div>
                </router-link>

                <router-link v-if="authStore.isAdmin" to="/admin" :class="mobileLinkClass('/admin')" @click="mobileMenuOpen = false">Painel Admin</router-link>
                <router-link to="/" :class="mobileLinkClass('/')" @click="mobileMenuOpen = false">Ranking</router-link>
            </div>
            
            <div class="pt-4 pb-4 border-t border-gray-200">
                <template v-if="authStore.isAuthenticated">
                    <div class="flex items-center px-4 mb-4">
                        <div class="shrink-0">
                            <div class="h-10 w-10 rounded-full bg-[#094789]/10 flex items-center justify-center text-[#094789] font-bold text-lg">
                                {{ authStore.user?.name?.charAt(0).toUpperCase() }}
                            </div>
                        </div>
                        <div class="ml-3">
                            <div class="text-base font-medium text-slate-800">{{ authStore.user?.name }}</div>
                            <div class="text-sm font-medium text-slate-500">{{ authStore.user?.email }}</div>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <button @click="goToProfile" class="block w-full text-left px-4 py-2 text-base font-medium text-slate-600 hover:text-[#094789] hover:bg-gray-50">
                            <div class="flex items-center gap-2"><User :size="18"/> Meu Perfil</div>
                        </button>
                        <button @click="handleLogout" class="block w-full text-left px-4 py-2 text-base font-medium text-rose-600 hover:bg-rose-50">
                            <div class="flex items-center gap-2"><LogOut :size="18"/> Sair da Conta</div>
                        </button>
                    </div>
                </template>

                <template v-else>
                    <div class="space-y-1 px-4">
                        <router-link to="/login" class="block w-full text-center px-4 py-3 bg-gray-50 text-slate-700 font-semibold rounded-lg mb-2" @click="mobileMenuOpen = false">
                            Fazer Login
                        </router-link>
                        <router-link to="/register" class="block w-full text-center px-4 py-3 bg-[#094789] text-white font-semibold rounded-lg" @click="mobileMenuOpen = false">
                            Criar Conta Grátis
                        </router-link>
                    </div>
                </template>
            </div>
        </div>
    </nav>
    
    <div class="h-16 lg:h-16"></div> 
</template>
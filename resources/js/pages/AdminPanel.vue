<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { 
    LayoutDashboard, 
    Users, 
    FileQuestion, 
    Gamepad2, 
    Settings, 
    LogOut, 
    ArrowLeft 
} from 'lucide-vue-next';

import AdminDashboard from '../components/admin/AdminDashboard.vue';
import AdminUsers from '../components/admin/AdminUsers.vue';
import AdminQuizzes from '../components/admin/AdminQuizzes.vue';
import AdminQuestions from '../components/admin/AdminQuestions.vue';
import AdminSettings from '../components/admin/AdminSettings.vue';

const router = useRouter();
const currentView = ref('insights');

const activeComponent = computed(() => {
    switch (currentView.value) {
        case 'users': return AdminUsers;
        case 'quizzes': return AdminQuizzes;
        case 'questions': return AdminQuestions;
        case 'settings': return AdminSettings;
        default: return AdminDashboard;
    }
});

const menuItems = [
    { id: 'insights', label: 'Insights', icon: LayoutDashboard, section: 'Geral' },
    { id: 'users', label: 'Usuários', icon: Users, section: 'Geral' },
    { id: 'questions', label: 'Perguntas', icon: FileQuestion, section: 'Conteúdo' },
    { id: 'quizzes', label: 'Histórico', icon: Gamepad2, section: 'Conteúdo' },
    { id: 'settings', label: 'Configurações', icon: Settings, section: 'Sistema' },
];

const navClass = (viewName) => {
    return currentView.value === viewName 
        ? 'bg-white text-[#094789] shadow-lg shadow-black/10' 
        : 'hover:bg-white/10 text-white';
};

const iconClass = (viewName) => {
    return currentView.value === viewName
        ? 'bg-[#FBC209] text-[#094789]'
        : 'bg-white/10 text-white group-hover:bg-[#FBC209] group-hover:text-[#094789]';
};
</script>

<template>
    <div class="min-h-screen bg-gray-100 font-sans flex flex-col lg:flex-row">
        <aside class="lg:w-80 xl:w-[22rem] bg-gradient-to-b from-[#0a4b90] via-[#094789] to-[#083866] text-white flex-shrink-0 lg:h-screen lg:sticky lg:top-0 lg:overflow-y-auto shadow-2xl z-20">
            <div class="px-6 pt-10 pb-12 space-y-8 flex flex-col h-full">
                <div class="bg-white/10 border border-white/15 rounded-3xl px-5 py-6 flex items-center gap-4">
                    <div class="flex-shrink-0 w-14 h-14 rounded-full bg-white/20 flex items-center justify-center text-lg font-bold text-[#FBC209]">
                        ADM
                    </div>
                    <div class="text-white min-w-0">
                        <p class="text-sm text-white/70">Painel Admin</p>
                        <p class="text-lg font-semibold leading-tight truncate">Web Quiz</p>
                    </div>
                </div>

                <nav class="flex-1 space-y-6">
                    <div v-for="(section, sectionName) in ['Geral', 'Conteúdo', 'Sistema']" :key="sectionName" class="space-y-2">
                        <p class="text-xs uppercase tracking-[0.3em] text-white/50 font-bold px-2">{{ section }}</p>
                        
                        <template v-for="item in menuItems" :key="item.id">
                            <a v-if="item.section === section" 
                               @click="currentView = item.id" 
                               :class="navClass(item.id)" 
                               class="group flex items-center gap-3 px-4 py-3 rounded-2xl cursor-pointer transition-all duration-200">
                                <span :class="['flex items-center justify-center w-9 h-9 rounded-xl transition-colors', iconClass(item.id)]">
                                    <component :is="item.icon" :size="20" stroke-width="2" />
                                </span>
                                <span class="text-sm font-medium tracking-wide">{{ item.label }}</span>
                            </a>
                        </template>
                    </div>

                    <div class="space-y-2 pt-4">
                        <button @click="router.push('/')" class="w-full group flex items-center gap-3 px-4 py-3 rounded-2xl hover:bg-white/10 transition-all duration-200 text-white/80 hover:text-white">
                            <span class="flex items-center justify-center w-9 h-9 rounded-xl bg-white/5 text-white group-hover:bg-[#FBC209] group-hover:text-[#094789]">
                                <ArrowLeft :size="20" />
                            </span>
                            <span class="text-sm font-medium tracking-wide">Voltar ao Site</span>
                        </button>
                    </div>
                </nav>
            </div>
        </aside>

        <main class="flex-1 px-4 sm:px-6 lg:px-10 xl:px-14 pt-8 pb-20 overflow-y-auto">
            <transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 translate-y-2" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 translate-y-2" mode="out-in">
                <component :is="activeComponent" />
            </transition>
        </main>
    </div>
</template>
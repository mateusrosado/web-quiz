<script setup>
import { onMounted } from 'vue';
import axios from 'axios';
import { usePagination } from '../../composables/usePagination';

// CORREÇÃO: URL sem '/api' no início
const { 
    data: userList, 
    pagination: usersPagination, 
    loadData: loadUsers 
} = usePagination('/admin/users');

onMounted(() => loadUsers());

async function toggleAdmin(user) {
    if(!confirm(`Alterar permissão de ${user.name}?`)) return;
    try {
        await axios.post(`/admin/users/${user.id}/toggle-admin`); // Sem /api
        loadUsers();
    } catch(e) { alert(e.response?.data?.message || 'Erro'); }
}

async function deleteUser(id) {
    if(!confirm('Tem certeza? Isso apagará todo histórico deste usuário.')) return;
    try {
        await axios.delete(`/admin/users/${id}`); // Sem /api
        loadUsers();
    } catch(e) { alert(e.response?.data?.message || 'Erro'); }
}
</script>

<template>
    <div class="space-y-6">
        <header>
            <p class="text-xs uppercase tracking-[0.3em] text-[#FBC209] font-bold">Comunidade</p>
            <h1 class="text-2xl font-semibold text-[#094789] mt-1">Gerenciar Usuários</h1>
        </header>

        <div class="bg-white rounded-3xl border border-[#094789]/10 shadow-sm overflow-hidden">
            <div v-if="userList.length === 0" class="text-center py-12 text-slate-500">
                Nenhum usuário encontrado.
            </div>
            
            <div v-else class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-[#094789] uppercase tracking-wider">Usuário</th>
                            <th class="px-6 py-4 text-xs font-bold text-[#094789] uppercase tracking-wider">Email</th>
                            <th class="px-6 py-4 text-xs font-bold text-[#094789] uppercase tracking-wider text-center">Função</th>
                            <th class="px-6 py-4 text-xs font-bold text-[#094789] uppercase tracking-wider text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="user in userList" :key="user.id" class="hover:bg-[#094789]/5 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-full bg-[#094789]/10 flex items-center justify-center text-[#094789] font-bold text-sm">
                                        {{ user.name?.charAt(0).toUpperCase() || '?' }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-700">{{ user.name || 'Usuário' }}</p>
                                        <p class="text-xs text-slate-400">ID: #{{ user.id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ user.email }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide"
                                    :class="user.is_admin ? 'bg-purple-100 text-purple-700' : 'bg-slate-100 text-slate-600'">
                                    {{ user.is_admin ? 'Admin' : 'Usuário' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button @click="toggleAdmin(user)" class="text-xs font-bold uppercase tracking-wide text-[#094789] hover:underline">
                                    {{ user.is_admin ? 'Rebaixar' : 'Promover' }}
                                </button>
                                <button @click="deleteUser(user.id)" class="text-xs font-bold uppercase tracking-wide text-rose-600 hover:underline">
                                    Excluir
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="usersPagination && usersPagination.last_page > 1" class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-center gap-2">
                <button @click="loadUsers(usersPagination.prev_page_url)" :disabled="!usersPagination.prev_page_url" 
                    class="px-4 py-2 rounded-full border border-[#094789]/20 text-[#094789] text-sm font-medium hover:bg-[#094789]/5 disabled:opacity-50">
                    Anterior
                </button>
                <span class="text-sm text-slate-500 font-medium px-2">
                    {{ usersPagination.current_page }} / {{ usersPagination.last_page }}
                </span>
                <button @click="loadUsers(usersPagination.next_page_url)" :disabled="!usersPagination.next_page_url"
                    class="px-4 py-2 rounded-full border border-[#094789]/20 text-[#094789] text-sm font-medium hover:bg-[#094789]/5 disabled:opacity-50">
                    Próxima
                </button>
            </div>
        </div>
    </div>
</template>
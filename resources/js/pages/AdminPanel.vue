<template>
    <div class="flex min-h-screen bg-gray-100 font-sans">
        <aside class="w-64 bg-[#1E1E4B] text-white flex-shrink-0 flex flex-col">
            <div class="p-6 font-bold text-2xl tracking-wide border-b border-blue-900/30">Admin Painel</div>
            <nav class="mt-6 flex-1 px-2 space-y-1">
                <a @click="currentView = 'insights'" :class="navClass('insights')" class="group flex items-center px-4 py-3 text-sm font-medium rounded-md cursor-pointer transition-colors">
                    <span class="mr-3 text-lg">📊</span> Insights
                </a>
                <a @click="currentView = 'users'" :class="navClass('users')" class="group flex items-center px-4 py-3 text-sm font-medium rounded-md cursor-pointer transition-colors">
                    <span class="mr-3 text-lg">👥</span> Usuários
                </a>
                <a @click="currentView = 'quizzes'" :class="navClass('quizzes')" class="group flex items-center px-4 py-3 text-sm font-medium rounded-md cursor-pointer transition-colors">
                    <span class="mr-3 text-lg">🎮</span> Histórico Geral
                </a>
                <a @click="currentView = 'questions'" :class="navClass('questions')" class="group flex items-center px-4 py-3 text-sm font-medium rounded-md cursor-pointer transition-colors">
                    <span class="mr-3 text-lg">❓</span> Perguntas
                </a>
                <a @click="currentView = 'settings'" :class="navClass('settings')" class="group flex items-center px-4 py-3 text-sm font-medium rounded-md cursor-pointer transition-colors">
                    <span class="mr-3 text-lg">⚙️</span> Configurações
                </a>
            </nav>
            <div class="p-4 border-t border-blue-900/30">
                <button @click="$router.push('/')" class="flex items-center w-full px-4 py-2 text-sm text-blue-200 hover:text-white transition-colors">
                    <span class="mr-2">⬅</span> Voltar ao Site
                </button>
            </div>
        </aside>

        <main class="flex-1 p-8 overflow-y-auto">
            
            <div v-if="currentView === 'insights'">
                <h2 class="text-3xl font-bold mb-8 text-gray-800">Visão Geral</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6" v-if="insights">
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                        <div class="text-sm font-medium text-gray-500 uppercase mb-1">Usuários Totais</div>
                        <div class="text-3xl font-bold text-blue-600">{{ insights.total_users }}</div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                        <div class="text-sm font-medium text-gray-500 uppercase mb-1">Quizzes Realizados</div>
                        <div class="text-3xl font-bold text-green-600">{{ insights.total_quizzes }}</div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                        <div class="text-sm font-medium text-gray-500 uppercase mb-1">Perguntas no Banco</div>
                        <div class="text-3xl font-bold text-yellow-600">{{ insights.total_questions }}</div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                        <div class="text-sm font-medium text-gray-500 uppercase mb-1">Combinações</div>
                        <div class="text-lg font-bold text-purple-600 truncate" :title="insights.possible_combinations">{{ insights.possible_combinations }}</div>
                    </div>
                </div>
            </div>

            <div v-if="currentView === 'users'">
                <h2 class="text-3xl font-bold mb-8 text-gray-800">Gerenciar Usuários</h2>
                
                <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
                    <div v-if="userList.length === 0" class="p-8 text-center text-gray-500">
                        Nenhum usuário encontrado.
                    </div>
                    
                    <table v-else class="w-full">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-medium">
                            <tr>
                                <th class="px-6 py-3 text-left tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left tracking-wider">Nome</th>
                                <th class="px-6 py-3 text-left tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left tracking-wider">Função</th>
                                <th class="px-6 py-3 text-right tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="user in userList" :key="user.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">#{{ user.id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ user.name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span :class="user.is_admin ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                        {{ user.is_admin ? 'ADMIN' : 'USER' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                                    <button @click="toggleAdmin(user)" class="text-blue-600 hover:text-blue-900 transition-colors">
                                        {{ user.is_admin ? 'Revogar Adm' : 'Tornar Adm' }}
                                    </button>
                                    <button @click="deleteUser(user.id)" class="text-red-600 hover:text-red-900 transition-colors">Excluir</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div v-if="usersPagination && usersPagination.last_page > 1" class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
                        <button 
                            @click="loadUsers(usersPagination.prev_page_url)" 
                            :disabled="!usersPagination.prev_page_url"
                            class="px-3 py-1 border rounded text-sm disabled:opacity-50 bg-white hover:bg-gray-100 transition-colors">
                            Anterior
                        </button>
                        <span class="text-sm text-gray-600">Página {{ usersPagination.current_page }} de {{ usersPagination.last_page }}</span>
                        <button 
                            @click="loadUsers(usersPagination.next_page_url)" 
                            :disabled="!usersPagination.next_page_url"
                            class="px-3 py-1 border rounded text-sm disabled:opacity-50 bg-white hover:bg-gray-100 transition-colors">
                            Próxima
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="currentView === 'questions'">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-800">Banco de Perguntas</h2>
                    <div class="space-x-3">
                        <button v-if="!showQuestionForm" @click="openNewQuestionForm" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 shadow-sm transition-colors flex items-center gap-2">
                            <span>+</span> Nova Pergunta
                        </button>
                        <div class="inline-flex rounded-md shadow-sm" role="group">
                            <label class="bg-white text-gray-700 px-4 py-2 border border-gray-300 rounded-l-lg hover:bg-gray-50 cursor-pointer transition-colors text-sm font-medium">
                                Importar CSV
                                <input type="file" class="hidden" @change="handleFileUpload" accept=".csv">
                            </label>
                            <button @click="downloadCSV" class="bg-white text-gray-700 px-4 py-2 border-t border-b border-r border-gray-300 rounded-r-lg hover:bg-gray-50 transition-colors text-sm font-medium">
                                Exportar CSV
                            </button>
                        </div>
                    </div>
                </div>

                <div v-if="showQuestionForm" class="mb-8">
                    <QuestionForm 
                        :questionData="editingQuestion"
                        @save="handleSaveQuestion"
                        @cancel="closeQuestionForm"
                    />
                </div>

                <div v-else class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
                    <div v-if="questionsList.length === 0" class="p-8 text-center text-gray-500">
                        Nenhuma pergunta cadastrada. Adicione uma nova ou importe um CSV.
                    </div>
                    <ul v-else class="divide-y divide-gray-200">
                        <li v-for="question in questionsList" :key="question.id" class="p-6 hover:bg-gray-50 transition-colors">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <p class="text-lg font-medium text-gray-900 mb-2">{{ question.description }}</p>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
                                        <div v-for="option in question.options" :key="option.id" 
                                            class="text-sm px-3 py-1.5 rounded border"
                                            :class="option.is_correct ? 'bg-green-50 border-green-200 text-green-800' : 'bg-gray-50 border-gray-200 text-gray-600'">
                                            <span v-if="option.is_correct" class="font-bold mr-1">✓</span>
                                            {{ option.text }}
                                        </div>
                                    </div>
                                </div>
                                <div class="ml-4 flex-shrink-0 flex space-x-3">
                                    <button @click="editQuestion(question)" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 p-2 rounded transition-colors" title="Editar">
                                        ✏️
                                    </button>
                                    <button @click="deleteQuestion(question.id)" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-2 rounded transition-colors" title="Excluir">
                                        🗑️
                                    </button>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <div v-if="questionsPagination && questionsPagination.last_page > 1" class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
                        <button 
                            @click="loadQuestions(questionsPagination.prev_page_url)" 
                            :disabled="!questionsPagination.prev_page_url"
                            class="px-3 py-1 border rounded text-sm disabled:opacity-50 bg-white hover:bg-gray-100 transition-colors">
                            Anterior
                        </button>
                        <span class="text-sm text-gray-600">Página {{ questionsPagination.current_page }} de {{ questionsPagination.last_page }}</span>
                        <button 
                            @click="loadQuestions(questionsPagination.next_page_url)" 
                            :disabled="!questionsPagination.next_page_url"
                            class="px-3 py-1 border rounded text-sm disabled:opacity-50 bg-white hover:bg-gray-100 transition-colors">
                            Próxima
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="currentView === 'settings'">
                <h2 class="text-3xl font-bold mb-8 text-gray-800">Configurações do Jogo</h2>
                <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200 max-w-2xl">
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantidade de Perguntas por Quiz</label>
                        <input v-model="settingsForm.quiz_question_limit" type="number" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-3 border">
                        <p class="mt-1 text-sm text-gray-500">Define quantas perguntas aparecerão em cada partida.</p>
                    </div>
                    <div class="mb-8">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pontos por Acerto</label>
                        <input v-model="settingsForm.score_per_question" type="number" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-3 border">
                        <p class="mt-1 text-sm text-gray-500">Valor de XP concedido a cada resposta correta.</p>
                    </div>
                    <div class="flex justify-end">
                        <button @click="saveSettings" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 font-bold shadow-sm transition-colors w-full sm:w-auto">
                            Salvar Configurações
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="currentView === 'quizzes'">
                <h2 class="text-3xl font-bold mb-8 text-gray-800">Histórico de Partidas</h2>
                <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-medium">
                            <tr>
                                <th class="px-6 py-3 text-left">Data</th>
                                <th class="px-6 py-3 text-left">Usuário</th>
                                <th class="px-6 py-3 text-center">Pontuação</th>
                                <th class="px-6 py-3 text-center">Acertos/Erros</th>
                                <th class="px-6 py-3 text-center">Tempo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="quiz in quizzesHistory" :key="quiz.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ new Date(quiz.created_at).toLocaleString() }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ quiz.user?.name || 'Anônimo' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-blue-600">{{ quiz.score }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                    <span class="text-green-600 font-bold">{{ quiz.correct_count }}</span> / <span class="text-red-600 font-bold">{{ quiz.wrong_count }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">{{ quiz.duration }}s</td>
                            </tr>
                        </tbody>
                    </table>
                    <div v-if="quizzesPagination && quizzesPagination.last_page > 1" class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
                        <button 
                            @click="loadQuizzesHistory(quizzesPagination.prev_page_url)" 
                            :disabled="!quizzesPagination.prev_page_url"
                            class="px-3 py-1 border rounded text-sm disabled:opacity-50 bg-white hover:bg-gray-100 transition-colors">
                            Anterior
                        </button>
                        <span class="text-sm text-gray-600">Página {{ quizzesPagination.current_page }} de {{ quizzesPagination.last_page }}</span>
                        <button 
                            @click="loadQuizzesHistory(quizzesPagination.next_page_url)" 
                            :disabled="!quizzesPagination.next_page_url"
                            class="px-3 py-1 border rounded text-sm disabled:opacity-50 bg-white hover:bg-gray-100 transition-colors">
                            Próxima
                        </button>
                    </div>
                </div>
            </div>

        </main>
    </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import axios from 'axios';
import QuestionForm from '../components/admin/QuestionForm.vue';

const currentView = ref('insights');
const insights = ref(null);
const settingsForm = reactive({ quiz_question_limit: 10, score_per_question: 10 });

const userList = ref([]);
const usersPagination = ref(null); // Initialize as null

const questionsList = ref([]);
const questionsPagination = ref(null); // Initialize as null
const showQuestionForm = ref(false);
const editingQuestion = ref(null);

const quizzesHistory = ref([]);
const quizzesPagination = ref(null); // Initialize as null

const navClass = (viewName) => {
    return currentView.value === viewName 
        ? 'bg-blue-800 text-white' 
        : 'text-blue-100 hover:bg-blue-800 hover:text-white';
};

onMounted(() => {
    loadInsights();
    loadUsers();
    loadQuestions();
    loadQuizzesHistory();
});

async function loadInsights() {
    try {
        const res = await axios.get('/admin/insights');
        insights.value = res.data;
        settingsForm.quiz_question_limit = res.data.quiz_limit;
        settingsForm.score_per_question = res.data.score_per_question;
    } catch (e) {
        console.error("Erro ao carregar insights", e);
    }
}

async function loadUsers(url = '/admin/users') {
    try {
        const res = await axios.get(url);
        if (res.data.data) {
            userList.value = res.data.data;
            usersPagination.value = res.data;
        } else {
            userList.value = res.data;
            usersPagination.value = null; // Reset pagination if not supported
        }
    } catch (e) {
        console.error("Erro ao carregar usuários", e);
    }
}

async function toggleAdmin(user) {
    if(!confirm(`Alterar permissão de ${user.name}?`)) return;
    try {
        await axios.post(`/admin/users/${user.id}/toggle-admin`);
        loadUsers();
    } catch(e) { alert(e.response?.data?.message || 'Erro'); }
}

async function deleteUser(id) {
    if(!confirm('Tem certeza? Isso apagará todo histórico deste usuário.')) return;
    try {
        await axios.delete(`/admin/users/${id}`);
        loadUsers();
        loadInsights();
    } catch(e) { alert(e.response?.data?.message || 'Erro'); }
}

async function saveSettings() {
    try {
        await axios.post('/admin/settings', settingsForm);
        alert('Configurações salvas!');
        loadInsights();
    } catch (e) {
        alert('Erro ao salvar configurações.');
    }
}

async function loadQuestions(url = '/admin/questions') {
    try {
        const res = await axios.get(url);
        if (res.data.data) {
            questionsList.value = res.data.data;
            questionsPagination.value = res.data;
        } else {
            questionsList.value = res.data;
            questionsPagination.value = null;
        }
    } catch (e) {
        console.error("Erro ao carregar perguntas", e);
    }
}

function openNewQuestionForm() {
    editingQuestion.value = null;
    showQuestionForm.value = true;
}

function editQuestion(question) {
    editingQuestion.value = JSON.parse(JSON.stringify(question));
    showQuestionForm.value = true;
}

function closeQuestionForm() {
    showQuestionForm.value = false;
    editingQuestion.value = null;
}

async function handleSaveQuestion(formData) {
    try {
        if (formData.id) {
            await axios.put(`/admin/questions/${formData.id}`, formData);
            alert('Pergunta atualizada!');
        } else {
            await axios.post('/admin/questions', formData);
            alert('Pergunta criada!');
        }
        closeQuestionForm();
        loadQuestions();
        loadInsights();
    } catch (e) {
        console.error(e);
        const msg = e.response?.data?.message || 'Erro ao salvar pergunta.';
        alert(msg);
    }
}

async function deleteQuestion(id) {
    if(!confirm('Tem certeza que deseja excluir esta pergunta?')) return;
    try {
        await axios.delete(`/admin/questions/${id}`);
        loadQuestions();
        loadInsights();
    } catch(e) {
        alert('Erro ao excluir pergunta.');
    }
}

async function handleFileUpload(event) {
    const file = event.target.files[0];
    if (!file) return;

    let formData = new FormData();
    formData.append('file', file);

    try {
        const res = await axios.post('/admin/questions/import', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        alert(res.data.message || 'Importação realizada!');
        loadQuestions();
        loadInsights();
    } catch (error) {
        alert('Erro na importação: ' + (error.response?.data?.message || error.message));
    }
    event.target.value = '';
}

async function downloadCSV() {
    try {
        const response = await axios.get('/admin/questions/export', { responseType: 'blob' });
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', 'questions_export.csv');
        document.body.appendChild(link);
        link.click();
        link.remove();
    } catch (error) {
        alert('Erro ao exportar dados.');
    }
}

async function loadQuizzesHistory(url = '/admin/quizzes') {
    try {
        const res = await axios.get(url);
        if (res.data.data) {
            quizzesHistory.value = res.data.data;
            quizzesPagination.value = res.data;
        } else {
            quizzesHistory.value = res.data;
            quizzesPagination.value = null;
        }
    } catch (e) {
        console.error("Erro ao carregar histórico", e);
    }
}
</script>
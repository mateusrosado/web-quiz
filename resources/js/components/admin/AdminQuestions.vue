<script setup>
import { ref, onMounted } from 'vue';
import { usePagination } from '../../composables/usePagination';
import QuestionForm from './QuestionForm.vue';
import axios from 'axios';
import { 
    Download, 
    Upload, 
    PlusCircle, 
    Pencil, 
    Trash2,
    ChevronLeft,
    ChevronRight
} from 'lucide-vue-next';

const showQuestionForm = ref(false);
const editingQuestion = ref(null);

const { 
    data: questionsList, 
    pagination: questionsPagination, 
    loadData: loadQuestions 
} = usePagination('/admin/questions');

onMounted(() => loadQuestions());

function openNewQuestionForm() { editingQuestion.value = null; showQuestionForm.value = true; }
function editQuestion(q) { editingQuestion.value = JSON.parse(JSON.stringify(q)); showQuestionForm.value = true; }
function closeQuestionForm() { showQuestionForm.value = false; editingQuestion.value = null; }

async function handleSaveQuestion(payload, setErrors) { 
    try {
        if(payload.id) await axios.put(`/admin/questions/${payload.id}`, payload);
        else await axios.post('/admin/questions', payload);
        closeQuestionForm(); 
        loadQuestions();
    } catch(e) { 
        if(e.response?.status === 422 && setErrors) setErrors(e.response.data.errors);
        else alert('Erro ao salvar.'); 
    }
}

async function deleteQuestion(id) { 
    if(confirm('Tem certeza que deseja apagar?')) { 
        try { await axios.delete(`/admin/questions/${id}`); loadQuestions(); } 
        catch (e) { alert('Erro ao deletar.'); }
    } 
}

async function handleFileUpload(event) {
    const file = event.target.files[0];
    if (!file) return;
    let formData = new FormData();
    formData.append('file', file);
    try {
        const res = await axios.post('/admin/questions/import', formData, { headers: { 'Content-Type': 'multipart/form-data' } });
        alert(res.data.message || 'Importação realizada!');
        loadQuestions();
    } catch (error) { 
        alert('Erro: ' + (error.response?.data?.message || error.message)); 
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
    } catch (error) { alert('Erro ao exportar.'); }
}
</script>

<template>
    <div class="space-y-6">
        <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
            <div class="space-y-1">
                <p class="text-xs uppercase tracking-[0.3em] text-[#FBC209] font-bold">Conteúdo</p>
                <h1 class="text-2xl font-semibold text-[#094789]">Banco de Perguntas</h1>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <button @click="downloadCSV" 
                    class="inline-flex items-center rounded-full border border-[#094789]/20 bg-white px-5 py-2.5 text-sm font-semibold text-[#094789] transition hover:bg-[#094789]/5 hover:border-[#094789]/40 hover:shadow-sm">
                    <Download :size="16" class="mr-2" /> Exportar
                </button>

                <label class="cursor-pointer inline-flex items-center rounded-full border border-[#094789]/20 bg-white px-5 py-2.5 text-sm font-semibold text-[#094789] transition hover:bg-[#094789]/5 hover:border-[#094789]/40 hover:shadow-sm">
                    <Upload :size="16" class="mr-2" /> Importar CSV
                    <input type="file" class="hidden" @change="handleFileUpload" accept=".csv">
                </label>

                <button @click="openNewQuestionForm" 
                    class="inline-flex items-center rounded-full bg-[#094789] px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-900/20 transition hover:scale-105">
                    <PlusCircle :size="16" class="mr-2" /> Nova Pergunta
                </button>
            </div>
        </div>

        <div v-if="showQuestionForm">
            <QuestionForm :questionData="editingQuestion" @save="handleSaveQuestion" @cancel="closeQuestionForm" />
        </div>

        <div v-else class="rounded-3xl border border-[#094789]/10 bg-white px-6 py-6 shadow-sm">
            <div v-if="questionsList.length === 0" class="text-center py-12 text-slate-500">
                Nenhuma pergunta encontrada.
            </div>

            <div v-else class="space-y-4">
                <div v-for="question in questionsList" :key="question.id" 
                     class="flex flex-col md:flex-row items-start md:items-center justify-between p-4 rounded-2xl border border-gray-100 hover:border-[#094789]/30 hover:bg-[#094789]/5 transition-all group">
                    
                    <div class="flex-1 pr-4">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="inline-flex px-2 py-1 rounded-md bg-[#094789]/10 text-xs font-bold text-[#094789]">#{{ question.id }}</span>
                            <h3 class="text-[#094789] font-semibold">{{ question.description }}</h3>
                        </div>
                        
                        <div class="flex flex-wrap gap-2 mt-2">
                            <span v-for="opt in question.options" :key="opt.id" 
                                  class="text-xs px-2 py-1 rounded border"
                                  :class="opt.is_correct ? 'bg-green-50 border-green-200 text-green-700 font-bold' : 'bg-gray-50 text-gray-500'">
                                {{ opt.text }}
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 mt-4 md:mt-0">
                        <button @click="editQuestion(question)" class="p-2 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition">
                            <Pencil :size="16" />
                        </button>
                        <button @click="deleteQuestion(question.id)" class="p-2 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 transition">
                            <Trash2 :size="16" />
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="questionsPagination && questionsPagination.last_page > 1" class="mt-6 flex justify-center gap-2">
                <button 
                    @click="loadQuestions(questionsPagination.prev_page_url)" 
                    :disabled="!questionsPagination.prev_page_url" 
                    class="flex items-center px-4 py-2 rounded-full border border-[#094789]/20 text-[#094789] text-sm font-medium hover:bg-[#094789]/5 disabled:opacity-50 disabled:cursor-not-allowed transition">
                    <ChevronLeft :size="16" class="mr-1" /> Anterior
                </button>
                
                <span class="px-4 py-2 text-sm text-slate-500 font-medium px-2">
                    Pg {{ questionsPagination.current_page }} de {{ questionsPagination.last_page }}
                </span>
                
                <button 
                    @click="loadQuestions(questionsPagination.next_page_url)" 
                    :disabled="!questionsPagination.next_page_url" 
                    class="flex items-center px-4 py-2 rounded-full border border-[#094789]/20 text-[#094789] text-sm font-medium hover:bg-[#094789]/5 disabled:opacity-50 disabled:cursor-not-allowed transition">
                    Próxima <ChevronRight :size="16" class="ml-1" />
                </button>
            </div>
        </div>
    </div>
</template>
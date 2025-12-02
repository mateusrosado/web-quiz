<template>
    <div class="bg-white p-6 rounded shadow mb-6">
        <h3 class="font-bold mb-4 text-lg">{{ isEditing ? 'Editar Pergunta' : 'Adicionar Nova Pergunta' }}</h3>
        
        <form @submit.prevent="submitForm">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Enunciado da Pergunta</label>
                <input v-model="form.description" type="text" placeholder="Ex: Qual a capital do Brasil?" class="w-full border p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div v-for="(option, index) in form.options" :key="index" class="relative">
                    <label class="block text-gray-700 text-sm font-bold mb-1">
                        Opção {{ index + 1 }} 
                        <span v-if="option.is_correct" class="text-green-600 ml-2 text-xs uppercase bg-green-100 px-2 py-0.5 rounded-full">Correta</span>
                    </label>
                    <div class="flex items-center">
                        <input v-model="option.text" type="text" :placeholder="'Texto da opção ' + (index + 1)" 
                            class="w-full border p-2 rounded-l focus:outline-none focus:ring-2 focus:ring-blue-500"
                            :class="{'border-green-500 ring-1 ring-green-500': option.is_correct}"
                            required>
                        
                        <button type="button" @click="setCorrectOption(index)" 
                            class="bg-gray-100 border border-l-0 border-gray-300 p-2 rounded-r hover:bg-gray-200"
                            :class="{'bg-green-100 text-green-700 border-green-300': option.is_correct}"
                            title="Marcar como correta">
                            <span v-if="option.is_correct">✅</span>
                            <span v-else>⚪</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" @click="$emit('cancel')" class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50">Cancelar</button>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 font-bold">
                    {{ isEditing ? 'Salvar Alterações' : 'Cadastrar Pergunta' }}
                </button>
            </div>
        </form>
    </div>
</template>

<script setup>
import { ref, reactive, watch, computed } from 'vue';

const props = defineProps({
    questionData: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['save', 'cancel']);

const isEditing = computed(() => !!props.questionData);

// Estrutura padrão para nova pergunta
const defaultForm = {
    description: '',
    options: [
        { text: '', is_correct: true },  // Primeira opção correta por padrão na criação
        { text: '', is_correct: false },
        { text: '', is_correct: false },
        { text: '', is_correct: false },
    ]
};

// Estado reativo do formulário
const form = reactive(JSON.parse(JSON.stringify(defaultForm)));

// Se receber dados para edição, preenche o formulário
watch(() => props.questionData, (newVal) => {
    if (newVal) {
        form.description = newVal.description;
        // Mapeia as opções para garantir que temos 4 (ou ajuste conforme sua lógica)
        // Aqui assumimos que a edição traz os IDs também para o backend saber quais atualizar
        form.options = newVal.options.map(opt => ({
            id: opt.id,
            text: opt.text,
            is_correct: Boolean(opt.is_correct) // Garante booleano
        }));
    } else {
        Object.assign(form, JSON.parse(JSON.stringify(defaultForm)));
    }
}, { immediate: true });

const setCorrectOption = (index) => {
    form.options.forEach((opt, i) => {
        opt.is_correct = i === index;
    });
};

const submitForm = () => {
    // Validação básica extra se necessário
    if (!form.options.some(opt => opt.is_correct)) {
        alert('Selecione pelo menos uma opção correta!');
        return;
    }
    
    // Emite o evento de salvar com os dados do formulário
    // Se for edição, mandamos também o ID da pergunta original
    const payload = { ...form };
    if (isEditing.value) {
        payload.id = props.questionData.id;
    }
    
    emit('save', payload);
};
</script>
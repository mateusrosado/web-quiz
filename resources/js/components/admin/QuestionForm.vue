<script setup>
import { reactive, watch, computed } from 'vue';
import { CheckCircle2, XCircle } from 'lucide-vue-next';

const props = defineProps({
    questionData: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['save', 'cancel']);

const isEditing = computed(() => !!props.questionData);

const defaultForm = {
    description: '',
    options: [
        { text: '', is_correct: true },
        { text: '', is_correct: false },
        { text: '', is_correct: false },
        { text: '', is_correct: false },
    ]
};

const form = reactive(JSON.parse(JSON.stringify(defaultForm)));

// Lógica blindada para organizar as opções
watch(() => props.questionData, (newVal) => {
    if (newVal && Array.isArray(newVal.options)) {
        form.description = newVal.description || '';
        
        // 1. Clona as opções para não alterar o original
        let opts = JSON.parse(JSON.stringify(newVal.options));
        
        // 2. Encontra o índice da resposta correta
        // Verifica booleano (true), número (1) ou string ("1")
        const correctIndex = opts.findIndex(o => o.is_correct === true || o.is_correct === 1 || o.is_correct === "1");
        
        let correctOption = null;

        if (correctIndex !== -1) {
            // Achou! Remove da lista e guarda
            correctOption = opts.splice(correctIndex, 1)[0];
        } else {
            // Não achou nenhuma marcada como certa?
            // Pega a primeira da lista para não perder dados (fallback)
            if (opts.length > 0) {
                correctOption = opts.shift();
            } else {
                correctOption = { text: '', id: null };
            }
        }

        // Garante que o objeto da correta está marcado como true
        correctOption.is_correct = true;

        // 3. Prepara as incorretas (garantindo que sejam false)
        const incorrectOptions = opts.map(o => ({ ...o, is_correct: false }));

        // 4. Preenche com vazias se faltar para completar 3 incorretas
        while (incorrectOptions.length < 3) {
            incorrectOptions.push({ text: '', is_correct: false, id: null });
        }

        // 5. Monta o array final: [Correta, Incorreta 1, Incorreta 2, Incorreta 3]
        form.options = [correctOption, ...incorrectOptions.slice(0, 3)];

    } else {
        // Reset para criar nova
        Object.assign(form, JSON.parse(JSON.stringify(defaultForm)));
    }
}, { immediate: true });

const submitForm = () => {
    const payload = { ...form };
    if (isEditing.value) {
        payload.id = props.questionData.id;
    }
    emit('save', payload);
};
</script>

<template>
    <div class="bg-white rounded-3xl border border-[#094789]/10 shadow-lg p-8">
        <h3 class="text-xl font-bold text-[#094789] mb-6 border-b border-gray-100 pb-4">
            {{ isEditing ? 'Editar Pergunta' : 'Cadastrar Nova Pergunta' }}
        </h3>
        
        <form @submit.prevent="submitForm" class="space-y-6">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-[#094789]/70 mb-2">Enunciado</label>
                <input 
                    v-model="form.description" 
                    type="text" 
                    placeholder="Ex: Qual a capital do Ceará?" 
                    class="w-full rounded-2xl border border-[#094789]/20 px-4 py-3 text-slate-700 focus:border-[#094789] focus:ring-[#094789]/30 transition"
                    required
                >
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div v-for="(option, index) in form.options" :key="index" 
                     class="p-5 rounded-3xl border transition-colors duration-200"
                     :class="index === 0 ? 'bg-emerald-50 border-emerald-100' : 'bg-slate-50 border-slate-200'">
                    
                    <label class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider mb-2"
                           :class="index === 0 ? 'text-emerald-700' : 'text-slate-500'">
                        <component :is="index === 0 ? CheckCircle2 : XCircle" :size="18" />
                        {{ index === 0 ? 'Resposta Correta' : `Alternativa Incorreta ${index}` }}
                    </label>
                    
                    <input 
                        v-model="option.text" 
                        type="text" 
                        :placeholder="index === 0 ? 'Digite a resposta certa aqui' : 'Digite uma alternativa errada'"
                        class="w-full rounded-2xl border px-4 py-3 text-slate-700 transition focus:outline-none focus:ring-2 focus:ring-offset-1"
                        :class="index === 0 
                            ? 'border-emerald-200 focus:border-emerald-500 focus:ring-emerald-200' 
                            : 'border-slate-300 focus:border-[#094789] focus:ring-[#094789]/30'"
                        required
                    >
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" @click="$emit('cancel')" class="px-6 py-3 rounded-full border border-[#094789]/20 text-[#094789] font-bold hover:bg-[#094789]/5 transition">
                    Cancelar
                </button>
                <button type="submit" class="px-8 py-3 rounded-full bg-[#094789] text-white font-bold shadow-lg shadow-blue-900/20 hover:scale-105 transition transform">
                    Salvar
                </button>
            </div>
        </form>
    </div>
</template>
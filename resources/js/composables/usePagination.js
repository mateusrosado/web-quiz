import { ref } from 'vue';
import axios from 'axios';

export function usePagination(endpoint) {
    const data = ref([]);
    const pagination = ref({});
    const loading = ref(false);

    const loadData = async (url = endpoint) => {
        loading.value = true;
        try {
            
            const response = await axios.get(url);
            const payload = response.data;

            if (typeof payload === 'string') {
                console.error("A API retornou HTML em vez de JSON. Verifique a URL:", url);
                data.value = [];
                return;
            }
            
            if (payload.data && Array.isArray(payload.data)) {
                data.value = payload.data;
                const { data: _, ...meta } = payload; 
                pagination.value = meta;
            } else {
                data.value = Array.isArray(payload) ? payload : [];
                pagination.value = {};
            }
        } catch (error) {
            console.error(`Erro ao carregar dados de ${endpoint}:`, error);
            data.value = [];
        } finally {
            loading.value = false;
        }
    };

    return {
        data,
        pagination,
        loading,
        loadData
    };
}
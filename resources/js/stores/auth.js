import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';

export const useAuthStore = defineStore('auth', () => {
    const token = ref(localStorage.getItem('token') || '');
    const user = ref(JSON.parse(localStorage.getItem('user')) || null);

    if (token.value) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`;
    }

    const isAuthenticated = computed(() => !!token.value);
    const isAdmin = computed(() => user.value?.is_admin);

    function setToken(newToken, newUser) {
        token.value = newToken;
        user.value = newUser;
        
        localStorage.setItem('token', newToken);
        localStorage.setItem('user', JSON.stringify(newUser));
        
        axios.defaults.headers.common['Authorization'] = `Bearer ${newToken}`;
    }

    function clearToken() {
        token.value = '';
        user.value = null;
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        delete axios.defaults.headers.common['Authorization'];
    }

    async function login(email, password) {
        try {
            const response = await axios.post('/login', { email, password });
            setToken(response.data.access_token, response.data.user);
            return true;
        } catch (error) {
            console.error('Erro no login:', error);
            throw error;
        }
    }

    async function register(formData) {
        try {
            const response = await axios.post('/register', formData);
            setToken(response.data.access_token, response.data.user);
            return true;
        } catch (error) {
            console.error('Erro no registro:', error);
            throw error;
        }
    }

    async function logout() {
        try {
            if (token.value) {
                await axios.post('/logout');
            }
        } catch (error) {
            console.error('Erro ao realizar logout no servidor:', error);
        } finally {
            clearToken();
            window.location.href = '/login';
        }
    }

    return { 
        token, 
        user, 
        isAuthenticated, 
        isAdmin, 
        login,
        register,
        logout,
        setToken
    };
});
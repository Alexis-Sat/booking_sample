import { createApp } from 'vue';
import axios from 'axios';
import Dashboard from './views/Dashboard.vue';
import LoginForm from './components/LoginForm.vue';

// Установка базового URL и заголовков
axios.defaults.baseURL = '/api';
axios.interceptors.request.use(config => {
    const token = localStorage.getItem('token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

const app = createApp({
    components: {
        'login-form': LoginForm,
        'dashboard': Dashboard,
    },
    data() {
        return {
            isAuthenticated: !!localStorage.getItem('token'),
        };
    },
    methods: {
        onLogin() {
            this.isAuthenticated = true;
        },
        onLogout() {
            this.isAuthenticated = false;
            localStorage.removeItem('token');
            window.location.href = '/login';
        },
    },
});

app.mount('#app');

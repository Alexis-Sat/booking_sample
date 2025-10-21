<template>
    <div class="login-form">
        <h2>Вход</h2>
        <form @submit.prevent="submit">
            <div>
                <label>Email</label>
                <input v-model="email" type="email" required />
            </div>
            <div>
                <label>Пароль</label>
                <input v-model="password" type="password" required />
            </div>
            <button type="submit">Войти</button>
            <p v-if="error" class="error">{{ error }}</p>
        </form>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const email = ref('');
const password = ref('');
const error = ref(null);

const emit = defineEmits(['login']);

const submit = async () => {
    try {
        const response = await axios.post('/login', {
            email: email.value,
            password: password.value,
        });

        localStorage.setItem('token', response.data.access_token);
        emit('login');
    } catch (e) {
        error.value = e.response?.data?.message || 'Ошибка авторизации';
    }
};
</script>

<style scoped>
.login-form {
    max-width: 400px;
    margin: 50px auto;
    padding: 30px;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    font-family: 'Segoe UI', sans-serif;
}

.login-form h2 {
    text-align: center;
    color: #333;
    margin-bottom: 2rem;
    font-weight: 600;
}

.login-form form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.login-form div {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.login-form label {
    font-size: 1rem;
    color: #555;
    font-weight: 500;
}

.login-form input {
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
    outline: none;
}

.login-form input:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

.login-form button {
    background: #007bff;
    color: white;
    border: none;
    padding: 12px;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s ease;
    margin-top: 1rem;
}

.login-form button:hover {
    background: #0056b3;
}

.login-form button:active {
    transform: scale(0.98);
}

.login-form .error {
    text-align: center;
    color: #dc3545;
    font-size: 0.9rem;
    margin-top: 1rem;
    padding: 10px;
    background: #f8d7da;
    border-radius: 6px;
    border: 1px solid #f5c6cb;
}
</style>

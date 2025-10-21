<template>
    <div class="modal" v-if="show">
        <div class="modal-content">
            <h3>{{ isEditing ? 'Редактировать пользователя' : 'Добавить пользователя' }}</h3>
            <form @submit.prevent="submit">
                <div class="form-group">
                    <label>Имя</label>
                    <input v-model="form.name" type="text" required/>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input v-model="form.email" type="email" required/>
                </div>
                <div class="form-group">
                    <label>Пароль</label>
                    <input v-model="form.password" type="password" required/>
                </div>
                <div class="button-group">
                    <button type="submit" :disabled="loading">Сохранить</button>
                    <button type="button" @click="emit('close')" :disabled="loading">Отмена</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { computed, reactive, ref, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
    show: Boolean,
    user: Object,
});

const emit = defineEmits(['close', 'saved']);

const loading = ref(false);
const error = ref('');

const form = reactive({
    name: '',
    email: '',
    password: '',
});

watch(() => props.user, (user) => {
    if (user) {
        form.name = user.name || '';
        form.email = user.email || '';
        form.password = user.password || '';
    } else {
        resetForm();
    }
}, { immediate: true });

const resetForm = () => {
    form.name = '';
    form.email = '';
    form.password = '';
    error.value = '';
};

const submit = async () => {
    error.value = '';
    loading.value = true;

    try {
        const token = localStorage.getItem('token');
        const config = {
            headers: { Authorization: `Bearer ${token}` }
        };

        if (props.user && props.user.id) {
            await axios.put(`/users/${props.user.id}`, form, config);
        } else {
            await axios.post('/users', form, config);
        }

        emit('saved');
        emit('close');
        resetForm();
    } catch (e) {
        console.error(e);
        if (e.response?.status === 401) {
            error.value = 'Сессия истекла.';
            localStorage.removeItem('token');
            emit('logout');
        } else {
            error.value = e.response?.data?.message || 'Ошибка при сохранении.';
        }
    } finally {
        loading.value = false;
    }
};
</script>

<style scoped>
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.4);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
    backdrop-filter: blur(2px);
    transition: opacity 0.3s ease;
}

.modal-content {
    background: #fff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
    width: 500px;
    max-height: 90vh;
    overflow-y: auto;
    animation: fadeInUp 0.3s ease-out;
}

.modal-content h3 {
    font-weight: 600;
    margin-top: 0;
    margin-bottom: 20px;
    color: #2c3e50;
    font-size: 1.25rem;
    border-bottom: 2px solid #3498db; /* Акцентная линия под заголовком */
    padding-bottom: 8px;
}

/* Группа полей */
.form-group {
    margin-bottom: 18px;
    display: flex;
    align-items: flex-start; /* Чтобы метка была по верхнему краю */
}

.form-group label {
    display: block;
    width: 100px;
    margin-right: 15px;
    font-weight: 500;
    color: #555;
    text-align: right;
    line-height: 1.6;
}

.form-group input,
.form-group select,
.form-group textarea {
    flex-grow: 1;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    transition: border-color 0.2s ease;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
}

.error-message {
    color: #dc3545;
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    padding: 8px;
    border-radius: 4px;
    margin-bottom: 10px;
}

/* Кнопки */
.button-group {
    display: flex;
    gap: 10px;
    margin-top: 20px;
    justify-content: flex-end;
}

button {
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.2s ease;
    min-width: 80px;
    text-align: center;
}

button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>

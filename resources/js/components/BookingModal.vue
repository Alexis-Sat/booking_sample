<template>
    <div class="modal" v-if="show">
        <div class="modal-content">
            <h3>{{ isEditing ? 'Редактировать' : 'Забронировать' }}</h3>
            <!-- Компонент для отображения ошибок -->
            <div v-if="error" class="error-message">
                {{ error }}
            </div>
            <form @submit.prevent="submit">
                <div class="form-group">
                    <label>Заголовок</label>
                    <input v-model="form.title" type="text" required/>
                </div>
                <div class="form-group">
                    <label>Комната</label>
                    <select v-model="form.room_id" required>
                        <option v-for="room in rooms" :key="room.id" :value="room.id">{{ room.name }}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Начало</label>
                    <input v-model="form.start_time" type="datetime-local" required/>
                </div>
                <div class="form-group">
                    <label>Конец</label>
                    <input v-model="form.end_time" type="datetime-local" required/>
                </div>
                <div class="form-group">
                    <label>Описание</label>
                    <textarea v-model="form.description"></textarea>
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
import {ref, reactive, computed, watch, onMounted} from 'vue';
import axios from 'axios';

const props = defineProps({
    show: Boolean,
    booking: {
        type: Object,
        default: null,
    },
});
const error = ref(''); // Общая ошибка

const loading = ref(false); // Состояние загрузки
const resetForm = () => {
    form.title = '';
    form.room_id = '';
    form.start_time = '';
    form.end_time = '';
    form.description = '';
    // Сброс ошибок
    error.value = '';
};

const rooms = ref([]);
const form = reactive({
    title: '',
    room_id: '',
    start_time: '',
    end_time: '',
    description: '',
});

const isEditing = computed(() => {
    return !!props.booking && !!props.booking.id;
});

watch(() => props.booking, (newBooking) => {
    if (newBooking) {
        Object.keys(form).forEach(key => {
            form[key] = newBooking[key] || '';
        });
    } else {
        resetForm();
    }
}, {immediate: true});


onMounted(async () => {
    try {
        const res = await axios.get('/rooms');
        rooms.value = res.data.data || [];
    } catch (e) {
        console.error(e);
        error.value = 'Не удалось загрузить список комнат. Пожалуйста, обновите страницу.';
    }
});

const submit = async () => {
    error.value = '';
    loading.value = true; // Установить состояние загрузки

    try {
        const token = localStorage.getItem('token');
        const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

        const config = {headers: {
            Authorization: `Bearer ${token}`,
            'X-Timezone': timezone
        }};

        if (isEditing.value) {
            await axios.put(`/bookings/${props.booking.id}`, form, config);
        } else {
            await axios.post('/bookings', form, config);
        }

        emit('saved');
        emit('close');
    } catch (e) {
        console.error(e);
        if (e.response) {
            const status = e.response.status;
            const data = e.response.data;

            if (status === 401) {
                error.value = 'Сессия истекла. Пожалуйста, войдите снова.';

            } else if (status === 409) {
                error.value =  data.message || 'Запрашиваемая комната занята.';
            } else if (status === 422) {
                error.value = data.message || 'Конфликт бронирования.';
            } else if (status >= 500) {
                error.value = 'Ошибка сервера. Пожалуйста, попробуйте позже.';
            } else {
                error.value = `Ошибка запроса: ${status}`;
            }
        } else if (e.request) {

            console.error('Ошибка сети:', e.request);
            error.value = 'Нет ответа от сервера. Проверьте подключение к интернету.';
        } else {
            console.error('Ошибка настройки запроса:', e.message);
            error.value = 'Произошла ошибка при обработке запроса.';
        }
    } finally {
        loading.value = false; // Сбросить состояние
    }

};

const emit = defineEmits(['close', 'saved']);
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

<template>
    <div>
        <button style="margin: 10px;" @click="openUserModal(null)">Добавить пользователя</button>
        <button style="margin: 10px;" @click="openRoomModal(null)">Добавить комнату</button>

        <h1 class="header">Календарь</h1>
        <img src="/images/avatar.png" alt="Пользователь" class="user-icon"/>
        <h4 class="user-info">{{ authUser }}</h4>
        <button class="logout-btn" @click="logout">Выход</button>
        <Calendar class="calendar-container" ref="calendarRef" @slot-click="openCreateModal"/>
        <BookingModal
            :show="modalOpen"
            :bookings="selectedBooking"
            @close="closeModal"
            @saved="refreshBookings"
            :isEditing="isEditing"
        />
        <UserModal
            :show="userModalOpen"
            :user="selectedUser"
            @close="closeUserModal"
            @saved="refreshUsers"
        />
        <RoomModal
            :show="roomModalOpen"
            :room="selectedRoom"
            @close="closeRoomModal"
            @saved="refreshRooms"
        />
    </div>
</template>

<script setup>
import {onMounted, ref} from 'vue';
import axios from "axios";
import Calendar from '../components/Calendar.vue';
import BookingModal from '../components/BookingModal.vue';
import utc from "dayjs/plugin/utc";
import dayjs from "dayjs";
import UserModal from "../components/UserModal.vue";
import RoomModal from "../components/RoomModal.vue";

dayjs.extend(utc)
const modalOpen = ref(false);
const calendarRef = ref(null);
const selectedBooking = ref(null);
const isEditing = ref(false)
const userModalOpen = ref(false);
const roomModalOpen = ref(false);
const selectedRoom = ref(null);

const usersList = ref([]);
const rooms = ref([]);

const selectedUser = ref(null);
const openUserModal = (user) => {
    selectedUser.value = user;
    userModalOpen.value = true;

};
const closeUserModal = () => {
    userModalOpen.value = false;
    selectedUser.value = null;

};

const openRoomModal = (room) => {
    selectedRoom.value = room;
    roomModalOpen.value = true;
};

const closeRoomModal = () => {
    roomModalOpen.value = false;
    selectedRoom.value = null;
};

const refreshUsers = async () => {
    try {
        const token = localStorage.getItem('token');
        const res = await axios.get('/users', {
            headers: {Authorization: `Bearer ${token}`}
        });
        usersList.value = res.data.data || [];
    } catch (e) {
        console.error('Ошибка при обновлении пользователей:', e);
        if (e.response?.status === 401) {
            localStorage.removeItem('token');
            emit('logout');
        }
    }
};

const refreshRooms = async () => {
    try {
        const token = localStorage.getItem('token');
        const res = await axios.get('/rooms', {
            headers: {Authorization: `Bearer ${token}`}
        });
        rooms.value = res.data.data || [];
    } catch (e) {
        console.error('Ошибка при обновлении комнат:', e);
        if (e.response?.status === 401) {
            localStorage.removeItem('token');
            emit('logout');
        }
    }
};


const openCreateModal = ({date, time, bookings}) => {
    if (bookings && bookings.length > 0) {
        // Передаём массив бронирований
        selectedBooking.value = bookings;
        isEditing.value = true;
    } else {
        // Создаём новое бронирование
        const localStartDateTimeString = `${date}T${time}`;
        const localStartDate = new Date(localStartDateTimeString);
        const localEndDate = new Date(localStartDate.getTime() + 30 * 60000);
        isEditing.value = false;
        const pad = (num) => num.toString().padStart(2, '0');

        const startFormatted = `${localStartDate.getFullYear()}-${pad(localStartDate.getMonth() + 1)}-${pad(localStartDate.getDate())}T${pad(localStartDate.getHours())}:${pad(localStartDate.getMinutes())}`;
        const endFormatted = `${localEndDate.getFullYear()}-${pad(localEndDate.getMonth() + 1)}-${pad(localEndDate.getDate())}T${pad(localEndDate.getHours())}:${pad(localEndDate.getMinutes())}`;

        selectedBooking.value = [
            { // Создаём "пустое" бронирование как массив из одного элемента
                start_time: startFormatted,
                end_time: endFormatted,
                title: '',
                room_id: '',
                description: '',
                users: []
            }
        ];
    }
    modalOpen.value = true;
};

const refreshBookings = () => {
    if (calendarRef.value) {
        calendarRef.value.fetchBookings()
    }
};
const closeModal = () => {
    modalOpen.value = false;
    selectedBooking.value = null;
};

const emit = defineEmits(['logout'])
const logout = async () => {
    try {
        await axios.post('/logout', {}, {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('token')}`
            }
        });
    } catch (e) {
        console.error('Ошибка при выходе', e);
    } finally {
        localStorage.removeItem('token'); // Удаляем токен
        emit('logout')
    }
};

const authUser = ref('')
const getAuthUser = async () => {
    try {
        const res = await axios.get('/me', {}, {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('token')}`
            }
        });
        authUser.value = res.data.name || ''
    } catch (e) {
        console.error('Ошибка при выходе', e);
    }
};

onMounted(() => {
    getAuthUser()
})

</script>

<style scoped>
div {
    display: flex;
    flex-direction: column;
    align-items: center;
    min-height: 100vh;
    background: #f5f7fa;
    padding: 20px;
}

.header {
    font-size: 2.5rem;
    color: #333;
    margin-bottom: 1rem;
    text-align: center;
    font-weight: 600;
    letter-spacing: 1px;
    display: flex;
}

.logout-btn {
    position: absolute;
    top: 70px;
    right: 30px;
    background: #dc3545;
    color: white;
    border: none;
    padding: 10px 18px;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

.logout-btn:hover {
    background: #c82333;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
}

.logout-btn:active {
    transform: scale(0.98);
}

.calendar-container {
    width: 100%;
    max-width: 1000px;
    margin-top: 20px;
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}


button {
    padding: 8px 16px;
    background: #3498db;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.2s ease;
}

button:hover {
    background: #2980b9;
}

.user-icon {
    position: absolute;
    top: 20px;
    right: 45px;
    width: 45px;
    height: 45px;
    border-radius: 50%;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    z-index: 1001;
}

.user-info {
    position: absolute;
    top: 20px;
    right: 105px;
}
</style>

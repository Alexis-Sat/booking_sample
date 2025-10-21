<template>
    <div>
        <h1 class="header" >Календарь</h1>
        <button class="logout-btn" @click="logout">Выход</button>
        <Calendar class="calendar-container" ref="calendarRef" @slot-click="openCreateModal" />
        <BookingModal
            :show="modalOpen"
            :booking="selectedBooking"
            @close="closeModal"
            @saved="refreshBookings"
        />
    </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from "axios";
import Calendar from '../components/Calendar.vue';
import BookingModal from '../components/BookingModal.vue';
import utc from "dayjs/plugin/utc";
import dayjs from "dayjs";
dayjs.extend(utc)
const modalOpen = ref(false);
const calendarRef = ref(null);
const selectedBooking = ref(null);

const openCreateModal = ({ date, time, booking }) => {
    if (booking) {
        const start = dayjs.utc(booking.start_time).local().format('YYYY-MM-DDTHH:mm');
        const end = dayjs.utc(booking.end_time).local().format('YYYY-MM-DDTHH:mm');

        selectedBooking.value = {
            ...booking,
            room_id: booking.room.id,
            start_time: start,
            end_time: end,
        };
    } else {
        const localStartDateTimeString = `${date}T${time}`;

        const localStartDate = new Date(localStartDateTimeString);

        const localEndDate = new Date(localStartDate.getTime() + 30 * 60000); // 30 минут

        const pad = (num) => num.toString().padStart(2, '0');

        const startFormatted = `${localStartDate.getFullYear()}-${pad(localStartDate.getMonth() + 1)}-${pad(localStartDate.getDate())}T${pad(localStartDate.getHours())}:${pad(localStartDate.getMinutes())}`;
        const endFormatted = `${localEndDate.getFullYear()}-${pad(localEndDate.getMonth() + 1)}-${pad(localEndDate.getDate())}T${pad(localEndDate.getHours())}:${pad(localEndDate.getMinutes())}`;

        selectedBooking.value = {
            start_time: startFormatted,
            end_time: endFormatted,
            title: '',
            room_id: '',
            description: ''
        };
    }
    modalOpen.value = true;
};

const refreshBookings = () => {
    if(calendarRef.value) {
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
        emit('logout')
    }
};

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
    top: 20px;
    right: 20px;
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
</style>

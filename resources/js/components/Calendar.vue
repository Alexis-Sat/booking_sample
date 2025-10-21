<template>
    <div class="calendar">
        <div class="button-group">
            <button class="calendar-btn" @click="changeWeek(-1)">←</button>
            <h3>{{ currentWeekLabel }}</h3>
            <button class="calendar-btn" @click="changeWeek(1)">→</button>
        </div>

        <table class="calendar-table">
            <thead>
            <tr>
                <th>Время</th>
                <th v-for="day in days" :key="day.date">{{ day.dayName }}, {{ day.date }}</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="time in timeSlots" :key="time">
                <td>{{ time }}</td>
                <td
                    v-for="day in days"
                    :key="day.date + time"
                    class="time-slot"
                    :class="{ booked: isBooked(day.date, time) }"
                    @click="onSlotClick(day.date, time)"
                >

                    <div
                        v-for="(booking, index) in getBookings(day.date, time)"
                        :key="booking.id"
                        class="booking-info"
                        :style="{ zIndex: index + 1 }"
                    >
                    <div class="booking-title">{{ booking.title }}</div>
                    <div class="booking-user">Комнату забронировал: {{ booking?.user?.name }}</div>
                    <div class="booking-user">Пользователи: {{ booking.users.map(u => u.name).join(', ') }}</div>
                    <div class="booking-room">Комната: {{ booking.room.name }}</div>
                    <div class="booking-desc" v-if="booking.description">{{ booking.description }}</div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import dayjs from "dayjs";
import isBetween from 'dayjs/plugin/isBetween';
import utc from 'dayjs/plugin/utc';

dayjs.extend(isBetween);
dayjs.extend(utc);
const bookings = ref([]);
const currentWeekStart = ref(dayjs().startOf('week'));

const timeSlots = Array.from({ length: 25 }).map((_, i) => {
    const hour = Math.floor(i / 2) + 8;
    const minute = (i % 2) * 30;
    return `${hour.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')}`;
}).filter(time => {
    const [hour] = time.split(':');
    return parseInt(hour) <= 20;
});

const days = computed(() => {
    const start = currentWeekStart.value;
    return Array.from({ length: 7 }).map((_, i) => {
        const date = start.add(i, 'day');
        return {
            date: date.format('YYYY-MM-DD'),
            dayName: date.format('ddd'),
        };
    });
});

const currentWeekLabel = computed(() => {
    const start = currentWeekStart.value.format('DD MMM');
    const end = currentWeekStart.value.add(6, 'day').format('DD MMM YYYY');
    return `${start} - ${end}`;
});

onMounted(() => {
    fetchBookings();
});

const fetchBookings = async () => {

    const start = currentWeekStart.value.format('YYYY-MM-DD');
    try {
        const token = localStorage.getItem('token');
        const res = await axios.get('/calendar', {
            params: { start_date: start },
            headers: { Authorization: `Bearer ${token}` },
        });
        bookings.value = res.data.data;
    } catch (e) {
        console.error(e.response);
        if (e.response.status === 401) {
            localStorage.removeItem('token')
            emit('logout')
        }
    }
};

const isBooked = (date, time) => {
    return bookings.value.some(b => {
        const start = dayjs.utc(b.start_time);
        const end = dayjs.utc(b.end_time);
        const slot = dayjs(new Date(`${date}T${time}`)).utc();

        return slot.isBetween(start, end, 'minute', '[)');
    });
};

const getBookings = (date, time) => {
    return bookings.value.filter(b => {
        const start = dayjs.utc(b.start_time);
        const end = dayjs.utc(b.end_time);
        const slot = dayjs(new Date(`${date}T${time}`)).utc();

        return slot.isBetween(start, end, 'minute', '[)');
    });
};

const onSlotClick = (date, time) => {
    const bookingsArray = getBookings(date, time);
    emit('slot-click', { date, time, bookings: bookingsArray || [] });
};

const changeWeek = (delta) => {
    currentWeekStart.value = currentWeekStart.value.add(delta * 7, 'day');
    fetchBookings();
};

const emit = defineEmits(['slot-click', 'logout']);

defineExpose({ fetchBookings });

</script>

<style scoped>
.calendar-table {
    width: 100%;
    border-collapse: collapse;
}
th, td {
    border: 1px solid #ccc;
    padding: 8px;
    text-align: center;
}
.time-slot {
    cursor: pointer;
    background-color: #f9f9f9;
}
.time-slot:hover {
    background-color: #e0e0e0;
}
.booked {
    background-color: #d4edda;
}
.button-group {
    display: flex;
    gap: 10px;
    margin: 20px;
    justify-content: flex-end;
}

.calendar-btn {
    padding: 8px 8px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.2s ease;
    min-width: 30px;
    text-align: center;
}
.booking-info {
    background-color: #d4edda;
    border-radius: 4px;
    padding: 4px 8px;
    margin: 2px 0;
    font-size: 0.85rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.booking-info:nth-child(odd) {
    background-color: #f8d7da; /* Альтернативный цвет */
}

.more-bookings {
    color: #6c757d;
    font-size: 0.8rem;
    margin-top: 4px;
    text-align: center;
}

</style>

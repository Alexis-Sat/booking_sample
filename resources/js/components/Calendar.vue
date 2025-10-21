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

                    <div v-if="getBooking(day.date, time)" class="booking-info">
                        <div class="booking-title">{{ getBooking(day.date, time).title }}</div>
                        <div class="booking-user">Пользователь: {{ getBooking(day.date, time).user.name }}</div>
                        <div class="booking-room">Комната: {{ getBooking(day.date, time).room.name }}</div>

                       <div class="booking-desc" v-if="getBooking(day.date, time).description">{{ getBooking(day.date, time).description }}</div>
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
        console.error(e);
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

const getBooking = (date, time) => {
    return bookings.value.find(b => {
        const start = dayjs.utc(b.start_time);
        const end = dayjs.utc(b.end_time);
        const slot = dayjs(new Date(`${date}T${time}`)).utc();

        if (slot.isBetween(start, end, 'minute', '[)')) {
            return b;
        }
        return null;
    });
};

const onSlotClick = (date, time) => {
    const booking = getBooking(date, time);
    emit('slot-click', { date, time, booking: booking || null });
};

const changeWeek = (delta) => {
    currentWeekStart.value = currentWeekStart.value.add(delta * 7, 'day');
    fetchBookings();
};

const emit = defineEmits(['slot-click']);

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

</style>

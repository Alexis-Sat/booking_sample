<?php

namespace App\Repositories;

use App\Interfaces\BookingRepositoryInterface;
use App\Models\Booking;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class BookingRepository implements BookingRepositoryInterface
{

    /**
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @return Collection
     */
    public function getAll(?Carbon $startDate, ?Carbon $endDate): Collection
    {
        return Booking::with(['user', 'room', 'users'])
            ->whereBetween('start_time', [$startDate, $endDate])
            ->get();
    }

    /**
     * @param int $id
     * @return Booking
     */
    public function getById(int $id): Booking
    {
        return Booking::with(['user', 'room', 'users'])->find($id);
    }

    /**
     * @param array $data
     * @param array $utcTime
     * @return Booking
     */
    public function store(array $data, array $utcTime): Booking
    {
        $booking = Booking::create([
            'user_id' => auth()->id(),
            'meeting_room_id' => $data['room_id'],
            'start_time' => $utcTime['startTime'],
            'end_time' => $utcTime['endTime'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
        ]);

        if (!empty($data['users'])) {
            $booking->users()->attach($data['users']);
        }
        return $booking;
    }

    /**
     * @param array $data
     * @param int $id
     * @param array $utcTime
     * @return Booking
     */
    public function update(array $data, int $id, array $utcTime): Booking
    {
        $booking = Booking::with('users')->where('id', $id)->first();

        $booking->update([
            'meeting_room_id' => $data['room_id'] ?? $booking->room_id,
            'start_time' => $utcTime['startTime'],
            'end_time' => $utcTime['endTime'],
            'title' => $data['title'] ?? $booking->title,
            'description' => $data['description'] ?? $booking->description,
        ]);

        if (!empty($data['users'])) {
            $booking->users()->sync($data['users']);
        }

        return $booking;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function delete(int $id):bool
    {
        return Booking::where('id', $id)->delete();
    }

    /**
     * Checks if room is available for booking
     * @param array $timeObjects
     * @param int $roomId
     * @param int|null $existingBookingId
     * @return void
     */
    public function checkRoomAvailability(array $timeObjects, int $roomId, ?int $existingBookingId = null): bool
    {
        $query = Booking::where('meeting_room_id', $roomId);
        if ($existingBookingId) {
            $query = $query->where('id', '!=', $existingBookingId);
        }
        return $query->overlapsWith($timeObjects['startTime'], $timeObjects['endTime'])
            ->exists();
    }
}

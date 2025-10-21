<?php

namespace App\Services;

use App\Enums\BookingDurationLimits;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class BookingService
{
    /**
     * Return booking details
     * @param array $data
     * @return Collection
     */
    public function index(array $data): Collection
    {

        //       $startDate = Carbon::parse($data['start_date'])->startOfWeek();
        //       $endDate = $startDate->copy()->endOfWeek();

        return Booking::with(['user', 'room'])
            //          ->whereBetween('start_time', [$startDate, $endDate])
            ->get();

    }


    /**
     * Converts time to UTC if needed
     * @param array $data
     * @param string $clientTimezone
     * @param Booking|null $booking
     * @return array
     */
    private function timeToUTC(array $data, string $clientTimezone, ?Booking $booking = null): array
    {
        if ($data['start_time']) {
            $start = Carbon::parse($data['start_time'], $clientTimezone)->utc();
        } else {
            $start = $booking?->start_time;
        }

        if ($data['end_time']) {
            $end = Carbon::parse($data['end_time'], $clientTimezone)->utc();
        } else {
            $end = $booking?->end_time;
        }

        if (!$start || !$end) throw new \InvalidArgumentException('Wrong time selected');

        return ['startTime' => $start, 'endTime' => $end];
    }

    /**
     * Checks if time is out of limits for booking
     * @param array $timeObjects
     * @return void
     */
    private function isOutOfLimits(array $timeObjects): void
    {
        $duration = $timeObjects['startTime']->diffInMinutes($timeObjects['endTime']);

        if ($duration < BookingDurationLimits::MIN->value) {
            throw new \InvalidArgumentException(trans('validation.time.limit.min'));
        }

        if ($duration > BookingDurationLimits::MAX->value) {
            throw new \InvalidArgumentException(trans('validation.time.limit.max'));
        }
    }

    /**
     * Checks if room is available for booking
     * @param array $timeObjects
     * @param int $roomId
     * @param int|null $existingBookingId
     * @return void
     */
    private function checkRoomAvailability(array $timeObjects, int $roomId, ?int $existingBookingId = null): void
    {
        $query = Booking::where('meeting_room_id', $roomId);
        if ($existingBookingId) {
            $query = $query->where('id', '!=', $existingBookingId);
        }
          $roomIsBusy = $query->overlapsWith($roomId, $timeObjects['startTime'], $timeObjects['endTime'])
            ->exists();

        if ($roomIsBusy) {
            throw new \InvalidArgumentException('Комната уже забронирована.');
        }
    }

    /**
     * Stores booking data
     * @param array $data
     * @param string $clientTimezone
     * @return Booking
     */
    public function store(array $data, string $clientTimezone): Booking
    {
        $utcTime = $this->timeToUTC($data, $clientTimezone);

        $this->isOutOfLimits($utcTime);
        $this->checkRoomAvailability($utcTime, (int)$data['room_id']);

        $booking = Booking::create([
            'user_id' => auth()->id(),
            'meeting_room_id' => $data['room_id'],
            'start_time' => $utcTime['startTime'],
            'end_time' => $utcTime['endTime'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
        ]);

        return $booking;

    }

    /**
     * Updates booking data
     * @param array $data
     * @param string $clientTimezone
     * @param Booking $booking
     * @return Booking
     */
    public function update(array $data, string $clientTimezone, Booking $booking): Booking
    {

        $utcTime = $this->timeToUTC($data, $clientTimezone, $booking);

        $this->isOutOfLimits($utcTime);
        $this->checkRoomAvailability($utcTime, (int)$data['room_id'], $booking->id);


        $booking->update([
            'room_id' => $data['room_id'] ?? $booking->room_id,
            'start_time' => $utcTime['startTime'],
            'end_time' => $utcTime['endTime'],
            'title' => $data['title'] ?? $booking->title,
            'description' => $data['description'] ?? $booking->description,
        ]);

        return $booking;

    }


}

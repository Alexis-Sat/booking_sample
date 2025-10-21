<?php

namespace App\Services;

use App\Enums\BookingDurationLimits;
use App\Interfaces\BookingRepositoryInterface;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class BookingService
{
    private BookingRepositoryInterface $bookingRepository;

    public function __construct(BookingRepositoryInterface $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
    }
    /**
     * Return booking details
     * @param array $data
     * @return Collection
     */
    public function index(array $data): Collection
    {
        $startDate = Carbon::parse($data['start_date'])->startOfWeek();
        $endDate = $startDate->copy()->endOfWeek();

        return $this->bookingRepository->getAll($startDate, $endDate);
    }


    /**
     * Converts time to UTC if needed
     * @param array $data
     * @param string $clientTimezone
     * @param Booking|null $booking
     * @return array
     * @throws \Exception
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

        if (!$start || !$end) throw new \InvalidArgumentException(trans('validation.start_time.after_or_equal'));

        if($start->lt(Carbon::now()) || $end->lt(Carbon::now())) throw new \InvalidArgumentException(trans('validation.start_time.after_or_equal'));

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
        $roomIsBusy = $this->bookingRepository->checkRoomAvailability($timeObjects, $roomId, $existingBookingId);
        if ($roomIsBusy) {
            throw new \InvalidArgumentException('Комната уже забронирована.');
        }
    }

    /**
     * Stores booking data
     * @param array $data
     * @param string $clientTimezone
     * @return Booking
     * @throws \Exception
     */
    public function store(array $data, string $clientTimezone): Booking
    {
            $utcTime = $this->timeToUTC($data, $clientTimezone);
            $this->isOutOfLimits($utcTime);
            $this->checkRoomAvailability($utcTime, (int)$data['room_id']);

            return $this->bookingRepository->store($data, $utcTime);
    }

    /**
     * Updates booking data
     * @param array $data
     * @param string $clientTimezone
     * @param Booking $booking
     * @return Booking|JsonResponse
     * @throws \Exception
     */
    public function update(array $data, string $clientTimezone, Booking $booking): Booking|JsonResponse
    {
            $utcTime = $this->timeToUTC($data, $clientTimezone, $booking);
            $this->isOutOfLimits($utcTime);
            $this->checkRoomAvailability($utcTime, (int)$data['room_id'], $booking->id);

            return $this->bookingRepository->update($data, $booking->id, $utcTime);
    }


}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\IndexRequest;
use App\Http\Requests\Booking\StoreRequest;
use App\Http\Requests\Booking\UpdateRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Carbon;


class BookingController extends Controller
{
    protected BookingService $bookingService;

    public function __construct()
    {
        $this->bookingService = new BookingService();
    }

    /** Returns booking data
     * @param IndexRequest $request
     * @return ResourceCollection
     */
    public function index(IndexRequest $request): ResourceCollection
    {
        $data = $request->validated();
        $bookings = $this->bookingService->index($data);
        return BookingResource::collection($bookings);
    }


    /**
     * Stores booking data
     * @param StoreRequest $request
     * @return BookingResource|JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse|BookingResource
    {
        try {
            $data = $request->validated();
            $clientTimezone = request()->header('X-Timezone', 'Europe/Moscow');

            $booking = $this->bookingService->store($data, $clientTimezone);

            return new BookingResource($booking);

        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

    }


    /**
     * Shows booking data
     * @param Booking $booking
     * @return JsonResource
     */
    public function show(Booking $booking): JsonResource
    {
        return new BookingResource($booking->load(['user', 'room']));
    }


    /**
     * Updates booking data
     * @param UpdateRequest $request
     * @param Booking $booking
     * @return BookingResource|JsonResponse
     */
    public function update(UpdateRequest $request, Booking $booking): JsonResponse|BookingResource
    {
        try {
            $data = $request->validated();
            $clientTimezone = request()->header('X-Timezone', 'Europe/Moscow');

            $booking = $this->bookingService->update($data, $clientTimezone, $booking);
            return new BookingResource($booking);

        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

    }

}

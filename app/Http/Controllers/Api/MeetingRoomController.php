<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MeetingRoom\IndexRequest;
use App\Http\Requests\MeetingRoom\StoreRequest;
use App\Http\Requests\MeetingRoom\UpdateRequest;
use App\Http\Resources\MeetingRoomResource;
use App\Models\MeetingRoom;
use App\Services\MeetingRoomService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MeetingRoomController extends Controller
{
    private meetingRoomService $meetingRoomService;

    public function __construct(meetingRoomService $meetingRoomService)
    {
        $this->meetingRoomService = $meetingRoomService;
    }

    /** Returns meeting rooms
     * @param IndexRequest $request
     * @return ResourceCollection
     */
    public function index(IndexRequest $request): ResourceCollection
    {
        $rooms = $this->meetingRoomService->index();
        return MeetingRoomResource::collection($rooms);
    }

    /**
     * Show meeting room
     * @param MeetingRoom $room
     * @return MeetingRoomResource
     */
    public function show(MeetingRoom $room)
    {
        $room = $this->meetingRoomService->show($room);
        return new MeetingRoomResource($room);
    }

    /**
     * Store meeting room
     * @param StoreRequest $request
     * @return MeetingRoomResource|JsonResponse
     */
    public function store(StoreRequest $request): MeetingRoomResource|JsonResponse
    {
        try {

            $roomData = $request->validated();
            $room = $this->meetingRoomService->store($roomData);
            return new MeetingRoomResource($room);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Update meeting room
     * @param UpdateRequest $request
     * @param MeetingRoom $room
     * @return MeetingRoomResource|JsonResponse
     */
    public function update(UpdateRequest $request, MeetingRoom $room): MeetingRoomResource|JsonResponse
    {
        try {

            $roomData = $request->validated();
            $room = $this->meetingRoomService->update($roomData, $room->id);
            return new MeetingRoomResource($room);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * @param MeetingRoom $room
     * @return JsonResponse
     */
    public function destroy(MeetingRoom $room): JsonResponse
    {
        $result = $this->meetingRoomService->destroy($room);
        if ($result) {
            return response()->json('Deleted', 204);
        } else return response()->json('Not found', 404);
    }

}

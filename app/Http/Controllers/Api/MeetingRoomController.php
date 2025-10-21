<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MeetingRoom\IndexRequest;
use App\Http\Requests\MeetingRoom\StoreRequest;
use App\Http\Requests\MeetingRoom\UpdateRequest;
use App\Http\Resources\MeetingRoomResource;
use App\Models\MeetingRoom;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MeetingRoomController extends Controller
{
    /** Returns meeting rooms
     * @param IndexRequest $request
     * @return ResourceCollection
     */
    public function index(IndexRequest $request): ResourceCollection
    {
        $rooms = MeetingRoom::all();
        return MeetingRoomResource::collection($rooms);
    }

    /**
     * Show meeting room
     * @param MeetingRoom $room
     * @return MeetingRoomResource
     */
    public function show(MeetingRoom $room)
    {
        return new MeetingRoomResource($room);
    }

    /**
     * Store meeting room
     * @param StoreRequest $request
     * @return MeetingRoomResource
     */
    public function store(StoreRequest $request): MeetingRoomResource
    {
        $roomData = $request->validated();
        $room = MeetingRoom::create($roomData);
        return new MeetingRoomResource($room);
    }

    /**
     * Update meeting room
     * @param MeetingRoom $room
     * @param UpdateRequest $request
     * @return MeetingRoomResource
     */
    public function update(UpdateRequest $request, MeetingRoom $room): MeetingRoomResource
    {
        $roomData = $request->validated();
        $room->update($roomData);
        return new MeetingRoomResource($room);
    }

    /**
     * @param MeetingRoom $room
     * @return JsonResponse
     */
    public function destroy(MeetingRoom $room):JsonResponse
    {
        $room->delete();
        return response()->json('Deleted', 204);
    }

}

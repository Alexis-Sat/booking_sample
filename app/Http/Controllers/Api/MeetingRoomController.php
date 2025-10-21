<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MeetingRoom\IndexRequest;
use App\Http\Resources\MeetingRoomResource;
use App\Models\MeetingRoom;
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

}

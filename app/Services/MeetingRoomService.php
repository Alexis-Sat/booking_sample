<?php

namespace App\Services;

use App\Interfaces\MeetingRoomRepositoryInterface;
use App\Models\MeetingRoom;
use Illuminate\Support\Collection;

class MeetingRoomService
{
    private MeetingRoomRepositoryInterface $meetingRoomRepository;
    public function __construct(MeetingRoomRepositoryInterface $meetingRoomRepository)
    {
        $this->meetingRoomRepository = $meetingRoomRepository;
    }

    /**
     * @return Collection
     */
    public function index(): Collection
    {
        return $this->meetingRoomRepository->getAll();
    }

    /**
     * Show meeting room
     * @param MeetingRoom $room
     * @return MeetingRoom|null
     */
    public function show(MeetingRoom $room): ?MeetingRoom
    {
        return $this->meetingRoomRepository->getById($room->id);
    }

    /**
     * Store meeting room
     * @param $data
     * @return MeetingRoom|null
     */
    public function store($data): ?MeetingRoom
    {
        return $this->meetingRoomRepository->store($data);

    }

    /**
     * Update meeting room
     * @param $data
     * @param MeetingRoom $room
     * @return MeetingRoom|null
     */
    public function update($data, MeetingRoom $room): ?MeetingRoom
    {
        return $this->meetingRoomRepository->update($data, $room->id);
    }

    /**
     * @param MeetingRoom $room
     * @return bool
     */
    public function destroy(MeetingRoom $room):bool
    {
       return $this->meetingRoomRepository->delete($room->id);
    }

}

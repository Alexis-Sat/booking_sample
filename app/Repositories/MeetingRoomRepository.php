<?php

namespace App\Repositories;

use App\Interfaces\MeetingRoomRepositoryInterface;
use App\Models\MeetingRoom;
use Illuminate\Database\Eloquent\Collection;

class MeetingRoomRepository implements MeetingRoomRepositoryInterface
{

    /**
     * @return Collection
     */
    public function getAll(): Collection
    {
        return MeetingRoom::all();
    }

    /**
     * @param int $id
     * @return MeetingRoom
     */
    public function getById(int $id): MeetingRoom
    {
        return MeetingRoom::where('id', $id)->first();
    }

    /**
     * @param array $data
     * @return MeetingRoom
     */
    public function store(array $data): MeetingRoom
    {
        return MeetingRoom::create($data);
    }

    /**
     * @param array $data
     * @param $id
     * @return MeetingRoom
     */
    public function update(array $data, $id): MeetingRoom
    {
        $meetingRoom = MeetingRoom::where('id', $id)->first();
        $meetingRoom->update($data);
        return $meetingRoom;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function delete(int $id):bool
    {
        return MeetingRoom::where('id', $id)->delete();
    }
}

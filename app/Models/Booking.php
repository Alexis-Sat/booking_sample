<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'meeting_room_id',
        'start_time',
        'end_time',
        'title',
        'description'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function room()
    {
        return $this->belongsTo(MeetingRoom::class, 'meeting_room_id');
    }

    /**
     * Scope for checking booking time
     * @param $query
     * @param $startTime
     * @param $endTime
     * @return mixed
     */
    public function scopeOverlapsWith($query, $startTime, $endTime): mixed
    {
        return $query->where(function ($q) use ($startTime, $endTime) {
                $q->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime);
            });
    }
}

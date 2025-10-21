<?php

namespace App\Interfaces;

use Illuminate\Support\Carbon;

interface BookingRepositoryInterface
{

    /**
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @return mixed
     */
    public function getAll(?Carbon $startDate, ?Carbon $endDate): mixed;

    /**
     * @param int $id
     * @return mixed
     */
    public function getById(int $id): mixed;

    /**
     * @param array $data
     * @param array $utcTime
     * @return mixed
     */
    public function store(array $data, array $utcTime): mixed;

    /**
     * @param array $data
     * @param int $id
     * @param array $utcTime
     * @return mixed
     */
    public function update(array $data, int $id, array $utcTime): mixed;

    /**
     * @param int $id
     * @return mixed
     */
    public function delete(int $id): mixed;

}

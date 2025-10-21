<?php

namespace App\Interfaces;

interface UserRepositoryInterface
{
    /**
     * @return mixed
     */
    public function getAll(): mixed;

    /**
     * @param int $id
     * @return mixed
     */
    public function getById(int $id): mixed;

    /**
     * @param array $data
     * @return mixed
     */
    public function store(array $data): mixed;

    /**
     * @param array $data
     * @param $id
     * @return mixed
     */
    public function update(array $data, $id): mixed;

    /**
     * @param int $id
     * @return mixed
     */
    public function delete(int $id): mixed;

}

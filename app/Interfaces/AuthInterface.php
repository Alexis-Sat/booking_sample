<?php

namespace App\Interfaces;

interface AuthInterface
{
    /**
     * @param array $data
     * @return mixed
     */
    public function authenticate(array $data): mixed;

}

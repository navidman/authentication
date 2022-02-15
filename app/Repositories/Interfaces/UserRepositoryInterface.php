<?php

namespace App\Repositories\Interfaces;

interface UserRepositoryInterface
{
    public function getUser();

    public function storeUser($data);
}

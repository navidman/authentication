<?php


namespace App\Repositories;


use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class UserRepository implements UserRepositoryInterface
{
    public function getUser()
    {
        $user = Auth::user();
        return $user;
    }


    public function storeUser($data)
    {
        $user = User::create([
            'mobile' => $data->mobile,
        ]);
        return $user;
    }




}

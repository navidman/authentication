<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'mobile' => '09120001111',
                'otp' => '111111',
            ],
            [
                'mobile' => '09121112222',
                'otp' => '222222',
            ]
        ];
        foreach ($users as $user) {
            $account = User::whereMobile($user['mobile'])->first();
            if (!$account) {
                User::create($user);
            }
        }
    }
}

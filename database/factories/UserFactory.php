<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{

    public function definition()
    {
        return [
            'mobile' => 0 . rand(9000000000, 9999999999),
            'otp' => rand(100000, 999999),
        ];
    }


}

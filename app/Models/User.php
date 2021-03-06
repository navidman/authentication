<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */

    public function findForPassport($mobile) {
        return $this->where('mobile', $mobile)->first();
    }

    public function validateForPassportPasswordGrant($otp)
    {
        return true;
    }

    public function checkOtp($otp)
    {
        if ($this->otp and $this->otp == $otp and $this->otp_expired_at > Carbon::now()) {
            $this->update([
                'otp' => null
            ]);
            return true;
        }

        return false;
    }
}

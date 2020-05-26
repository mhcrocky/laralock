<?php

namespace App\Models\Auth;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', 'code', 'active'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    # map
    public function userProfileMap()
    {
        return [
            'name' => $this->userBio->name,
            'profile_img' => $this->userBio->profile_img,
            'status' => User_getStatusForHuman($this->userstat->status),
            'email' => $this->email
        ];
    }

    public function userDetailMap()
    {
        return [
            'name' => $this->userBio->name,
            'profile_img' => $this->userBio->profile_img,
            'email' => $this->email,
            'status' => User_getStatusForHuman($this->userstat->status),
            'code' => $this->code,
            'active' => ucfirst(User_getActiveStatus($this->active)),
            'registered' => Carbon_HumanDateTime($this->created_at)
        ];
    }

    # pivot
    public function userbio()
    {
        return $this->hasOne('App\Models\Auth\UserBiodata', 'code', 'code');
    }

    public function userstat()
    {
        return $this->hasOne('App\Models\Auth\UserStatus', 'code', 'code');
    }
}

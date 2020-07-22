<?php

namespace App\Models\Auth;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable, SoftDeletes;

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

    public function userInfoListMap()
    {
        return [
            'name' => $this->userBio->name,
            'profile_img' => $this->userBio->profile_img,
            'code' => $this->code,
            'active' => ucfirst(User_getActiveStatus($this->active)),
            'status' => User_getStatusForHuman($this->userstat->status),
            'last_login' => $this->userhistory()->count() ? Carbon_HumanDateTime($this->userhistory()->latest()->first()['created_at']) : 'Never Logged In',
            'registered' => Carbon_HumanDateTime($this->created_at),
            'revoke_at' => $this->deleted_at ? Carbon_HumanDateTime($this->deleted_at) : ''
        ];
    }

    public function userNewInfoDetail()
    {
        return [
            'name' => $this->userBio->name,
            'profile_img' => $this->userBio->profile_img,
            'email' => $this->email,
            'status' => User_getStatusForHuman($this->userstat->status),
            'active' => ucfirst(User_getActiveStatus($this->active)),
            'code' => $this->code,
            'access' => $this->registermember->user_access,
            'registered' => Carbon_HumanDateTime($this->created_at)
        ];
    }

    # scope
    public function scopeGetUserOnly($query, $arrCode = [])
    {
        // this query results are faster but unsorted
        // $query->select('users.*')->join('user_statuses', 'users.code', '=', 'user_statuses.code');
        // if (count($arrCode)) $query->whereIn('users.code', $arrCode);
        // $query->where('status', User_setStatus('user'));

        // this query results are sorted but slower
        $query->join('user_biodatas', 'users.code', '=', 'user_biodatas.code');
        $query->whereIn('users.code', function ($q) {
            $q->select('code')->from('user_statuses')->where('status', User_setStatus('user'));
        });
        if (count($arrCode)) $query->whereIn('users.code', $arrCode);
        $query->orderBy('name');
    }

    # relation
    public function userbio()
    {
        return $this->hasOne(UserBiodata::class, 'code', 'code')->withDefault(['info' => 'User not found']);
    }

    public function userstat()
    {
        return $this->hasOne(UserStatus::class, 'code', 'code')->withDefault(['info' => 'User not found']);
    }

    public function registermember()
    {
        return $this->hasOne(RegisterMember::class, 'user_code', 'code')->withDefault(['info' => 'User not found']);
    }

    public function userhistory()
    {
        return $this->hasMany(UserLoginHistory::class, 'code', 'code');
    }

    public function lostpassword()
    {
        return $this->hasMany(ForgetPassword::class, 'user_email', 'email');
    }

    public function userimghistory()
    {
        return $this->hasMany(UserProfileImageHistory::class, 'code', 'code')->withDefault(['info' => 'User have no profile history']);
    }
}

<?php

namespace App\Models\Access;

use Illuminate\Database\Eloquent\Model;

class ForgetPassword extends Model
{
    protected $fillable = ['user_access', 'user_email'];

    public function getLostPasswordListMap()
    {
        return [
            'name' => $this->user->userbio->name,
            'profile_img' => $this->user->userbio->profile_img,
            'code' => $this->user->code,
            'active' => ucfirst(User_getActiveStatus($this->user->active)),
            'status' => User_getStatusForHuman($this->user->userstat->status),
            'last_login' => $this->user->userhistory()->count() ? Carbon_HumanDateTime($this->user->userhistory()->latest()->first()['created_at']) : 'Never Logged In',
            'request_at' => Carbon_HumanDateTime($this->created_at)
        ];
    }

    public function getLostPasswordDetailMap()
    {
        return [
            'name' => $this->user->userbio->name,
            'profile_img' => $this->user->userbio->profile_img,
            'email' => $this->user_email,
            'active' => ucfirst(User_getActiveStatus($this->user->active)),
            'status' => User_getStatusForHuman($this->user->userstat->status),
            'access' => $this->user_access,
            'last_login' => $this->user->userhistory()->count() ? Carbon_HumanDateTime($this->user->userhistory()->latest()->first()['created_at']) : 'Never Logged In',
            'registered' => Carbon_HumanDateTime($this->user->created_at),
            'request_at' => Carbon_HumanDateTime($this->created_at)
        ];
    }

    #relation
    public function user()
    {
        return $this->belongsTo('App\Models\Auth\User', 'user_email', 'email')->withDefault(['info' => 'User not found']);
    }
}

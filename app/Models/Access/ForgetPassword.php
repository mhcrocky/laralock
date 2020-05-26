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
            'email' => $this->user_email,
            'status' => User_getStatusForHuman($this->user->userstat->status),
            'access' => $this->user_access,
            'request_at' => Carbon_HumanDateTime($this->created_at)
        ];
    }

    #relation
    public function user()
    {
        return $this->belongsTo('App\Models\Auth\User', 'user_email', 'email')->withDefault(['info' => 'User not found']);
    }
}

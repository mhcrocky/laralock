<?php

namespace App\Models\Access;

use Illuminate\Database\Eloquent\Model;

class RegisterMember extends Model
{
    protected $fillable = ['user_access', 'user_code'];

    #relation
    public function user()
    {
        return $this->belongsTo('App\Models\Auth\User', 'user_code', 'code')->withDefault(['info' => 'User not found']);
    }
}

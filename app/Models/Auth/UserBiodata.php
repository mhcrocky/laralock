<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;

class UserBiodata extends Model
{
    protected $fillable = ['code', 'name', 'profile_img'];

    #relation
    public function user()
    {
        return $this->belongsTo('App\Models\Auth\User');
    }
}

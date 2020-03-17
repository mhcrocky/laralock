<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;

class UserStatus extends Model
{
    protected $fillable = ['code', 'status'];

    #pivot
    public function user()
    {
        return $this->belongsTo('App\Models\Auth\User');
    }
}

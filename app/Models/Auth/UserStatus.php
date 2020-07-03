<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;

class UserStatus extends Model
{
    protected $fillable = ['code', 'status'];

    #relation
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;

class UserLoginHistory extends Model
{
    protected $fillable = ['code', 'ipaddr', 'info'];
}

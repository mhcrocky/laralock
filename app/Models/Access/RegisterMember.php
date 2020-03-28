<?php

namespace App\Models\Access;

use Illuminate\Database\Eloquent\Model;

class RegisterMember extends Model
{
    protected $fillable = ['user_access', 'user_code'];
}

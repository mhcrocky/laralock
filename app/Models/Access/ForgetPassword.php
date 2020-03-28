<?php

namespace App\Models\Access;

use Illuminate\Database\Eloquent\Model;

class ForgetPassword extends Model
{
    protected $fillable = ['user_access', 'user_email'];
}

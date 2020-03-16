<?php

namespace App\Http\Controllers\APIs\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Auth\AccessVerifyUser;
# Models
use App\Models\Auth\User;

class AuthController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:api', ['except' => ['login', 'register', 'register_verify', 'lost_password', 'lost_password_verify', 'password_renew']]);
    }

    public function login()
    {
        # code...
    }
}

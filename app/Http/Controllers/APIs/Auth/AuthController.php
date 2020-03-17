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
        $this->middleware('auth:api', ['except' => ['login', 'register', 'register_verify', 'lost_password', 'lost_password_verify', 'password_renew']]);
    }

    public function login()
    {
        $validator = Validator::make(request()->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json(errorResponse($validator->errors()), 202);
        }
        $credentials = request(['email', 'password']);
        if (!$token = Auth::attempt($credentials)) {
            return response()->json(errorResponse('Account not found !'), 202);
        }
        if (Auth::user()->active == User_setActiveStatus('active')) {
            return Auth::user()->createToken('_jwtApiToken')->plainTextToken;
        }
        return response()->json(errorResponse('Your account has been ' . User_getActiveStatus(Auth::user()->active)), 202);
    }
}
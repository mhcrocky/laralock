<?php

namespace App\Http\Controllers\APIs\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
# Models
use App\Models\Auth\User;
use App\Models\Access\ForgetPassword; // ! ['user_access', 'user_email']

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'verified'], ['except' => ['login', 'register', 'register_verify', 'lost_password', 'lost_password_verify', 'password_renew']]);
    }

    public function login()
    {
        $validator = Validator(request()->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
            'device' => 'nullable|string|alpha_num'
        ]);
        if ($validator->fails()) {
            return response()->json(errorResponse($validator->errors()), 202);
        }
        $credentials = request(['email', 'password']);
        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json(errorResponse('Account not found !'), 202);
        }
        if (Auth::user()->email_verified_at) {
            if (Auth::user()->active == User_setActiveStatus('active')) {
                return $this->respondWithToken(Auth::user()->createToken(request('device') ? (request('device') . "-" . getClientIpAddress()) : ("_jwtApiToken-" . getClientIpAddress()))->plainTextToken);
            }
            return response()->json(errorResponse('Your account has been ' . User_getActiveStatus(Auth::user()->active)), 202);
        }
        return response()->json(errorResponse('Please verify your account first'), 202);
    }

    public function credential()
    {
        return response()->json(dataResponse(['name' => Auth::user()->userBio->name]), 200);
    }

    public function logout()
    {
        if (Auth::user()->tokens()->delete()) {
            return response()->json(successResponse('Successfully Logout'), 201);
        }
        return response()->json(errorResponse('Failed to Logout'), 202);
    }

    public function lost_password()
    {
        $validator = Validator(request()->all(), [
            'email' => 'required|string|email'
        ], [
            'email.required' => 'Email cannot be empty',
            'email.email' => 'Format Email is wrong',
        ]);
        if ($validator->fails()) {
            return response()->json(errorResponse($validator->errors()), 202);
        }
        $getAccount = User::where('email', request('email'))->first();
        if ($getAccount) {
            sendAccessLostPassword(request('email'));
            return response()->json(successResponse('Request has been sent, please check your email!'), 200);
        }
        return response()->json(errorResponse('Account not found!'), 202);
    }

    protected function respondWithToken($token)
    {
        return response()->json(dataResponse([
            'account_name' => Auth::user()->userBio->name,
            'status' => User_getStatusForHuman(Auth::user()->userstat->status),
            'access_token' => $token,
            'token_type' => 'bearer'
        ], '', 'Authorization'));
    }
}

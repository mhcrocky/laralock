<?php

namespace App\Http\Controllers\APIs\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
# Models
use App\Models\Auth\User;
use App\Models\Access\ForgetPassword; // ! ['user_access', 'user_email']

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'verified'], ['except' => ['login', 'register', 'register_verify', 'lost_password', 'lost_password_access', 'lost_password_recover']]);
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
            return response()->json(errorResponse('Your account has been ' . User_getActiveStatus(Auth::user()->active) . ' due to bad behavior.'), 202);
        }
        return response()->json(errorResponse('Please verify your account first'), 202);
    }

    public function credential()
    {
        return response()->json(dataResponse(['name' => Auth::user()->userbio->name]), 200);
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

    public function lost_password_access()
    {
        $validator = Validator(request()->all(), [
            '_access' => 'required|string|alpha_num'
        ], [
            '_access.*' => 'Sorry, we cannot verify this request'
        ]);
        if ($validator->fails()) {
            return response()->json(errorResponse($validator->errors()), 202);
        }
        $getAccess = ForgetPassword::where('user_access', request('_access'))->first();
        if ($getAccess) {
            return response()->json(successResponse('Please recover your password now'), 200);
        }
        return response()->json(errorResponse('Sorry, we cannot verify this request'), 202);
    }

    public function lost_password_recover()
    {
        $validator = Validator(request()->all(), [
            'password' => 'required|string',
            '_access' => 'required|string|alpha_num'
        ], [
            '_access.*' => 'Sorry, we cannot verify this request'
        ]);
        if ($validator->fails()) {
            return response()->json(errorResponse($validator->errors()), 202);
        }
        $getAccess = ForgetPassword::where('user_access', request('_access'))->first();
        if ($getAccess) {
            DB::beginTransaction();
            try {
                DB::table('users')->where('email', $getAccess['user_email'])->update(['password' => Hash::make(request('password'))]);
                DB::table('forget_passwords')->where('user_email', $getAccess['user_email'])->delete();
                DB::commit();
                return response()->json(successResponse('Password updated successfully'), 201);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(errorResponse('Failed to update new password'), 202);
            }
        }
        return response()->json(errorResponse('Sorry, we cannot verify this request'), 202);
    }

    protected function respondWithToken($token)
    {
        return response()->json(dataResponse([
            'account_name' => Auth::user()->userbio->name,
            'status' => User_getStatusForHuman(Auth::user()->userstat->status),
            'access_token' => $token,
            'token_type' => 'bearer'
        ], '', 'Authorization'));
    }
}

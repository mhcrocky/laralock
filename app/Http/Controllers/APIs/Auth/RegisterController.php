<?php

namespace App\Http\Controllers\APIs\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Auth\User; // ! ['email', 'password', 'code', 'active']
use App\Models\Auth\UserBiodata; // ! ['code', 'name', 'profile_img']
use App\Models\Auth\UserStatus; // ! ['code', 'status']
use App\Models\Access\RegisterMember; // ! ['user_access', 'user_code']

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum', ['except' => ['register', 'register_verify']]);
    }

    public function register()
    {
        $validator = Validator(request()->all(), [
            'name' => 'required|string||regex:/^[a-zA-Z_\s]+$/i',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string'
        ], [
            'name.required' => 'Name cannot be empty',
            'name.regex' => 'Format Name is wrong',
            'email.required' => 'Email cannot be empty',
            'email.email' => 'Format Email is wrong',
            'email.unique' => 'Member has already been registered'
        ]);
        if ($validator->fails()) {
            return response()->json(errorResponse($validator->errors()), 202);
        }
        // return response()->json(successResponse(request()->all()), 200);
        try {
            DB::transaction(function () {
                $newCode = User_createNewCode();
                DB::table('users')->insert([
                    'email' => request('email'), 'password' => Hash::make(request('password')), 'code' => $newCode, 'active' => User_setActiveStatus('block')
                ]);
                DB::table('user_biodatas')->insert([
                    'code' => $newCode, 'name' => ucwords(request('name')), 'profile_img' => default_user_image()
                ]);
                DB::table('user_statuses')->insert([
                    'code' => $newCode, 'status' => User_setStatus('user')
                ]);
                Mail_sendRegisterVerification(request('email'), $newCode);
            }, 5);
            return response()->json(successResponse('Successfully create new member, please check your email!'), 201);
        } catch (\Exception $e) {
            return response()->json(errorResponse('Failed create new member, please try again'), 202);
        }
    }

    public function register_verify()
    {
        $validator = Validator(request()->all(), [
            '_access' => 'required|string|alpha_num'
        ], [
            '_access.*' => 'Sorry, we cannot verify this request'
        ]);
        if ($validator->fails()) {
            return response()->json(errorResponse($validator->errors()), 202);
        }
        $getAccess = RegisterMember::where('user_access', request('_access'))->first();
        if ($getAccess) {
            User::where('code', $getAccess['user_code'])->update(['email_verified_at' => Carbon_DBtimeNow(), 'active' => User_setActiveStatus('active')]);
            RegisterMember::where('user_access', request('_access'))->delete();
            return response()->json(successResponse('Account successfully verified'), 201);
        }
        return response()->json(errorResponse('Sorry, we cannot verify this request'), 202);
    }
}

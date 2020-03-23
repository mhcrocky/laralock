<?php

namespace App\Http\Controllers\APIs\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Auth\User; // ! ['email', 'password', 'code', 'active']
use App\Models\Auth\UserBiodata; // ! ['code', 'name', 'profile_img']
use App\Models\Auth\UserStatus; // ! ['code', 'status']

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
            }, 5);
            return response()->json(successResponse('Successfully create new member, welcome!'), 201);
        } catch (\Exception $e) {
            return response()->json(errorResponse('Failed create new member, please try again'), 202);
        }
    }
}

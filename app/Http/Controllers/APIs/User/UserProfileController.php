<?php

namespace App\Http\Controllers\APIs\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Auth\User;
use App\Models\Auth\UserBiodata;
use App\Models\Auth\UserProfileImageHistory;

class UserProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware(['useractive:active'], ['except' => ['index']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::where('code', Auth::user()->code)->get()->map->userProfileMap();
        return response()->json(dataResponse($user[0], '', 'profile summary'), 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        if (request()->has('_upload')) {
            if (request('_upload') == 'image') {
                $validator = Validator(request()->all(), [
                    '_image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
                ], [
                    '_image.*' => 'Image not support.'
                ]);
                if ($validator->fails()) {
                    return response()->json(errorResponse($validator->errors()), 202);
                }
                $image = request()->file('_image');
                $imgCode = randString(20);
                $saveUrl = default_url_user_image();
                $name = 'USR-' . $imgCode . '.' . $image->getClientOriginalExtension();
                $image->move(substr($saveUrl, 1), $name);
                UserProfileImageHistory::create([
                    'code' => Auth::user()->code,
                    'image_url' => "{$saveUrl}{$name}",
                    'image_name' => $image->getClientOriginalName(),
                    'image_code' => $imgCode
                ]);
                return response()->json(successResponse('Image profile uploaded.', ['origin_name' => $image->getClientOriginalName(), 'img_url' => "{$saveUrl}{$name}"]), 200);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        if (request()->has('_update')) {
            if (request('_update') == 'biodata') {
                $validator = Validator(request()->all(), [
                    'name' => 'required|string||regex:/^[a-zA-Z_\s]+$/i',
                    'image' => 'nullable|string'
                ], [
                    'name.required' => 'Name cannot be empty',
                    'name.regex' => 'Format Name is wrong',
                ]);
                if ($validator->fails()) {
                    return response()->json(errorResponse($validator->errors()), 202);
                }
                $updateBiodata = request('image') ? UserBiodata::where('code', Auth::user()->code)->update(['name' => request('name'), 'profile_img' => request('image')]) : UserBiodata::where('code', Auth::user()->code)->update(['name' => request('name')]);
                if ($updateBiodata) {
                    return response()->json(successResponse('Successfully update biodata'), 201);
                }
                return response()->json(errorResponse('Failed to update biodata'), 202);
            }
            if (request('_update') == 'password') {
                $validator = Validator(request()->all(), [
                    'old_pass' => 'required|string',
                    'new_pass' => 'required|string'
                ]);
                if ($validator->fails()) {
                    return response()->json(errorResponse($validator->errors()), 202);
                }
                if (Hash::check(request('old_pass'), Auth::user()->password)) {
                    $updatePassword = User::where('code', Auth::user()->code)->update(['password' => Hash::make(request('new_pass'))]);
                    if ($updatePassword) {
                        return response()->json(successResponse('Successfully update password'), 201);
                    }
                    return response()->json(errorResponse('Failed to update your password'), 202);
                }
                return response()->json(errorResponse('Current password is incorrect'), 202);
            }
        }
        return _throwErrorResponse();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

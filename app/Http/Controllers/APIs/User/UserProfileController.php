<?php

namespace App\Http\Controllers\APIs\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Auth\User;

class UserProfileController extends Controller
{
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
                $name = 'USR-' . randString(25) . '.' . $image->getClientOriginalExtension();
                $image->move('files/image/profile/users/', $name);
                return response()->json(successResponse('Image profile uploaded.', ['origin_name' => $image->getClientOriginalName(), 'img_name' => "/files/image/profile/users/{$name}"]), 200);
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
                ]);
                if ($validator->fails()) {
                    return response()->json(errorResponse($validator->errors()), 202);
                }
                return response()->json(successResponse('', request()->all()), 200);
            }
        }
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

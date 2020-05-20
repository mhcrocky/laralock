<?php

namespace App\Http\Controllers\APIs\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Auth\User;
use App\Models\Auth\UserLoginHistory;

class AdminMenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [];
        if (request()->has('_users')) {
            $getUser = DB::table('users')->join('user_biodatas', 'users.code', '=', 'user_biodatas.code')->join('user_statuses', 'users.code', '=', 'user_statuses.code')->select('name', 'users.code', 'profile_img', 'active', 'status', 'users.created_at')->where('user_statuses.status', User_setStatus('user'));
            if (request('_users') == 'countOnly') {
                $data['users'] = strval($getUser->count());
            } else {
                $data['users']['count'] = strval($getUser->count());
                $data['users']['list'] = $getUser->get()->map(function ($user) {
                    return ['name' => $user->name, 'profile_img' => $user->profile_img, 'status' => User_getStatusForHuman($user->status), 'code' => $user->code, 'active' => ucfirst(User_getActiveStatus($user->active)), 'registered' => Carbon_HumanDateTime($user->created_at)];
                });
            }
        }
        if (request()->has('_user')) {
            if (request('_user')) {
                $getUser = User::where('code', request('_user'));
                $getUserHistories = UserLoginHistory::where('code', request('_user'));
                $data['user'] = count($getUser->get()) ? $getUser->get()->map->userDetailMap()[0] : [];
                $data['history'] = count($getUserHistories->get()) ? $getUserHistories->get()->map->userLoginHistorySimpleMap() : [];
            }
        }
        return response()->json(dataResponse($data), 200);
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
        //
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
        //
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

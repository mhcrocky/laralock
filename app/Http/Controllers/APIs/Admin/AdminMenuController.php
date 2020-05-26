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
        $validator = Validator(request()->all(), [
            '_users' => 'nullable|string|alpha_dash',
            '_user' => 'nullable|string|alpha_num'
        ]);
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        $data = [];
        /**
         * get user count and list
         */
        if (request()->has('_users')) {
            $getUsers = $this->getUsers()->where('email_verified_at', '!=', null);
            if (request('_users') == 'countOnly') {
                $data['users'] = strval($getUsers->count());
            } else {
                $data['users']['count'] = strval($getUsers->count());
                $data['users']['list'] = $getUsers->get()->map(function ($user) {
                    $lastLogin = count($this->getUserHistory($user->code)->get()) ? Carbon_HumanDateTime($this->getUserHistory($user->code)->latest()->first()->created_at) : 'Never Logged In';
                    return ['name' => $user->name, 'profile_img' => $user->profile_img, 'code' => $user->code, 'active' => ucfirst(User_getActiveStatus($user->active)), 'last_login' => $lastLogin, 'registered' => Carbon_HumanDateTime($user->created_at)];
                });
            }
        }
        if (request()->has('_newMembers')) {
            $getNewMembers = $this->getUsers()->where('email_verified_at', null);
            if (request('_newMembers') == 'countOnly') {
                $data['newMembers'] = strval($getNewMembers->count());
            } else {
                $data['newMembers']['count'] = strval($getNewMembers->count());
                $data['newMembers']['list'] = $getNewMembers->get()->map(function ($user) {
                    return ['name' => $user->name, 'profile_img' => $user->profile_img, 'status' => User_getStatusForHuman($user->status), 'code' => $user->code, 'active' => ucfirst(User_getActiveStatus($user->active)), 'registered' => Carbon_HumanDateTime($user->created_at)];
                });
            }
        }
        /**
         * get user detail by code
         */
        if (request()->has('_user')) {
            if (request('_user')) {
                $getUser = User::where('code', request('_user'))->get();
                $getUserHistories = UserLoginHistory::where('code', request('_user'))->get();
                $data['user'] = count($getUser) ? $getUser->map->userDetailMap()[0] : [];
                $data['history'] = count($getUserHistories) ? $getUserHistories->map->userLoginHistorySimpleMap() : [];
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
        if (request()->has('_user')) {
            $validator = Validator(request()->all(), [
                '_user' => 'required|string|alpha_num',
                '_setNewActiveStatus' => 'required|string|alpha'
            ]);
            if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
            if (request()->has('_setNewActiveStatus')) {
                $setNewActiveStatus = User::where('code', request('_user'))->update(['active' => User_setActiveStatus(strtolower(request('_setNewActiveStatus')))]);
                if ($setNewActiveStatus) return response()->json(successResponse('Successfully update new active status to ' . request('_setNewActiveStatus')), 201);
                else return response()->json(errorResponse('Failed to update new active status'), 202);
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
        if (request()->has('_userDelete') && request()->has('_method')) {
            return $this->deleteUser(request('_userDelete'), request('_method'));
        }
    }

    # private methods
    /**
     * get all user
     *
     * @return void
     */
    private function getUsers()
    {
        return DB::table('users')
            ->join('user_biodatas', 'users.code', '=', 'user_biodatas.code')
            ->join('user_statuses', 'users.code', '=', 'user_statuses.code')
            ->select('name', 'users.code', 'profile_img', 'active', 'status', 'users.created_at')
            ->where('user_statuses.status', User_setStatus('user'));
    }

    /**
     * get user histories
     *
     * @param string $userCode
     * @return void
     */
    private function getUserHistory($userCode)
    {
        return UserLoginHistory::where('code', $userCode);
    }

    private function deleteUser($userCode, $delMethod)
    {
        if ($delMethod == 'force') {
            // force delete
            return response()->json('force delete ' . $userCode, 201);
        } else {
            // soft delete
            return response()->json('soft delete ' . $userCode, 201);
        }
    }
}

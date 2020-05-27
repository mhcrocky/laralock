<?php

namespace App\Http\Controllers\APIs\Admin;

use App\Http\Controllers\Controller;
use App\Models\Access\ForgetPassword;
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
            $getUsers = $this->getUsers()->whereNotNull('email_verified_at')->whereNull('deleted_at');
            if (request('_users') == 'countOnly') {
                $data['users'] = strval($getUsers->count());
            } else {
                $data['users']['count'] = strval($getUsers->count());
                $data['users']['list'] = $getUsers->get()->map->userInfoListMap();
            }
        }
        /**
         * get unlisted user count and list
         */
        if (request()->has('_unlistedUsers')) {
            $getUnlistedUsers = $this->getUsers()->whereNotNull('email_verified_at')->whereNotNull('deleted_at')->withTrashed();
            if (request('_unlistedUsers') == 'countOnly') {
                $data['unlistedUsers'] = strval($getUnlistedUsers->count());
            } else {
                $data['unlistedUsers']['count'] = strval($getUnlistedUsers->count());
                $data['unlistedUsers']['list'] = $getUnlistedUsers->get()->map->userInfoListMap();
            }
        }
        /**
         * get new member register count and list
         */
        if (request()->has('_newMembers')) {
            $getNewMembers = $this->getUsers()->whereNull('email_verified_at')->whereNull('deleted_at');
            if (request('_newMembers') == 'countOnly') {
                $data['newMembers'] = strval($getNewMembers->count());
            } else {
                $data['newMembers']['count'] = strval($getNewMembers->count());
                $data['newMembers']['list'] = $getNewMembers->get()->map->userInfoListMap();
            }
        }
        /**
         * get user lost password count and list
         */
        if (request()->has('_lostPassword')) {
            $getLostPassword = $this->getUserLostPassord();
            if (request('_lostPassword') == 'countOnly') {
                $data['lostPassword'] = strval($getLostPassword->count());
            } else {
                $data['lostPassword']['count'] = strval($getLostPassword->count());
                $data['lostPassword']['list'] = $getLostPassword->get()->map->getLostPasswordListMap();
            }
        }
        /**
         * get user detail by code
         */
        if (request()->has('_user')) {
            if (request('_user')) {
                $getUser = $this->getUser(request('_user'))->withTrashed();
                $getUserHistories = $this->getUserHistory(request('_user'));
                $data['user'] = $getUser->count() ? $getUser->get()->map->userDetailMap()[0] : [];
                $data['history'] = $getUserHistories->count() ? $getUserHistories->get()->map->userLoginHistorySimpleMap() : [];
            }
        }

        if (request()->has('_newMember')) {
            if (request('_newMember')) {
                $getUser = $this->getUser(request('_newMember'))->whereNull('email_verified_at');
                $getUserHistories = $this->getUserHistory(request('_user'));
                $data['user'] = $getUser->count() ? $getUser->get()->map->userNewInfoDetail()[0] : [];
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
                $setNewActiveStatus = $this->getUser(request('_user'))->update(['active' => User_setActiveStatus(strtolower(request('_setNewActiveStatus')))]);
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
        // return DB::table('users')
        //     ->join('user_biodatas', 'users.code', '=', 'user_biodatas.code')
        //     ->join('user_statuses', 'users.code', '=', 'user_statuses.code')
        //     ->select('name', 'users.code', 'profile_img', 'active', 'status', 'users.created_at', 'deleted_at')
        //     ->where('user_statuses.status', User_setStatus('user'));
        return User::whereIn('code', function ($query) {
            $query->select('code')->from('user_statuses')->where('status', User_setStatus('user'));
        });
    }

    private function getUser($userCode)
    {
        return User::with('userstat')->where('code', $userCode);
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

    private function getUserLostPassord()
    {
        return ForgetPassword::whereIn('user_email', function ($query) {
            $query->select('email')->from('users')->whereNull('deleted_at');
        });
    }

    /**
     * delete user using softDelete method
     *
     * @param string $userCode
     * @param string $delMethod
     * @return void
     */
    private function deleteUser($userCode, $delMethod)
    {
        $validator = Validator(['user_code' => $userCode, 'delete_method' => $delMethod], [
            'user_code' => 'required|string|alpha_num',
            'delete_method' => 'required|string|alpha'
        ]);
        if ($validator->fails()) return response()->json(errorResponse('Some inputs not correct'), 202);
        $userIdentity = $this->getUser($userCode);
        $delMethod == 'force' ? $userIdentity->withTrashed() : '';
        if ($userIdentity->count() && (User_getStatus($userIdentity->get()[0]->userstat->status) != 'admin')) {
            // anyway, who wants to kill admin :)
            $userInfo = $userIdentity->get()->map->userProfileMap()[0];
            if ($delMethod == 'force') {
                // force delete
                $forceDelete = false;
                if ($forceDelete) return response()->json(successResponse('User ' . $userInfo['name'] . ' has been removed from membership'), 201);
                else return response()->json(errorResponse('Failed to remove user ' . $userInfo['name']), 202);
            } else {
                // soft delete
                $softDelete = $this->getUser($userCode)->delete();
                if ($softDelete) return response()->json(successResponse('User ' . $userInfo['name'] . ' has been removed'), 201);
                else return response()->json(errorResponse('Failed to remove user ' . $userInfo['name']), 202);
            }
        } else {
            return response()->json(errorResponse('User not found'), 202);
        }
    }
}

<?php

namespace App\Http\Controllers\APIs\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use App\Models\Access\ForgetPassword;
use App\Models\Auth\User;
use App\Models\Auth\UserBiodata;
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
            '_unlistedUsers' => 'nullable|string|alpha_dash',
            '_newMembers' => 'nullable|string|alpha_dash',
            '_lostPasswords' => 'nullable|string|alpha_dash',
            '_user' => 'nullable|string|alpha_num',
            '_newMember' => 'nullable|string|alpha_num',
            '_lostPassword' => 'nullable|string|alpha_num',
            '_searchName' => 'nullable|string|min:3|regex:/^[a-zA-Z_\s]+$/i'
        ], [
            '_searchName.min' => 'You need at least 3 characters',
            '_searchName.regex' => 'Dude, is there really someone with that name?'
        ]);
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        $data = [];
        /**
         * get user count and list
         */
        if (request()->has('_users')) {
            $getUsers = $this->getUsers()->whereNotNull('email_verified_at');
            if (request('_users') == 'countOnly') {
                $data['users'] = strval($getUsers->count());
            } else {
                $getUsersPag = $this->setToPaginate($this->getUsers()->whereNotNull('email_verified_at'), 10, '?_users=full');
                $data['users']['count'] = strval($getUsers->count());
                $data['users']['list'] = $getUsersPag->getCollection()->map->userInfoListMap();
                $data['users']['query'] = $this->getQueryLinkPaginatePage($getUsersPag->toArray());
            }
        }
        /**
         * get unlisted user count and list
         */
        if (request()->has('_unlistedUsers')) {
            $getUnlistedUsers = $this->getUsers()->onlyTrashed();
            if (request('_unlistedUsers') == 'countOnly') {
                $data['unlistedUsers'] = strval($getUnlistedUsers->count());
            } else {
                $getUnlistedUsersPag = $this->setToPaginate($this->getUsers()->onlyTrashed(), 10, '?_unlistedUsers=full');
                $data['unlistedUsers']['count'] = strval($getUnlistedUsers->count());
                $data['unlistedUsers']['list'] = $getUnlistedUsersPag->getCollection()->map->userInfoListMap();
                $data['unlistedUsers']['query'] = $this->getQueryLinkPaginatePage($getUnlistedUsersPag->toArray());
            }
        }
        /**
         * get new member register count and list
         */
        if (request()->has('_newMembers')) {
            $getNewMembers = $this->getUsers()->whereNull('email_verified_at');
            if (request('_newMembers') == 'countOnly') {
                $data['newMembers'] = strval($getNewMembers->count());
            } else {
                $getNewMembersPag = $this->setToPaginate($this->getUsers()->whereNull('email_verified_at'), 10, '?_newMembers=full');
                $data['newMembers']['count'] = strval($getNewMembers->count());
                $data['newMembers']['list'] = $getNewMembersPag->getCollection()->map->userInfoListMap();
                $data['newMembers']['query'] = $this->getQueryLinkPaginatePage($getNewMembersPag->toArray());
            }
        }
        /**
         * get user lost password count and list
         */
        if (request()->has('_lostPasswords')) {
            $getLostPassword = $this->getUserLostPassord();
            if (request('_lostPasswords') == 'countOnly') {
                $data['lostPassword'] = strval($getLostPassword->count());
            } else {
                $getLostPasswordPag = $this->setToPaginate($this->getUserLostPassord(), 10, '?_lostPasswords=full');
                $data['lostPassword']['count'] = strval($getLostPassword->count());
                $data['lostPassword']['list'] = $getLostPasswordPag->getCollection()->map->userInfoListMap();
                $data['lostPassword']['query'] = $this->getQueryLinkPaginatePage($getLostPasswordPag->toArray());
            }
        }
        /**
         * get user by name
         */
        if (request()->has('_searchName')) {
            if (request('_searchName')) {
                $userCount = request('_condition') == 'unlisted' ? $this->getUserByName(request('_searchName'))->onlyTrashed() : $this->getUserByName(request('_searchName'))->whereNotNull('email_verified_at');
                $userLists = request('_condition') == 'unlisted' ? $this->getUserByName(request('_searchName'))->onlyTrashed() : $this->getUserByName(request('_searchName'))->whereNotNull('email_verified_at');
                $userConds = request('_condition') == 'unlisted' ? '&_condition=unlisted' : '';
                $getUserPag = $this->setToPaginate($userLists, 10, '?_searchName=' . request('_searchName') . $userConds);
                $data['users']['keyname'] = request('_searchName');
                $data['users']['count'] = strval($userCount->count());
                $data['users']['list'] = $getUserPag->getCollection()->map->userInfoListMap();
                $data['users']['query'] = $this->getQueryLinkPaginatePage($getUserPag->toArray());
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
        /**
         * get new member register detail by code
         */
        if (request()->has('_newMember')) {
            if (request('_newMember')) {
                $getUser = $this->getUser(request('_newMember'))->whereNull('email_verified_at');
                $data['user'] = $getUser->count() ? $getUser->get()->map->userNewInfoDetail()[0] : [];
            }
        }
        if (request()->has('_lostPassword')) {
            if (request('_lostPassword')) {
                $getUser = $this->getUserLostPassord()->whereIn('user_email', function ($query) {
                    $query->select('email')->from('users')->where('code', request('_lostPassword'));
                });
                $data['user'] = $getUser->count() ? $getUser->get()->map->getLostPasswordDetailMap()[0] : [];
            }
        }
        return response()->json(dataResponse($data), 200);
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
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        if (request()->has('_userSetStatus')) {
            $validator = Validator(request()->all(), [
                '_userSetStatus' => 'required|string|alpha_num',
                '_setNewActiveStatus' => 'required|string|alpha'
            ]);
            if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
            if (request()->has('_setNewActiveStatus')) {
                $getUser = $this->getUser(request('_userSetStatus'));
                if ($getUser->count() && (User_getStatus($getUser->get()[0]->userstat->status) == 'admin')) return response()->json(errorResponse('Sorry, this user is protected by system'), 202);
                $setNewActiveStatus = $getUser->update(['active' => User_setActiveStatus(strtolower(request('_setNewActiveStatus')))]);
                if ($setNewActiveStatus) return response()->json(successResponse('Successfully update new active status to ' . request('_setNewActiveStatus')), 201);
                else return response()->json(errorResponse('Failed to update new active status'), 202);
            }
        }
        if (request()->has('_userRestore')) {
            $validator = Validator(request()->all(), [
                '_userRestore' => 'required|string|alpha_num'
            ]);
            if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
            $getUser = $this->getUser(request('_userRestore'))->withTrashed();
            if ($getUser->count()) {
                $foundUser = $getUser->get()->map->userDetailMap()[0];
                $restoreUser = $getUser->restore();
                if ($restoreUser) {
                    return response()->json(successResponse('Successfully restore ' . $foundUser['name'] . ' to membership'), 201);
                } else {
                    return response()->json(errorResponse('Failed to restore ' . $foundUser['name']), 202);
                }
            } else {
                return response()->json(errorResponse('User not found'), 202);
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
        $validator = Validator(request()->all(), [
            '_userDelete' => 'string|alpha_num',
            '_method' => 'string|nullable|alpha',
            '_lostPasswdRequest' => 'string|alpha_num'
        ]);
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        if (request()->has('_userDelete') && request()->has('_method')) {
            return $this->deleteUser(request('_userDelete'), request('_method'));
        }
        if (request()->has('_lostPasswdRequest')) {
            return $this->deleteLostPasswordRequest(request('_lostPasswdRequest'));
        }
    }

    # private methods
    /**
     * get all user
     *
     * @return void
     */
    private function getUsers($param = [])
    {
        // return User::join('user_biodatas', 'users.code', '=', 'user_biodatas.code')
        //     ->orderBy('user_biodatas.name')
        //     ->whereHas('userstat', function ($query) {
        //         return $query->where('status', User_setStatus('user'));
        //     });

        return User::getUserOnly($param);
    }

    /**
     * get user by code
     *
     * @param string $userCode
     * @return void
     */
    private function getUser($userCode)
    {
        return User::with('userstat')->where('code', $userCode);
    }

    private function getUserByName($userName)
    {
        $getUserCode = Arr::pluck(UserBiodata::where('name', 'like', "%$userName%")->get(), 'code');
        return $this->getUsers(count($getUserCode) ? $getUserCode : ['abort']);
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

    /**
     * get user lost passwords
     * default => return all request from database
     *
     * @return void
     */
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
        if ($delMethod == 'force') $userIdentity->withTrashed();
        if ($userIdentity->count() && (User_getStatus($userIdentity->get()[0]->userstat->status) != 'admin')) {
            // anyway, who wants to kill admin :)
            $userInfo = $userIdentity->get()->map->userProfileMap()[0];
            if ($delMethod == 'force') {
                // force delete
                $forceDelete = $userIdentity->forceDelete();
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

    /**
     * delete lost password request
     *
     * @param string $accessCode
     * @return void
     */
    private function deleteLostPasswordRequest($accessCode)
    {
        $validator = Validator(['access_code' => $accessCode], [
            'access_code' => 'required|string|alpha_num'
        ]);
        if ($validator->fails()) return response()->json(errorResponse('Some inputs not correct'), 202);
        $getRequest = $this->getUserLostPassord()->with('user.userbio')->where('user_access', $accessCode);
        if ($getRequest->count()) {
            $getName = $getRequest->get()[0]['user']['userbio']['name'];
            $deleteRequest = $getRequest->delete();
            if ($deleteRequest) {
                return response()->json(successResponse("Successfully delete request from {$getName}"), 201);
            } else {
                return response()->json(errorResponse("Failed to delete {$getName}'s request"), 202);
            }
        } else {
            return response()->json(errorResponse('User not found'), 202);
        }
    }

    # protected method
    /**
     * paginate data
     *
     * @param array $bigData
     * @param int $setLimit
     * @param string $pageUrl
     * @return void
     */
    protected function setToPaginate($bigData, $setLimit, $pageUrl)
    {
        return $bigData->paginate($setLimit)->withPath(url()->current() . $pageUrl);
    }

    /**
     * get query page from paginated data
     *
     * @param array $queryData
     * @return void
     */
    protected function getQueryLinkPaginatePage($queryData)
    {
        return [
            'data_from' => $queryData['from'],
            'data_to' => $queryData['to'],
            'first_page' => $queryData['first_page_url'],
            'prev_page' => $queryData['prev_page_url'],
            'next_page' => $queryData['next_page_url'],
            'last_page' => $queryData['last_page_url']
        ];
    }
}

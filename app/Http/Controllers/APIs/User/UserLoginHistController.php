<?php

namespace App\Http\Controllers\APIs\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Auth\UserLoginHistory;

class UserLoginHistController extends Controller
{
    public function __construct()
    {
        $this->middleware(['useractive:active'], ['except' => ['index']]);
        $this->middleware(['checkrole:admin'], ['only' => ['update', 'destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->has('_userid') && (User_getStatus(Auth::user()->userstat->status) == 'admin')) {
            return response()->json(successResponse('Admin'), 200);
        } else {
            if (request()->has('_loghistory')) {
                $history = UserLoginHistory::where([['code', Auth::user()->code], ['created_at', '>=', Carbon_RangeDateYesterday(request('_loghistory') ? request('_loghistory') : 'last-week')]])->get()->map->userLoginHistorySimpleMap();
                return response()->json(dataResponse($history), 200);
            }
            if (request()->has('_logdetailuser')) {
                $detail = [];
                if (User_getStatus(Auth::user()->userstat->status) == 'admin') $detail = UserLoginHistory::where('log_code', request('_logdetailuser'))->get()->map->userLoginHistoryFullMap();
                else $detail = UserLoginHistory::where([['code', Auth::user()->code], ['log_code', request('_logdetailuser')]])->get()->map->userLoginHistoryFullMap();
                return response()->json(dataResponse($detail), 200);
            }
        }
        return _throwErrorResponse();
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Auth\UserLoginHistory  $userLoginHistory
     * @return \Illuminate\Http\Response
     */
    public function show(UserLoginHistory $userLoginHistory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Auth\UserLoginHistory  $userLoginHistory
     * @return \Illuminate\Http\Response
     */
    public function edit(UserLoginHistory $userLoginHistory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Auth\UserLoginHistory  $userLoginHistory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserLoginHistory $userLoginHistory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Auth\UserLoginHistory  $userLoginHistory
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserLoginHistory $userLoginHistory)
    {
        //
    }
}

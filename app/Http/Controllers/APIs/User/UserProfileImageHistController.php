<?php

namespace App\Http\Controllers\APIs\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Auth\UserProfileImageHistory;

class UserProfileImageHistController extends Controller
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
        $imgHist = UserProfileImageHistory::where('code', Auth::user()->code);
        return response()->json(dataResponse($imgHist->count() ? $imgHist->get()->map->userProfileImgHistoryListMap() : []), 200);
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
        $validator = Validator(request()->all(), [
            '_histImgCode' => 'required|string|alpha_num',
            '_method' => 'nullable|string|alpha'
        ], [
            '_histImgCode.required' => 'Image code required',
            '_histImgCode.alpha_num' => 'Image code only contain letters and numbers'
        ]);
        if ($validator->fails()) {
            return response()->json(errorResponse($validator->errors()), 202);
        }
        if ((User_getStatus(Auth::user()->userstat->status) == 'admin') && (request('_method') == 'force')) {
            return response()->json(successResponse('Admin'), 200);
        } else {
            $histImg = UserProfileImageHistory::where([['code', Auth::user()->code], ['image_code', request('_histImgCode')]]);
            if ($histImg->count()) {
                $histImg->delete();
                return response()->json(successResponse('Successfully delete history image'), 201);
            } else {
                return response()->json(errorResponse('History image not found'), 202);
            }
        }
    }
}

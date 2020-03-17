<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**
 * jwt auth default route
 * access : /api/auth/---
 */
Route::group(['middleware' => 'api', 'prefix' => 'auth'], function () {
    Route::post('/login', 'APIs\Auth\AuthController@login');
    Route::post('/logout', 'APIs\Auth\AuthController@logout');
    Route::post('/refresh', 'APIs\Auth\AuthController@refresh');
    Route::post('/usercard', 'APIs\Auth\AuthController@usercard');
    Route::post('/register', 'APIs\Auth\AuthController@register');
});

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
Route::group([/*'middleware' => 'api', */'prefix' => 'auth'], function () {
    Route::post('/login', 'APIs\Auth\AuthController@login');
    Route::post('/logout', 'APIs\Auth\AuthController@logout');
    Route::post('/refresh', 'APIs\Auth\AuthController@refresh');
    Route::post('/usercard', 'APIs\Auth\AuthController@usercard');
    Route::post('/register', 'APIs\Auth\RegisterController@register');

    Route::post('/lost-password', 'APIs\Auth\AuthController@lost_password');
    Route::post('/lost-password/access', 'APIs\Auth\AuthController@lost_password_access');
    Route::post('/lost-password/recover', 'APIs\Auth\AuthController@lost_password_recover');

    Route::get('/creds', 'APIs\Auth\AuthController@credential');
});

/**
 * access rest
 * for verify and recover password
 * access : /api/access/---
 */
Route::group([/*'middleware' => 'api', */'prefix' => 'access'], function () {
    Route::post('/register/verify', 'APIs\Auth\RegisterController@register_verify');
});

/**
 * testing token user
 */
Route::group(['middleware' => ['auth:sanctum', 'verified'], 'prefix' => 'user'], function () {
    Route::resource('/me', 'APIs\UserTestController')->except(['create', 'edit']);
});


/**
 * ! issue line 20
 * @ Need to create a middleware which only accepts from trusted front-end
 */

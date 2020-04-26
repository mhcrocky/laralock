<?php

/**
 * use libraries
 */

use Illuminate\Support\Str;

/**
 * use models
 */

use App\Models\Auth\User;
use App\Models\Auth\UserBiodata;
use App\Models\Auth\UserStatus;

/** */

/**
 * set user status active
 * for database
 *
 * @param string $status
 * @return void
 */
function User_setActiveStatus($status)
{
    if ($status == 'active') {
        return '7';
    }
    if ($status == 'suspend') {
        return '4';
    }
    if ($status == 'block') {
        return '5';
    }
}

/**
 * get user status active
 * for human
 *
 * @param string $status
 * @return void
 */
function User_getActiveStatus($status)
{
    if ($status == '7') {
        return 'active';
    }
    if ($status == '4') {
        return 'suspend';
    }
    if ($status == '5') {
        return 'block';
    }
    return 'black-list';
}

/**
 * get user active by code
 *
 * @param string $code
 * @return void
 */
function User_getActiveStatusByCode($code)
{
    $user = User::where('code', $code)->first('active');
    return $user && $user['active'] ? $user['active'] : null;
}

/**
 * get user status active condition
 * true or false
 *
 * @param string $code
 * @return void
 */
function User_isActive($code)
{
    $user = User::where('code', $code)->first('active');
    return $user && (User_getActiveStatus($user['active'] == 'active')) ? true : false;
}

/**
 * get user name by code
 *
 * @param string $code
 * @return void
 */
function User_getNameByCode($code)
{
    $user = UserBiodata::where('code', $code)->first('name');
    return $user && $user['name'] ? $user['name'] : null;
}

/**
 * get user status by code
 *
 * @param string $code
 * @return void
 */
function User_getStatusByCode($code)
{
    $user = UserStatus::where('code', $code)->first('status');
    return $user && $user['status'] ? $user['status'] : null;
}

/**
 * set user status
 * for DB
 *
 * @param string $status
 * @return void
 */
function User_setStatus($status)
{
    if ($status == 'admin') {
        return 'greatadmin';
    }
    if ($status == 'user') {
        return 'bestuser';
    }
}

/**
 * get user status
 * for Human
 *
 * @param string $status
 * @return void
 */
function User_getStatus($status)
{
    if ($status == 'greatadmin') {
        return 'admin';
    }
    if ($status == 'bestuser') {
        return 'user';
    }
}

/**
 * get user status
 * convert for Human
 *
 * @param string $status
 * @return void
 */
function User_getStatusForHuman($status)
{
    if ($status == 'greatadmin') {
        return 'Admin';
    }
    if ($status == 'bestuser') {
        return 'User';
    }
}

/**
 * set gender type 
 * to DB
 *
 * @param string $gender
 * @return void
 */
function User_setGender($gender)
{
    if ($gender == 'L') {
        return 'M';
    }
    if ($gender == 'P') {
        return 'F';
    }
    return '-';
}

/**
 * get gender type 
 * from DB
 *
 * @param string $gender
 * @return void
 */
function User_getGender($gender)
{
    if ($gender == 'M') {
        return 'L';
    }
    if ($gender == 'F') {
        return 'P';
    }
    return '-';
}

/**
 * get gender type convert
 * for Human
 *
 * @param string $gender
 * @return void
 */
function User_getGenderForHuman($gender)
{
    if ($gender == 'M') {
        return 'Laki-Laki';
    }
    if ($gender == 'F') {
        return 'Perempuan';
    }
    return 'Lain';
}

/**
 * create new user code
 *
 * @return void
 */
function User_createNewCode()
{
    return Str::random(64);
}

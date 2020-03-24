<?php

/**
 * use libraries
 */

use Illuminate\Support\Str;
use Illuminate\Support\Arr;

/**
 * use models
 *
 */

/** */

/**
 * handler template theme asset
 *
 * @return void
 */
# online version
function online_asset()
{
    return 'http://bachtiars.com/AdminLTE-3.0.2/';
}
# offline version
function offline_asset()
{
    return asset('AdminLTE-3.0.2');
}

/**
 * icon apps
 *
 * @return void
 */
function apps_icon()
{
    return online_asset() . '/dist/img/AdminLTELogo.png';
}

/**
 * default user image
 *
 * @return void
 */
function default_user_image()
{
    return '/files/image/profile/default.jpg';
}

/**
 * create allowed url for user
 *
 * @param array $data
 * @return void
 */
function globalUrlAllowedMap($data)
{
    return [
        'index' => $data['index'], 'type' => $data['type'], 'name' => $data['url_name'], 'icon' => $data['url_icon'], 'link' => $data['url_link'], 'description' => $data['url_desc']
    ];
}

/**
 * get ip address client
 *
 * @return void
 */
function getClientIpAddress()
{
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if (getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if (getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if (getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if (getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
    else if (getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
    // request()->headers->get('origin')
    // request()->header('User-Agent')
}

/**
 * create custom amount random string
 *
 * @param int $rand_amount
 * @return void
 */
function randString($rand_amount)
{
    return Str::random($rand_amount);
}

/**
 * get random element from array data
 *
 * @param array $array_data
 * @return void
 */
function randArray($array_data)
{
    return Arr::random($array_data);
}

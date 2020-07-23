<?php

/**
 * use libraries
 */

use Illuminate\Support\Facades\Mail;

/**
 * mail class
 */

use App\Mail\Access\RecoverPasswordMail;
use App\Mail\Access\RegisterMail;

/**
 * use models
 */

use App\Models\Access\ForgetPassword; // ! ['user_access', 'user_email']
use App\Models\Access\RegisterMember; // ! ['user_access', 'user_code']

/** */

/**
 * create new access token code
 *
 * @return void
 */
function Mail_createNewAccessToken()
{
    return randString(32);
}

/**
 * send email for verify new register user
 *
 * @param string $mailto
 * @param string $usercode
 * @return void
 */
function Mail_sendRegisterVerification($mailto, $usercode)
{
    $access_key = Mail_createNewAccessToken();
    $url = env('SANCTUM_STATEFUL_DOMAINS') . "#/register/verify/{$access_key}";
    RegisterMember::create(['user_access' => $access_key, 'user_code' => $usercode]);
    Mail::to($mailto)->send((new RegisterMail)->subject(env('APP_NAME') . ' Account Verification')->markdown('emails.access.registermail', ['url' => $url]));
}

/**
 * preview mail for verify new register user
 *
 * @param string $access_key
 * @return void
 */
function Mail_viewRegisterVerification($access_key)
{
    $getRequest = RegisterMember::where('user_access', $access_key);
    if ($getRequest->count()) {
        $url = env('SANCTUM_STATEFUL_DOMAINS') . "#/register/verify/{$access_key}";
        return (new RegisterMail)->markdown('emails.access.registermail', ['url' => $url]);
    }
    return _throwErrorResponse();
}

/**
 * send email for access recover password
 *
 * @param string $mailto
 * @return void
 */
function Mail_sendAccessLostPassword($mailto)
{
    $access_key = Mail_createNewAccessToken();
    $url = env('SANCTUM_STATEFUL_DOMAINS') . "#/lost-password/recover/{$access_key}";
    ForgetPassword::create(['user_access' => $access_key, 'user_email' => $mailto]);
    Mail::to($mailto)->send((new RecoverPasswordMail)->subject(env('APP_NAME') . ' Password Recovery')->markdown('emails.access.recoverpasswordmail', ['url' => $url]));
}

/**
 * preview email for access recover password
 *
 * @param string $access_key
 * @return void
 */
function Mail_viewAccessLostPassword($access_key)
{
    $getRequest = ForgetPassword::where('user_access', $access_key);
    if ($getRequest->count()) {
        $url = env('SANCTUM_STATEFUL_DOMAINS') . "#/lost-password/recover/{$access_key}";
        return (new RecoverPasswordMail)->markdown('emails.access.recoverpasswordmail', ['url' => $url]);
    }
    return _throwErrorResponse();
}

<?php

/**
 * use libraries
 */

use Illuminate\Support\Facades\Mail;

/**
 * mail class
 */

use App\Mail\Access\RegisterMail;

/**
 * use models
 */

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
    $url = env('SANCTUM_STATEFUL_DOMAINS') . "/register/verify/{$access_key}";
    RegisterMember::create(['user_access' => $access_key, 'user_code' => $usercode]);
    Mail::to($mailto)->send((new RegisterMail)->markdown('emails.access.registermail', ['url' => $url]));
}

/**
 * send email for access user change password
 *
 * @param string $mailto
 * @param string $access_key
 * @return void
 */
function sendAccessLostPassword($mailto, $access_key)
{
    $url = url("/api/access/signin/lost/verify?_access={$access_key}");
    Mail::to($mailto)->send((new LostPasswordMail)->markdown('emails.register.lostpassword', ['url' => $url]));
}

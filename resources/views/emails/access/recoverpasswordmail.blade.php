@component('mail::message')
# Request Forget Password

We received a request to make a password change, press the button below to continue the process, but if you dont feel like sending a request to make a password change, please just ignore it.<br>We recommend that you frequently make changes to your password for the security of your account.

@component('mail::button', ['url' => $url])
Change Password
@endcomponent

Or this url : <a href="{{ $url }}">{{ $url }}</a><br>
Thanks,<br>
# {{ config('app.name') }}
@endcomponent
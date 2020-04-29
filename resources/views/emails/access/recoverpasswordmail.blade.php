@component('mail::message')
# Request Forget Password

Hmmm, did you forget your password? relax, press the button below and change your password immediately.<br>We recommend that you frequently make changes to your password for the security of your account.

@component('mail::button', ['url' => $url])
Change Password
@endcomponent

Or this url : <a href="{{ $url }}">{{ $url }}</a><br>
Thanks,<br>
# {{ config('app.name') }}
@endcomponent
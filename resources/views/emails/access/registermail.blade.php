@component('mail::message')
# Welcome to {{ config('app.name') }}

Please verify your account by click this button below.

@component('mail::button', ['url' => $url])
Verify Account
@endcomponent

Or this url : <a href="{{ $url }}">{{ $url }}</a><br>
Thanks,<br>
# {{ config('app.name') }}
@endcomponent

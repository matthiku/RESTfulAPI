@component('mail::message')
# Hello {{ $user->name }},

You have changed your email address and therefore need to verify your new email address again:

@component('mail::button', ['url' => route('verify', $user->verification_token) ])
Verify new email
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent

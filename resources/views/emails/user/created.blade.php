@component('mail::message')
# Welcome!

Your user account was created on the Cyber Range manager. You can now **deploy
and manage Virtual Machines** on the Cyber Range.

You can login using these credentials :

email: **{{ $email }}**

password: **{{ $password }}**

@component('mail::button', ['url' => route('home')])
Login
@endcomponent

Or copy-paste this link in your browser: {{ route('home') }}

Greetings,
@endcomponent

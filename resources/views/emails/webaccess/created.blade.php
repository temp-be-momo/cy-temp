@component('mail::message')
# Web Access!

Your **web access** was created on the Cyber Range. You can now **use your
Virtual Machines** on the Cyber Range.

You can login using these credentials :

username: **{{ $username }}**

password: **{{ $password }}**

@component('mail::button', ['url' => route('guacamole')])
Login
@endcomponent

Or copy-paste this link in your browser: {{ route('guacamole') }}

Greetings,
@endcomponent

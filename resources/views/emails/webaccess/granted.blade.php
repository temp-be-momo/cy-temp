@component('mail::message')
# {{ $blueprint->getHostname() }}

The Virtual Machine **{{ $blueprint->getHostname() }}** is ready for you
on the **Cyber Range**.

You can access the VM using the web interface:

@component('mail::button', ['url' => route('guacamole')])
Login
@endcomponent

@if ($blueprint->needGuestConfig())
Here are the **credentials for the VM**:

login: **vagrant**

password: **{{ $blueprint->getPassword() }}**
@endif

IP: **{{ $blueprint->getVm()->getNetworkAdapter(0)->getIPAddress() }}**

Greetings,

cylab.be
@endcomponent

@component('mail::message')
# Deployed {{ $job->vm_name }}

**{{ $job->vm_name }}** was deployed on the **Cyber Range**.


IP: **{{ $blueprint->getVm()->getNetworkAdapter(0)->getIPAddress() }}**

@if ($blueprint->needGuestConfig())
login: **vagrant**

password: **{{ $blueprint->getPassword() }}**
@endif

Greetings,

cylab.be
@endcomponent

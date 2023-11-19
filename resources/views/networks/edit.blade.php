@extends('layouts.app')

@section('content')
<h1>Network</h1>

<form action="{{ action('NetworkController@store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" name="name" id="name" class="form-control"
               required autofocus>
    </div>
    
    <div class="mb-3">
        <label class="form-label">IP</label>
        <input type="text" name="ip" id="ip" class="form-control"
               placeholder="192.168.0.1"
               required>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Mask</label>
        <input type="text" name="mask" id="mask" class="form-control"
               value="255.255.255.0"
               required>
    </div>
    
    <div class="mb-3">
        <label class="form-label">DHCP pool : First IP</label>
        <input type="text" name="lower_ip" id="lower_ip" class="form-control"
               placeholder="192.168.0.100"
               required>
    </div>
    
    <div class="mb-3">
        <label class="form-label">DHCP pool : Last IP</label>
        <input type="text" name="upper_ip" id="upper_ip" class="form-control"
               placeholder="192.168.0.200"
               required>
    </div>
    
    <div class="mb-3">
        <button class="btn btn-primary" type="submit">Save</button>
    </div>
</form>
@endsection

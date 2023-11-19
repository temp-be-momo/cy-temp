@extends('layouts.app')

@section('title', $network->name())

@section('content')
<h1>{{ $network->name() }}</h1>

<div class="mb-2">
    <form action='{{ action('NetworkController@destroy', ["network" => $network->name()]) }}'
          method='POST'>
        @csrf
        @method('DELETE')
        <button type="submit" class='btn btn-danger'>
        <i class="fas fa-times-circle"></i> Delete
        </button>
</div>

<div class='card mb-3'>
    <div class='card-header'>DHCP server</div>
    <div class='card-body'>
        <p>IP: {{ $network->ipAddress() }}</p>
        <p>Mask: {{ $network->networkMask() }}</p>
        <p>IP pool: {{ $network->lowerIP() }} - {{ $network->upperIP() }}</p>
    </div>
</div>


<div class='card'>
    <div class='card-header'>Attached machines</div>
    <div class='card-body'>
        @include('vm.table', ["vms" => $machines])
    </div>
</div>
@endsection

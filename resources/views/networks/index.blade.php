@extends('layouts.app')

@section('title', "Networks")

@section('content')
<h1>Networks</h1>

<div class="mb-2">
    <a class="btn btn-primary" href="{{ action('NetworkController@create') }}">
        <i class="fas fa-plus-circle"></i> New
    </a>
</div>

<table class="table table-lined">
    <tr>
        <th>Name</th>
        <th>Type</th>
        <th>Subnet</th>
    </tr>
    
    @foreach ($networks as $network)
    <tr>
        <td>
            <a href='{{ action('NetworkController@show', ["network" => $network->name()]) }}'
               class='text-decoration-none'>
                {{ $network->name() }}
            </a>
        </td>
        <td>
            <span class="badge bg-primary">Internal</span>
            <span class="badge bg-success">DHCP</span>
        </td>
        <td>
            {{ $network->ipAddress() }}
        </td>
    </tr>
    @endforeach
</table>

@endsection

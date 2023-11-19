@extends('layouts.app')

@section('title', 'Scenarios')

@section('content')
<h1>Scenarios</h1>
<p>
    <span class="badge bg-warning text-dark">Experimental feature!</span>
</p>
<p>
    <a href="{{ action('ScenarioController@create') }}" class="btn btn-primary">
        <i class="fas fa-plus-circle"></i> New
    </a>
</p>

<table class="table table-lined">
    <tr>
        <th>Name</th>
        <th></th>
    </tr>
    @foreach($scenarios as $scenario)
    <tr>
        <td>{{ $scenario->name }}</td>
        <td class="text-right">
            <a class="btn btn-primary btn-sm my-1"
               href="{{ action('ScenarioController@show', ['scenario' => $scenario]) }}">
                 Show
            </a>

            <a class="btn btn-primary btn-sm my-1"
               href="{{ action('ScenarioController@edit', ['scenario' => $scenario]) }}">
                 Edit
            </a>

            <form method="POST"
                  action="{{ action('ScenarioController@destroy', ['scenario' => $scenario]) }}"
                  style="display: inline-block">
                {{ csrf_field() }}
                {{ method_field("DELETE") }}
                <button class="btn btn-danger btn-sm my-1">
                     Delete
                </button>
            </form>
        </td>
    </tr>
    @endforeach
</table>
@endsection

@extends('layouts.app')

@section('title', 'Accounts')

@section('content')
<h1>Guacamole Web Accounts</h1>
<p>
    <span class="badge bg-warning text-dark">Warning</span> Cyrange and Guacamole
    accounts should remain in sync. Don't make modifications here unless you
    really know what you are doing...
</p>
<p>
    <a href="{{ action('AccountController@create') }}" class="btn btn-primary">
        <i class="fas fa-plus-circle"></i> New
    </a>

    <a href="{{ route('guacamole') }}" class="btn btn-primary">
        <i class="fas fa-desktop"></i> Open Guacamole
    </a>
</p>

<table class="table table-lined">
    <tr>
        <th>Username</th>
        <th>Connections</th>
        <th></th>
    </tr>
    @foreach($accounts as $account)
    <tr>
        <td>{{ $account->getUsername() }}</td>
        <td>{{ $account->connections()->count() }}</td>
        <td class="text-right">
            <a class="btn btn-primary btn-sm"
               href="{{ action('AccountController@show', ['account' => $account]) }}">
                <i class="fas fa-search"></i> Show
            </a>

            <a class="btn btn-primary btn-sm"
               href="{{ action('AccountController@edit', ['account' => $account]) }}">
                <i class="fas fa-edit"></i> Edit
            </a>

            <form method="POST"
                  action="{{ action('AccountController@destroy', ['account' => $account]) }}"
                  style="display: inline-block">
                {{ csrf_field() }}
                {{ method_field("DELETE") }}
                <button class="btn btn-danger btn-sm">
                    <i class="fas fa-times-circle"></i> Delete
                </button>
            </form>
        </td>
    </tr>
    @endforeach
</table>
@endsection

@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-header">{{ $account->getUsername() }}</div>

    <div class="card-body">
        <p>Email: {{ $account->email_address }}</p>

        <table class="table table-lined">

            <tr>
                <th>VM</th>
                <th>RDP port</th>
                <th></th>
            </tr>

            @foreach($account->connections as $connection)
            @php
            $vm = \App\VM::findByRDPPort($connection->getPort());
            @endphp

            <tr>
                <td>
                @if($vm != null)
                    <a href='{{ action('VMController@show', ['vm' => $vm]) }}'>
                        {{ $vm->getName() }}
                    </a>
                @else
                    &lt;none&gt;
                @endif
                </td>

                <td>
                     {{ $connection->getPort() }}
                </td>

                <td class="text-right">
                    <form method="POST"
                          action="{{ action('ConnectionController@destroy', ['connection' => $connection]) }}"
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

        <div>
            <a class="btn btn-primary"
               href="{{ action('AccountController@edit', ['account' => $account]) }}">
                <i class="fas fa-edit"></i> Edit
            </a>

            <form method="POST"
                  action="{{ action('AccountController@destroy', ['account' => $account]) }}"
                  style="display: inline-block">
                {{ csrf_field() }}
                {{ method_field("DELETE") }}
                <button class="btn btn-danger">
                    <i class="fas fa-times-circle"></i> Delete
                </button>
            </form>
        </div>
    </div>
</div>

@endsection

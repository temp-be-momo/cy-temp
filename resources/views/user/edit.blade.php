@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">User</div>

    <div class="card-body">
        @if (!$user->exists)
        <form method="POST" action="{{ action("UserController@store") }}">
        @else
        <form method="POST"
              action="{{ action("UserController@update", ["user" => $user]) }}">
            {{ method_field("PUT") }}
        @endif

            {{ csrf_field() }}

            <div class="mb-3">
                <label for="name" class="form-label">
                    Name
                </label>

                <input id="name" type="text"
                       class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                       name="name"
                       value="{{ old('name', $user->name) }}" required autofocus>

                @if ($errors->has('name'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                @endif
            </div>


            <div class="mb-3">
                <label for="email" class="form-label">
                    Email
                </label>

                <input id="email" type="email"
                       class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                       name="email"
                       placeholder="me@example.com"
                       value="{{ old('email', $user->email) }}">

                @if ($errors->has('email'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
            </div>

            <div class="mb-3">
                <label for="administrator" class="form-label">
                    Administrator
                </label>

                
                <div class="form-check">
                    <input id="administrator" type="checkbox"
                           class="form-check-input"
                           name="administrator"
                           {{ $user->isAdmin() ? 'checked' : '' }}>
                </div>
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check"></i> Save
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

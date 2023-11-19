@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">Gateway Account</div>

    <div class="card-body">
        @if (!isset($account))
        <form method="POST" action="{{ action("AccountController@store") }}">
        @else
        <form method="POST"
              action="{{ action("AccountController@update", ["account" => $account]) }}">
        {{ method_field("PUT") }}
        @endif
            {{ csrf_field() }}

            <div class="mb-3">
                <label for="username" class="form-label">Username</label>

                <input id="username" type="text"
                       class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}"
                       name="username"
                       value="{{ old('username', isset($account) ? $account->getUsername() : '') }}" required autofocus>

                @if ($errors->has('username'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('username') }}</strong>
                    </span>
                @endif
            </div>

            <div class="mb-3">
                <label for="email_address" class="form-label">Email</label>

                <input id="email_address" type="email"
                       class="form-control{{ $errors->has('email_address') ? ' is-invalid' : '' }}"
                       name="email_address"
                       value="{{ old('email_address', $account->email_address ?? '') }}" required>

                @if ($errors->has('email_address'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('email_address') }}</strong>
                    </span>
                @endif
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>

                <input id="name" type="password"
                       class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                       name="password">

                @if ($errors->has('password'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
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

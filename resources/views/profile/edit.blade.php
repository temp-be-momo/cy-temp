@extends('layouts.app')

@section('content')

<h1>Profile</h1>

<div class="card my-4">
    <div class="card-header">Info</div>

    <div class="card-body">
        {{ $user->email }}
    </div>
</div>

<div class="card my-4">
    <div class="card-header">Change password</div>

    <div class="card-body">
        <form method="POST"
              action="{{ action("ProfileController@updatePassword") }}">
            {{ method_field("PUT") }}
            {{ csrf_field() }}

            <div class="mb-3">
                <label for="old_password" class="form-label">Old password</label>

                <input id="old_password" type="password"
                       class="form-control{{ $errors->has('old_password') ? ' is-invalid' : '' }}"
                       name="old_password"
                       value="{{ old('old_password') }}" required>

                @if ($errors->has('old_password'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('old_password') }}</strong>
                    </span>
                @endif
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">New password</label>

                <input id="password" type="password"
                       class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                       name="password"
                       value="{{ old('password') }}" required>

                @if ($errors->has('password'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">
                    New password (confirm)
                </label>

                <input id="password_confirmation" type="password"
                       class="form-control{{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}"
                       name="password_confirmation"
                       value="{{ old('password_confirmation') }}" required>

                @if ($errors->has('password_confirmation'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                    </span>
                @endif
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check"></i> Update password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

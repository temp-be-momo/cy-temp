@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">VM</div>

    <div class="card-body">
        <form method="POST" action="{{ action("VMController@store") }}">

            {{ csrf_field() }}

            <div class="mb-3">
                <label for="name" class="form-label">
                    Name
                </label>

                <input id="name" type="text"
                       class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                       name="name"
                       value="{{ old('name') }}" required autofocus>

                @if ($errors->has('name'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                @endif
            </div>

            <div class="mb-3">
                <label for="blueprint" class="form-label">
                    Template
                </label>

                <select name="template_id" id="template_id"
                    class="form-control">
                    @foreach (\App\Template::all()->sortBy('name') as $template)
                    <option value="{{ $template->id }}"
                        {{ $template_id == $template->id ? 'selected' : '' }}>
                        {{ $template->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="web_access" class="form-label">
                    Enable Web Access
                </label>

                <div class="form-check">
                    <input id="web_access" type="checkbox"
                           class="form-check-input"
                           name="web_access"
                           value="{{ old('web_access') }}">
                </div>
            </div>

            <div class="mb-3">
                <label for="web_access_email" class="form-label">
                    Web Access User
                </label>

                <input id="web_access_email" type="email"
                       class="form-control{{ $errors->has('web_access_email') ? ' is-invalid' : '' }}"
                       name="web_access_email"
                       placeholder="me@example.com"
                       value="{{ old('web_access_email', $user->email) }}">

                @if ($errors->has('web_access_email'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('web_access_email') }}</strong>
                    </span>
                @endif
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-cogs"></i> Deploy
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

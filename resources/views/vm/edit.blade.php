@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">Machine</div>

    <div class="card-body">

        <form method="POST"
              action="{{ action("VMController@update", ["vm" => $vm]) }}">
            {{ method_field("PUT") }}
            {{ csrf_field() }}

            <div class="mb-3">
                <label for="name" class="col-md-3 col-form-label text-md-right">Name</label>

                <div class="col-md-8">
                    <input id="name" type="text"
                           class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                           name="name"
                           value="{{ old('name', $vm->name) }}" required autofocus>

                    @if ($errors->has('name'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="mb-3">
                <label for="user_id" class="col-md-3 col-form-label text-md-right">Owner</label>

                <div class="col-md-8">
                    <select name='user_id' id='user_id'
                            class="form-control{{ $errors->has('user_id') ? ' is-invalid' : '' }}">
                        @foreach (\App\User::orderBy('name')->get() as $u)
                        <option value='{{ $u->id }}'
                                {{ $u->id == $vm->user_id ? 'selected' : '' }}>
                            {{ $u->name }}
                        </option>
                        @endforeach
                    </select>

                    @if ($errors->has('user_id'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('user_id') }}</strong>
                        </span>
                    @endif
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

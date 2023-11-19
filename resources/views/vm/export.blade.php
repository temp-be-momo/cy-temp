@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">Export</div>

    <div class="card-body">
        <form method="POST" action="{{ action("VMController@doExport", ["vm" => $vm]) }}">

            {{ csrf_field() }}

            <div class="mb-3">
                <label for="name" class="form-label">Name</label>

                <input id="name" type="text"
                       class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                       name="name"
                       value="{{ old('name', $name) }}" required autofocus>

                @if ($errors->has('name'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                @endif
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>

                <textarea id="description"
                       class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}"
                       name="description" rows="4"
                       required>{{ old('description') }}</textarea>

                @if ($errors->has('description'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('description') }}</strong>
                    </span>
                @endif
            </div>

            <div class="mb-3">
        
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-file-export"></i> Export
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

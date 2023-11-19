@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">Image</div>

    <div class="card-body">
        <form method="POST" action="{{ action("ImageController@doImport") }}">

            {{ csrf_field() }}

            <div class="mb-3">
                <label for="url" class="form-label">URL</label>

                <input id="url" type="text"
                       class="form-control{{ $errors->has('url') ? ' is-invalid' : '' }}"
                       name="url"
                       value="{{ old('url') }}" required autofocus
                       autocomplete="off">

                @if ($errors->has('url'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('url') }}</strong>
                    </span>
                @endif
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                
                <input id="name" type="text"
                       class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                       name="name"
                       value="{{ old('name', $image->name) }}" required>

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
                       required>{{ old('description', $image->description) }}</textarea>

                @if ($errors->has('description'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('description') }}</strong>
                    </span>
                @endif
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-cloud-download-alt"></i> Import
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

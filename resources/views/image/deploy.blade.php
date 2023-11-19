@extends('layouts.app')

@section('content')
<h1>Deploy</h1>
<form method="POST" action="{{ action("ImageController@doDeploy", ["image" => $image]) }}">

    {{ csrf_field() }}

    <div class="mb-3">
        <label for="image" class="form-label">Image</label>

        <input id="image" type="text"
               class="form-control{{ $errors->has('url') ? ' is-invalid' : '' }}"
               name="image"
               disabled
               value="{{ $image->name }}">
    </div>

    <div class="mb-3">
        <label for="name" class="form-label">Name</label>

        <input id="name" type="text"
               class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
               name="name"
               value="{{ old('name', "") }}"
               required autofocus>

        @if ($errors->has('name'))
            <span class="invalid-feedback">
                <strong>{{ $errors->first('name') }}</strong>
            </span>
        @endif
    </div>

    <div class="mb-3">
        <label for="cpu_count" class="form-label">
            CPU count
        </label>

        <input id="cpu_count" type="number"
               min="1" max="32" step="1"
               class="form-control{{ $errors->has('cpu_count') ? ' is-invalid' : '' }}"
               name="cpu_count"
               value="{{ old('cpu_count', 1) }}" required>

        @if ($errors->has('cpu_count'))
            <span class="invalid-feedback">
                <strong>{{ $errors->first('cpu_count') }}</strong>
            </span>
        @endif
    </div>

    <div class="mb-3">
        <label for="memory" class="form-label">
            Memory [MB]
        </label>
        
        <input id="memory" type="number"
               min="1" max="32768" step="1"
               class="form-control{{ $errors->has('memory') ? ' is-invalid' : '' }}"
               name="memory"
               value="{{ old('memory', 256) }}" required>

        @if ($errors->has('memory'))
            <span class="invalid-feedback">
                <strong>{{ $errors->first('memory') }}</strong>
            </span>
        @endif
    </div>


    <div class="mb-3">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-cogs"></i> Deploy
        </button>
    </div>
</form>
@endsection


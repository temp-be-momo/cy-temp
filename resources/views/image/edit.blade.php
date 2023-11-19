@extends('layouts.app')

@section('content')

<h1 class="mb-3">
    {{ $image->name }}
</h1>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Image info</div>

            <div class="card-body">
                @if (!$image->exists)
                <form method="POST" action="{{ action("ImageController@store") }}">
                @else
                <form method="POST"
                      action="{{ action("ImageController@update", ["image" => $image]) }}">
                    {{ method_field("PUT") }}
                    @endif
                    {{ csrf_field() }}

                    <div class="mb-3">
                        <label for="name" class="col-md-3 col-form-label text-md-right">Name</label>

                        <div class="col-md-9">
                            <input id="name" type="text"
                                   class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                   name="name"
                                   value="{{ old('name', $image->name) }}" required autofocus>

                            @if ($errors->has('name'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="col-md-3 col-form-label text-md-right">Description</label>

                        <div class="col-md-9">
                            <textarea id="description"
                                      class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}"
                                      name="description" rows="10"
                                      required>{{ old('description', $image->description) }}</textarea>

                            @if ($errors->has('description'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('description') }}</strong>
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
    </div>


    <div class="col-md-4">

        @if ($image->exists())
        <div class="card">
            <div class="card-header">Screenshot</div>

            <div class="card-body">
                <form method="POST"
                      action="{{ action("ImageController@screenshot", ["image" => $image]) }}"
                      enctype="multipart/form-data">
                    {{ csrf_field() }}

                    <input class="form-control mb-3" type="file"
                           accept="image/png"
                           id="screenshot" name="screenshot">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Upload
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

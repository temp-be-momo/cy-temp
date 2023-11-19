@extends('layouts.app')

@section('content')
<h1>{{ $image->name }}</h1>

<div class="mb-3">
    <a class="btn btn-primary"
       href="{{ action('ImageController@deploy', ["image" => $image]) }}">
        <i class="fas fa-cogs"></i> Quick deploy
    </a>

    <a class="btn btn-primary"
       href="{{ $image->downloadURL() }}">
        <i class="fas fa-file-download"></i> Download
    </a>

    <a class="btn btn-primary"
       href="{{ action('ImageController@edit', ['image' => $image]) }}">
        <i class="fas fa-edit"></i> Edit
    </a>

    <form method="POST"
          action="{{ action('ImageController@destroy', ['image' => $image]) }}"
          style="display: inline-block">
        {{ csrf_field() }}
        {{ method_field("DELETE") }}
        <button class="btn btn-danger">
            <i class="fas fa-times-circle"></i> Delete
        </button>
    </form>
</div>
<div class="row">
    <div class="col-md-8">
        @if (!$image->exists())
        <p><span class="badge bg-warning">Not on disk!</span></p>
        @endif

        <p>Size: <b>{{ $image->sizeForHumans() }}B</b></p>
        <p>{!! nl2br($image->description) !!}</p>

        <p>Slug: <code>{{ $image->slug }}</code></p>


        <p>Hash: <code>{{ $image->hash }}</code></p>
        <p class="text-muted">Created by {{ $image->user->name }} on {{ $image->created_at }}</p>
    </div>
    
    <div class="col-md-4">

        @if (!is_null($image->screenshot))
        <a href="{{ Storage::url($image->screenshot) }}" rel="lightbox">
            <img src="{{ Storage::url($image->screenshot) }}"
                 class="img-fluid img-thumbnail">
        </a>
        @endif
    </div>
</div>
@endsection

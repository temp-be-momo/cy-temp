@extends('layouts.app')

@section('title', 'Images')

@section('content')
<h1>Images</h1>

<p>
    <a href="{{ action('ImageController@import') }}" class="btn btn-primary"
       title="Import from URL">
        <i class="fas fa-plus-circle"></i> Import
    </a>

    <a href="{{ action('ImageController@create') }}" class="btn btn-primary"
       title="Direct image upload">
        <i class="fas fa-upload"></i> Upload
    </a>

    <a href="https://cylab.be/blog/141/create-your-own-vm-image-for-the-cyber-range"
       class="btn btn-outline-primary"
       target="_blanck">
        <i class="fas fa-info-circle"></i> Create your images
    </a>
</p>


<table class="table table-lined">
    <tr>
        <th>Name</th>
        <th class="text-end">Size</th>
    </tr>
    @foreach($images as $image)
        <tr>
            <td>
                <a href="{{ action('ImageController@show', ['image' => $image]) }}"
                   class="text-decoration-none">
                    {{ $image->name }}
                </a>
            </td>
            <td class="text-end">
                @if ($image->exists())
                    {{ $image->sizeForHumans() }}B
                @else
                    <span class="badge bg-warning text-dark">Not on disk!</span>
                @endif
            </td>
        </tr>
    @endforeach
</table>

<p>
    <a href='{{ action('ImageController@importAlpine') }}'
       class='btn btn-sm btn-primary'>
           <i class="fas fa-download"></i> Import Alpine image
    </a>
</p>
@endsection

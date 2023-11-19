@extends('layouts.app')

@section('title', 'Templates')

@section('content')
<h1>Templates</h1>

<p>
    <a href="{{ action('TemplateController@create') }}" class="btn btn-primary">
        <i class="fas fa-plus-circle"></i> New
    </a>
</p>

<table class="table table-lined">
    <tr>
        <th>Name</th>
        <th></th>
    </tr>

    @foreach($templates as $template)
    <tr>
        <td>{{ $template->name }}</td>
        <td class="text-right">
            <a class="btn btn-primary btn-sm"
               href="{{ action('TemplateController@show', ['template' => $template]) }}">
                <i class="fas fa-search"></i> Show
            </a>

            <a class="btn btn-primary btn-sm"
               href="{{ action('TemplateController@edit', ['template' => $template]) }}">
                <i class="fas fa-edit"></i> Edit
            </a>

            <form method="POST"
                  action="{{ action('TemplateController@destroy', ['template' => $template]) }}"
                  style="display: inline-block">
                {{ csrf_field() }}
                {{ method_field("DELETE") }}
                <button class="btn btn-danger btn-sm">
                    <i class="fas fa-times-circle"></i> Delete
                </button>
            </form>
        </td>
    </tr>
    @endforeach
</table>
@endsection

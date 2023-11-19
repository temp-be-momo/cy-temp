@extends('layouts.app')

@section('content')

<h1 class='mb-3'>{{ $scenario->name }}</h1>

<div class="row justify-content-center">
    <div class="col-md-8">
        <pre><code class="language-yaml">{{ $scenario->yaml }}</code></pre>
    </div>

    <div class='col-md-4'>
        <div class='card'>
            <div class='card-body'>
                <div>
                    <a href="{{ action('ScenarioController@deploy', ["scenario" => $scenario]) }}"
                       class="btn btn-primary">
                        <i class="fas fa-cogs"></i> Deploy
                    </a>


                    <a class="btn btn-primary"
                       href="{{ action('ScenarioController@edit', ['scenario' => $scenario]) }}">
                         Edit
                    </a>

                    <form method="POST"
                          action="{{ action('ScenarioController@destroy',
                              ['scenario' => $scenario]) }}"
                          style="display: inline-block">
                        {{ csrf_field() }}
                        {{ method_field("DELETE") }}
                        <button class="btn btn-danger">
                             Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class='card mt-2'>
            <div class='card-header'>
                Validation errors
            </div>
            <div class='card-body'>
                <ul  class='pl-3'>
                    @foreach ($scenario->validate() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

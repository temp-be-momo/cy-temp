@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">{{ $template->name }}</div>

    <div class="card-body">
        <p>Image: {{ $template->image->name }}</p>
        <p>CPU's: {{ $template->cpu_count }}</p>
        <p>Memory: {{ $template->memory }}MB</p>
        <p>Boot delay: {{ $template->boot_delay }} seconds</p>
        <p>Configure guest: {{ $template->need_guest_config ? 'YES' : 'NO' }}</p>

        <p>Provisioning commands:</p>
        <code>
            <pre>{{ $template->provision }}</pre>
        </code>

        <p>Email note:</p>
        <code>
            <pre>{{ $template->email_note }}</pre>
        </code>

        <div>
            <a class="btn btn-primary"
               href="{{ action('TemplateController@edit', ['template' => $template]) }}">
                <i class="fas fa-edit"></i> Edit
            </a>

            <a class="btn btn-primary"
               href="{{ action('VMController@createFromTemplate', ['template' => $template]) }}">
                <i class="fas fa-cogs"></i> Deploy
            </a>

            <form method="POST"
                  action="{{ action('TemplateController@destroy', ['template' => $template]) }}"
                  style="display: inline-block">
                {{ csrf_field() }}
                {{ method_field("DELETE") }}
                <button class="btn btn-danger">
                    <i class="fas fa-times-circle"></i> Delete
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

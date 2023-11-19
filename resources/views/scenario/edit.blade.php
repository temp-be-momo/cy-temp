@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">Scenario</div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                @if (!$scenario->exists)
                <form method="POST" action="{{ action("ScenarioController@store") }}">
                @else
                <form method="POST"
                      action="{{ action("ScenarioController@update", ["scenario" => $scenario]) }}">
                {{ method_field("PUT") }}
                @endif
                    {{ csrf_field() }}

                    <div class="form-group">
                        <label for="name">Name</label>

                        <input id="name" type="text"
                               class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                               name="name"
                               value="{{ old('name', $scenario->name) }}" required autofocus
                               autocomplete="off">

                        @if ($errors->has('name'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="yaml">YAML</label>
                        <textarea
                            id="yaml"
                            name="yaml"
                            rows="30"
                            data-editor="yaml"
                            class="form-control{{ $errors->has('yaml') ? ' is-invalid' : '' }}"
                            >{{ old('yaml', $scenario->yaml) }}</textarea>

                        @if ($errors->has('yaml'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('yaml') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check"></i> Save
                        </button>
                    </div>
                </form>
            </div>

            <div class="col-md-4">
                <p>Available <b>images</b></p>
                <ul>
                    @foreach ($images as $image)
                    <li>{{ $image->slug }} : {{ $image->name }}</li>
                    @endforeach
                </ul>

                <p>Available <b>bridge interfaces</b></p>
                <ul>
                    <li>DEFAULT : default bridge interface</li>
                    @foreach ($interfaces as $if)
                    <li>{{ $if->name() }} [{{ $if->ip() }}]</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    // Hook up ACE editor to all textareas with data-editor attribute
    window.addEventListener('DOMContentLoaded', function() {
        $('textarea[data-editor]').each(function () {
            var textarea = $(this);
            var mode = textarea.data('editor');

            var editDiv = $('<div>', {
                position: 'absolute',
                width: textarea.width(),
                height: textarea.height(),
                'class': textarea.attr('class')
            }).insertBefore(textarea);

            textarea.css('visibility', 'hidden');
            textarea.css('height', 0);

            var editor = window.ace.edit(editDiv[0]);
            editor.setOptions({
                displayIndentGuides: true,
                showPrintMargin: false,
                showGutter: true,
                showLineNumbers: true
            });
            editor.getSession().setValue(textarea.val());
            editor.getSession().setMode("ace/mode/" + mode);

            // copy back to textarea on form submit...
            textarea.closest('form').submit(function () {
                textarea.val(editor.getSession().getValue());
            });
        });
    });
</script>
@endsection

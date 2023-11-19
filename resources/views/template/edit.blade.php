@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">Template</div>

    <div class="card-body">
        @if (!$template->exists)
        <form method="POST" action="{{ action("TemplateController@store") }}">
        @else
        <form method="POST"
              action="{{ action("TemplateController@update", ["template" => $template]) }}">
        {{ method_field("PUT") }}
        @endif
            {{ csrf_field() }}

            <div class="mb-3">
                <label for="name" class="col-md-3 col-form-label text-md-right">Name</label>

                <div class="col-md-8">
                    <input id="name" type="text"
                           class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                           name="name"
                           value="{{ old('name', $template->name) }}" required autofocus>

                    @if ($errors->has('name'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="mb-3">
                <label for="image" class="col-md-3 col-form-label text-md-right">Image</label>

                <div class="col-md-8">

                    <select name="image_id" id="image_id"
                            class="form-control{{ $errors->has('image') ? ' is-invalid' : '' }}">
                        @foreach (\App\Image::orderBy('name')->get() as $image)
                        <option value="{{ $image->id  }}"
                                {{ old('image_id', $template->image_id) == $image->id ? 'selected' : '' }} >{{ $image->name }}</option>
                        @endforeach
                    </select>


                    @if ($errors->has('image'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('image') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="mb-3">
                <label for="cpu_count" class="col-md-3 col-form-label text-md-right">CPU's</label>

                <div class="col-md-2">
                    <input id="cpu_count" type="number"
                           class="form-control{{ $errors->has('cpu_count') ? ' is-invalid' : '' }}"
                           name="cpu_count"
                           value="{{ old('cpu_count', $template->cpu_count) }}" required>

                    @if ($errors->has('cpu_count'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('cpu_count') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="mb-3">
                <label for="memory" class="col-md-3 col-form-label text-md-right">Memory [MB]</label>

                <div class="col-md-2">
                    <input id="memory" type="number"
                           class="form-control{{ $errors->has('memory') ? ' is-invalid' : '' }}"
                           name="memory"
                           value="{{ old('memory', $template->memory) }}" required >

                    @if ($errors->has('memory'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('memory') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="mb-3">
                <label for="boot_delay" class="col-md-3 col-form-label text-md-right">Boot delay [seconds]</label>

                <div class="col-md-2">
                    <input id="boot_delay" type="number"
                           class="form-control{{ $errors->has('boot_delay') ? ' is-invalid' : '' }}"
                           name="boot_delay"
                           value="{{ old('boot_delay', $template->boot_delay) }}" required >

                    @if ($errors->has('boot_delay'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('boot_delay') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="mb-3">
                <label for="need_guest_config" class="col-md-3 col-form-label text-md-right">Configure guest</label>

                <div class="col-md-2">
                    <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="need_guest_config" name="need_guest_config"
                           {{ $template->need_guest_config ? 'checked' : '' }}>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="provision" class="col-md-3 col-form-label text-md-right">Provision commands</label>

                <div class="col-md-8">
                    <textarea id="provision" type="text"
                              rows="10"
                           class="form-control{{ $errors->has('provision') ? ' is-invalid' : '' }}"
                           name="provision"
                           >{{ old('provision', $template->provision) }}</textarea>

                    @if ($errors->has('provision'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('provision') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="mb-3">
                <label for="email_note" class="col-md-3 col-form-label text-md-right">Email note</label>

                <div class="col-md-8">
                    <textarea id="email_note" type="text"
                              rows="10"
                           class="form-control{{ $errors->has('email_note') ? ' is-invalid' : '' }}"
                           name="email_note"
                           placeholder="This message will be appended to the email sent to the user..."
                           >{{ old('email_note', $template->email_note) }}</textarea>

                    @if ($errors->has('email_note'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('email_note') }}</strong>
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
@endsection

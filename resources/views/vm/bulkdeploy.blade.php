@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">Bulk Deploy</div>

    <div class="card-body">
        <form method="POST" action="{{ action("VMController@bulkStore") }}">

            {{ csrf_field() }}

            <div class="mb-3">
                <label for="name" class="form-label">
                    Name
                </label>

                <input id="name" type="text"
                       class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                       name="name"
                       value="{{ old('name') }}" required autofocus>

                @if ($errors->has('name'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                @endif
            </div>

            <div class="mb-3">
                <label for="blueprint" class="form-label">
                    Template
                </label>

                <select name="template_id" id="template_id"
                    class="form-control">
                    @foreach (\App\Template::all()->sortBy('name') as $template)
                    <option value="{{ $template->id }}">{{ $template->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="emails" class="form-label">
                    Emails
                </label>

                <textarea id="emails"
                    rows="10"
                    class="form-control"
                    placeholder="One email address per line..."
                    name="emails">{{ old('emails') }}</textarea>
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-cogs"></i> Deploy
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

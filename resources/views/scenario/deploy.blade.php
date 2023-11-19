@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">Deploy scenario</div>

    <div class="card-body">
        <form method="POST"
              action="{{ action("ScenarioController@doDeploy", ["scenario" => $scenario]) }}">
            {{ csrf_field() }}

            <div class="mb-3">
                <label for="name" class="col-md-3 col-form-label text-md-right">
                    Scenario
                </label>

                <div class="col-md-8">
                    <input type="text"
                           disabled
                           class="form-control"
                           value="{{ $scenario->name }}">
                </div>
            </div>

            <div class="mb-3">
                <label for="name" class="col-md-3 col-form-label text-md-right">Name</label>

                <div class="col-md-8">
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
            </div>

            <div class="mb-3">
                <label for="yaml" class="col-md-3 col-form-label text-md-right">
                    Participants
                </label>

                <div class="col-md-8">
                    <textarea
                        id="participants"
                        name="participants"
                        rows="10"
                        placeholder="Email of participants, one per line..."
                        class="form-control{{ $errors->has('participants') ? ' is-invalid' : '' }}"
                        >{{ old('participants') }}</textarea>

                    @if ($errors->has('participants'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('participants') }}</strong>
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

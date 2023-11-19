@extends('layouts.app')

@section('content')
<form method="POST" action="{{ action("VBoxVMController@update", ["uuid" => $vboxvm->getUUID()]) }}">

    {{ method_field('PUT') }}
    {{ csrf_field() }}

    <div class="card my-3">
        <div class="card-header">VM</div>

        <div class="card-body">

            <div class="mb-3">
                <label for="cpu_count" class="form-label">
                    vCPU
                </label>

                
                <input id="cpu_count" type="number"
                       min='1' max='32' step="1"
                       class="form-control{{ $errors->has('cpu_count') ? ' is-invalid' : '' }}"
                       name="cpu_count"
                       value="{{ old('cpu_count', $vboxvm->getCPUCount()) }}" required>

                @if ($errors->has('cpu_count'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('cpu_count') }}</strong>
                    </span>
                @endif
            </div>

            <div class="mb-3">
                <label for="cpu_cap" class="form-label">
                    CPU cap [%]
                </label>

                <input id="cpu_cap" type="number"
                       min='1' max='100' step="1"
                       class="form-control{{ $errors->has('cpu_cap') ? ' is-invalid' : '' }}"
                       name="cpu_cap"
                       value="{{ old('cpu_cap', $vboxvm->getCPUCap()) }}" required>

                @if ($errors->has('cpu_cap'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('cpu_cap') }}</strong>
                    </span>
                @endif
            </div>

            <div class="mb-3">
                <label for="memory" class="form-label">
                    Memory [MB]
                </label>

                <input id="memory" type="number"
                       min='64' max='131072' step="1"
                       class="form-control{{ $errors->has('memory') ? ' is-invalid' : '' }}"
                       name="memory"
                       value="{{ old('memory', $vboxvm->getMemorySize()) }}" required>

                @if ($errors->has('memory'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('memory') }}</strong>
                    </span>
                @endif
            </div>

        </div>
    </div>

    <div class="mb-3">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Save
        </button>
    </div>
</form>

@endsection

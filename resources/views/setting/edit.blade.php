@extends('layouts.app')

@section('content')
<h1>Settings</h1>
<form method="POST"
      action="{{ action("SettingController@update") }}">
    {{ method_field("PUT") }}
    {{ csrf_field() }}

    @php
    use \App\Setting;
    // net_bridge_default
    @endphp
    <div class="mb-3">
        <label for="name" class="form-label">
            Default bridge interface
        </label>

        <select id="net_bridge_default"
                class="form-select"
                name="net_bridge_default">

            <option value='--'>--</option>

             @foreach ($host_interfaces as $interface)
             <option value="{{ $interface->name() }}" {{ (Setting::defaultBridgeInterface() == $interface->name()) ? 'selected' : '' }}>{{ $interface->name() }}</option>
             @endforeach
        </select>

        @if ($errors->has('net_bridge_default'))
            <span class="invalid-feedback">
                <strong>{{ $errors->first('net_bridge_default') }}</strong>
            </span>
        @endif
    </div>

    <div class="mb-3">
        <button type="submit" class="btn btn-primary">
             Save
        </button>
    </div>
</form>
@endsection

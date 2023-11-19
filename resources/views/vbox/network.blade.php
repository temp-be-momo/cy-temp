@extends('layouts.app')

@section('content')

<form method="POST" action="{{ action("VBoxVMController@updateNetwork", [
    "uuid" => $vboxvm->getUUID(),
    "slot" => $slot]) }}">

    {{ method_field('PUT') }}
    {{ csrf_field() }}

    <div class="card my-3">
        <div class="card-header">
            Slot <b>{{ $slot }}</b>
        </div>

        <div class="card-body">
            <div class="mb-3 form-check">
                <input class="form-check-input" type="checkbox"
                    value='1'
                    {{ $interface->isEnabled() ? 'checked' : '' }}
                    name="enabled"
                    id="enabled">
                
                <label for="enabled" class="form-check-label">
                    Enabled
                </label>    
            </div>
            
            <div class="mb-3">
                <label for="mode" class="form-label">
                    Mode:
                </label>

                <select id="mode"
                       class="form-select"
                       name="mode">

                    <option value="Bridged" {{ ($interface->getAttachmentType() == 'Bridged') ? 'selected' : '' }}>
                        Bridged
                    </option>
                    
                    <option value="Internal" {{ ($interface->getAttachmentType() == 'Internal') ? 'selected' : '' }}>
                        Internal
                    </option>
                </select>
            </div>

            <div class="mb-3">
                <label for="bridge" class="form-label">
                    Bridge interface:
                </label>

                
                <select id="bridge"
                       class="form-select"
                       name="bridge">

                    @foreach ($host_interfaces as $bridge)
                    <option value="{{ $bridge->name() }}" {{ ($interface->getBridgedInterface() == $bridge->name()) ? 'selected' : '' }}>
                        {{ $bridge->name() }} [{{ $bridge->status() }} - {{ $bridge->ip() }}]
                    </option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-3">
                <label for="internal" class="form-label">
                    Internal network:
                </label>

                
                <select id="internal"
                       class="form-select"
                       name="internal">

                    @foreach ($internal_networks as $network)
                    <option value="{{ $network->name() }}" {{ ($interface->getInternalNetwork() == $network->name()) ? 'selected' : '' }}>
                        {{ $network->network() }}
                    </option>
                    @endforeach
                </select>
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

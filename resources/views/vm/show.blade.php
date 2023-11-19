@extends('layouts.app')

@section('title', $vm->getName())

@section('content')
<h1>{{ $vm->getName() }}</h1>

@if (Auth::user()->isAdmin())
<div class="mb-2">

    @if ($vboxvm->isRunning())
    <a class="btn btn-primary my-2"
       href="{{ action('VBoxVMController@reset', ['uuid' => $vm->getUUID()]) }}">
        <i class="fas fa-undo"></i> Reset
    </a>
    <a class="btn btn-primary my-2"
       href="{{ action('VBoxVMController@halt', ['uuid' => $vm->getUUID()]) }}">
        <i class="fas fa-stop"></i> Halt
    </a>
    <a class="btn btn-primary my-2"
       href="{{ action('VBoxVMController@kill', ['uuid' => $vm->getUUID()]) }}">
        <i class="fas fa-power-off"></i> Kill
    </a>
    @else
    <a class="btn btn-primary my-2"
       href="{{ action('VBoxVMController@up', ['uuid' => $vm->getUUID()]) }}">
        <i class="fas fa-play"></i> Run
    </a>
    @endif
    
    
    <a class="btn btn-primary"
       href="{{ action('VMController@export', ['vm' => $vm]) }}">
        <i class="fas fa-file-export"></i> Export
    </a>


    <a class="btn btn-primary my-2"
        href='{{ action('VMController@log', ['vm' => $vm]) }}'>
           <i class="fas fa-file-alt"></i> Deploy log
    </a>

    <form method="POST"
          action="{{ action('VMController@destroy', ['vm' => $vm]) }}"
          style="display: inline-block">
        {{ csrf_field() }}
        {{ method_field("DELETE") }}
        <button class="btn btn-danger my-2" data-type="VM">
            <i class="fas fa-times-circle"></i> Destroy
        </button>
    </form>

    <form method="POST"
          action="{{ action('VMController@unmanage', ['vm' => $vm]) }}"
          style="display: inline-block">
        {{ csrf_field() }}
        {{ method_field("PUT") }}
        <button class="btn btn-warning my-2" data-type="VM">
            <i class="fas fa-times-circle"></i> Unmanage
        </button>
    </form>
</div>
@endif

<div class='row'>
    <div class='col-md-6'>

        <div class="card my-3">
            <div class="card-body">
                <p>Name: <b>{{ $vm->name }}</b></p>
                <p>UUID: <b>{{ $vm->uuid }}</b></p>
                <p>Owner: <b>{{ $vm->user->name }}</b></p>

                @if (Auth::user()->isAdmin())
                <p>
                    <a class="btn btn-primary"
                       href="{{ action('VMController@edit', ['vm' => $vm]) }}">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                </p>
                @endif
            </div>
        </div>

        <div class="card my-3">
            <div class="card-header">Guacamole</div>
            <div class="card-body">
                <p>
                    <a href="{{ route('guacamole') }}" class="btn btn-primary"
                       target="_blanck">
                        <i class="fas fa-desktop"></i> Open Guacamole
                    </a>
                </p>

                <p>
                    RDP: {{ $vboxvm->getVRDEServer()->isEnabled() ? 'enabled' : 'disabled' }}
                    ({{ $vboxvm->getVRDEServer()->getBindAddress() }}:{{ $vboxvm->getVRDEServer()->getPort() }})
                </p>

                <p>
                    User: <b>{{ optional($vm->guacamole())->name }}</b>
                </p>


                @if (Auth::user()->isAdmin())
                <form action="{{ action('VMController@guacamole', ["vm" => $vm]) }}"
                      method="post"
                      class="my-3">

                    {{ csrf_field() }}
                    
                    <div class="row">
                        <div class="col-auto">
                            <label for="guac_user" class="col-form-label">Assign to user:</label>
                        </div>
                        <div class="col-auto">
                            <select name="guac_user"
                                    class="form-select mx-1">
                                @foreach ($guac_users as $guac_user)
                                <option value="{{ $guac_user->user_id }}">
                                    {{ $guac_user->email_address }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="submit"
                                    class="form-control btn btn-primary">Assign</button>
                        </div>
                    </div>
                </form>
                @endif
            </div>
        </div>

        <div class="card my-3">
            <div class="card-header">
                VirtualBox
            </div>
            <div class="card-body">
                <p>Name: {{ $vboxvm->getName() }}</p>
                <p>Group: {{ $vboxvm->getGroups()[0] }}</p>
                <p>UUID: {{ $vboxvm->getUUID() }}</p>
                <p>
                    Guest additions:
                    @if ($vboxvm->getState() == "Running")
                    {{ $vboxvm->getGuestAdditionsVersion() }}
                    @endif
                </p>
                <p>vCores: {{ $vboxvm->getCPUCount() }}</p>
                <p>CPU cap: {{ $vboxvm->getCPUCap() }}%</p>
                <p>Memory: {{ $vboxvm->getMemorySize() }} MB</p>
                <p>{!! $vm->stateBadge() !!}</p>

                @if (Auth::user()->isAdmin())
                <p>
                    <a class="btn btn-primary my-2"
                       href="{{ action('VBoxVMController@edit', ['uuid' => $vm->getUUID()]) }}">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                </p>
                @endif
            </div>
        </div>

        <h4 class='mt-5'>Drives</h4>

        @foreach ($vboxvm->getMediumAttachments() as $attachment)
        <div class='card my-3'>
            <div class='card-header'>
                {{ $attachment->getController() }} {{ $attachment->getType() }}
            </div>
            <div class='card-body'>
                <p>Device: {{ $attachment->getDevice() }}</p>
                <p>Port: {{ $attachment->getPort() }}</p>
                <p>Has medium: {{ $attachment->hasMedium() ? "Yes" : "No" }}</p>
                @if ($attachment->hasMedium())
                <p>Size: {{ round($attachment->getMedium()->getSize() / 1E9, 2) }} GB</p>
                @endif
            </div>
        </div>
        @endforeach

        <h4 class="mt-5">Storage controllers</h4>

        @foreach ($vboxvm->getStorageControllers() as $c)
        <div class='card my-3'>
            <div class='card-body'>
            <p>Storage controller: {{ $c->getName() }}</p>
            <p>Type: {{ $c->getControllerType() }}</p>
            <p>Host IO Cache: {{ $c->getUseHostIOCache() ? 'Yes' : 'No' }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <div class='col-md-6'>
        <div class="card my-3">
            <div class="card-body text-center">
                <img class="img-fluid" id="thumbnail"
                     src="{{ action('VMController@thumbnail', ['vm' => $vm]) }}?{{ time() }}">
            </div>
        </div>

        <h4 class="mt-5">Network interfaces</h4>

        @for($i = 0; $i < 8; $i++)
        @php
        $a = $vboxvm->getNetworkAdapter($i);
        @endphp
        <div class="card my-3">
            <div class='card-header'>
                Slot <b>{{ $i }}</b>
            </div>
            <div class="card-body">
                @if ($a->isEnabled())
                <p>{{ $a->getAttachmentType() }} on {{ $a->getNetworkName() }}</p>
                <p>IP: {{ $a->getIPAddress() }}</p>
                <p>MAC: {{ $a->getMACAddress() }}</p>
                @else
                <p>Disabled</p>
                @endif

                @if (Auth::user()->isAdmin())
                <p>
                    <a class="btn btn-primary my-2"
                       href="{{ action('VBoxVMController@editNetwork', [
                           'uuid' => $vm->getUUID(),
                            'slot' => $i]) }}">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                </p>
                @endif
            </div>
        </div>
        @endfor
    </div>
</div>

<script type="text/javascript">
    thumbnail_url = "{{ action('VMController@thumbnail', ['vm' => $vm]) }}?";
    setInterval(function() {
        console.log('reload vm thumbnail ' + thumbnail_url + ' ...');
        d = new Date();
        $("#thumbnail").attr(
                "src",
                thumbnail_url + d.getTime());
    }, 5000);
</script>

@endsection

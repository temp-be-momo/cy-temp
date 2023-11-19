@php
$show_owner = $show_owner ?? true;
@endphp
<table class="table table-lined">
    <thead>
    <tr>
        <th>Name</th>
        <th>State</th>
        @if ($show_owner)
        <th>Owner</th>
        @endif
        <th>vCores</th>
        <th>Memory</th>
        <th>Storage</th>
        <th class="text-end">IP</th>
    </tr>
    </thead>

    <tbody id="vms">
    @foreach($vms as $vm)
    @if (!$vm->hasVBoxVM())
        @continue
    @endif
    <tr>
        <td>
            <a class="text-decoration-none"
               href="{{ action('VMController@show', ['vm' => $vm]) }}">
                {{ $vm->getName() }}
            </a>
        </td>
        <td>{!! $vm->stateBadge() !!}</td>
        @if ($show_owner)
        <td>{{ $vm->user->name }}</td>
        @endif
        <td>{{ $vm->getVBoxVM()->getCPUCount() }}</td>
        <td>{{ $vm->getVBoxVM()->getMemorySize() / 1024 }}GB</td>
        <td>{{ round($vm->totalStorageSize()/1E9, 1) }}GB</td>
        <td class="text-end">
            {{ $vm->getVBoxVM()->getNetworkAdapter(0)->getIPAddress() }}
        </td>
    </tr>
    @endforeach
    </tbody>
</table>

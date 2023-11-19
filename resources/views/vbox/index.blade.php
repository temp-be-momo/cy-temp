@extends('layouts.app')

@section('title', "Virtual Machines")

@section('content')
<h1>VirtualBox Machines</h1>

<p>
    <input type="text" class="form-control d-inline col-md-2" name="search" id="search"
           placeholder="Search...">
</p>

<table class="table table-lined">
    <thead>
    <tr>
        <th>Name</th>
        <th></th>
        <th></th>
    </tr>
    </thead>

    <tbody id="vms">
    @foreach($vms as $vm)
    @if (\App\VM::exists($vm->getUUID()))
        @continue
    @endif
    <tr>
        <td>{{ $vm->getName() }}</td>
        <td>{{ $vm->getState() }}</td>
        <td class="text-right">
            <a href="{{ action('VBoxVMController@assign', ['uuid' => $vm->getUUID()]) }}"
               class="btn btn-primary btn-sm">
                   <i class="fas fa-user-tag"></i> Manage
            </a>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>

<script>
window.addEventListener('load', function() {
  $("#search").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#vms tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});
</script>
@endsection

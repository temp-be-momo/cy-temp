@extends('layouts.app')

@section('title', "Machines")

@section('content')
<h1>All machines</h1>
<p>
    <input type="text" class="form-control d-inline my-2" name="search" id="search"
           style='min-width: 100px; max-width: 300px;'
           placeholder="Search...">

    @if (Auth::user()->isAdmin())
    <a href="{{ action('VMController@create') }}" class="btn btn-primary">
        <i class="fas fa-plus-circle"></i> Deploy
    </a>

    <a href="{{ action('VMController@bulkCreate') }}" class="btn btn-primary">
        <i class="fas fa-plus-circle"></i> Bulk deploy
    </a>

    <a href='{{ action('VBoxVMController@haltAll') }}' class='btn btn-warning'>
        <i class="fas fa-stop"></i> Stop all
    </a>

    <a href='{{ action('VBoxVMController@upAll') }}' class='btn btn-success'>
        <i class="fas fa-play"></i> Run all
    </a>
    @endif
    
    <a href="{{ route('guacamole') }}" class="btn btn-primary"
       target="_BLANCK">
        <i class="fas fa-desktop"></i> Open Guacamole
    </a>
</p>

<div class='row'>
    <div class='col-sm-3'>
        <div class='card text-center my-2'>
            <div class='card-header'>
                Machines
            </div>
            <div class='card-body'>
                {{ $status->vm_count_running }} / {{ $status->vm_count }}
            </div>
        </div>
    </div>

    <div class='col-sm-3'>
        <div class='card text-center my-2'>
            <div class='card-header'>vCores</div>
            <div class='card-body'>
                {{ $status->vcpu_count_running }} / {{ $status->vcpu_count }}
            </div>
        </div>
    </div>

    <div class='col-sm-3'>
        <div class='card text-center my-2'>
            <div class='card-header'>Memory</div>
            <div class='card-body'>
                {{ round($status->memory_running/1024) }}GB / {{ round($status->memory/1024) }}GB
            </div>
        </div>
    </div>

    <div class='col-sm-3'>
        <div class='card text-center my-2'>
            <div class='card-header'>Storage</div>
            <div class='card-body'>
                {{ round($status->disk_size/1E9) }}GB
            </div>
        </div>
    </div>
</div>

@include('vm.table', ['vms' => $vms])

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
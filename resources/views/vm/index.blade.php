@extends('layouts.app')

@section('title', "Machines")

@section('content')
<h1>My machines</h1>
<p>
    <input type="text" class="form-control d-inline my-2" name="search" id="search"
           style='min-width: 100px; max-width: 300px;'
           placeholder="Search...">

    <a href="{{ route('guacamole') }}" class="btn btn-primary"
       target="_BLANCK">
        <i class="fas fa-desktop"></i> Open Guacamole
    </a>
</p>

<div class='row mb-3'>
    <div class='col-lg'>
        <div class='card text-center h-100'>
            <div class='card-header'>
                Machines
            </div>
            <div class='card-body p-3'>
                {{ $status->vm_count_running }} / {{ $status->vm_count }}
            </div>
        </div>
    </div>

    <div class='col-lg'>
        <div class='card text-center h-100'>
            <div class='card-header'>vCores</div>
            <div class='card-body p-3'>
                {{ $status->vcpu_count_running }} / {{ $status->vcpu_count }}
            </div>
        </div>
    </div>

    <div class='col-lg'>
        <div class='card text-center h-100'>
            <div class='card-header'>Memory</div>
            <div class='card-body p-3'>
                {{ round($status->memory_running/1024) }}GB / {{ round($status->memory/1024) }}GB
            </div>
        </div>
    </div>

    <div class='col-lg'>
        <div class='card text-center h-100'>
            <div class='card-header'>Storage</div>
            <div class='card-body p-3'>
                {{ round($status->disk_size/1E9) }}GB
            </div>
        </div>
    </div>
    
    <div class='col-lg'>
        <div class='card text-center h-100'>
            <div class='card-header'>
                <i class="fas fa-leaf text-green"></i> CO<sub>2</sub>
            </div>
            <div class='card-body p-3'>
                {{ round($status->monthlyCO2()/1000, 1) }}kg/month
                
                <p class="card-text mb-0">
                    <small class="text-muted">
                        <a class="text-decoration-none" href="{{ action('VMController@co2') }}">Details</a>
                    </small>
                </p>
            </div>
        </div>
    </div>
</div>

@include('vm.table', ["show_owner" => false])

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
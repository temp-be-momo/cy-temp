@extends('layouts.app')

@php
use \App\Status;
@endphp

@section('content')

<p>
    <a href='{{ action('StatusController@dashboard') }}'
       class='btn btn-primary'>
        <i class="fas fa-chart-line"></i> Dashboard
    </a>
</p>

<div class="row row-cols-1 row-cols-md-3 g-4">
    <div class="col">
        <div class="card h-100 mb-4">
            <div class="card-header">
                Cyrange
            </div>

            <div class="card-body">
                <p>Version: {{ Status::releaseTag() }}</p>
                <p>
                    Latest: {{ Status::latestTag() }}

                    <a href="https://cylab.be/blog/132/installing-the-cyrange-cyber-range-platform#upgrade"
                       class="btn btn-sm btn-outline-primary"
                       target="_blanck">
                        <i class="fas fa-info-circle"></i> Upgrade instructions
                    </a>
                </p>

                <p>Virtual Machines : {{ $status->vm_count }}</p>
                <canvas id="chart_vms"></canvas>
            </div>
        </div>
    </div>
    
    <div class='col'>
        <div class="card h-100 mb-4">
            <div class="card-header">
                Guacamole

                @if (is_null($status->web_accounts))
                <span class="badge badge-warning">Unreachable!</span>
                @endif
            </div>
            <div class="card-body">
                <p>
                    {{ env('GUACAMOLE_USERNAME') . "@" . env('GUACAMOLE_HOST') . ":3306" }}
                </p>
                <p>
                    Active Web Users :
                    @if (is_null($status->web_accounts))
                    ?
                    @else
                    {{ $status->web_accounts_active }} / {{ $status->web_accounts }}
                    @endif
                </p>

                <canvas id="chart_users"></canvas>

            </div>
        </div>
    </div>
    
    <div class='col'>
        <div class="card h-100 mb-4">
            <div class="card-header">
                VirtualBox


                @if (is_null($status->vbox_version))
                <span class="badge badge-warning">Unreachable!</span>
                @endif
            </div>

            <div class="card-body">
                <p>
                    {{ config('vbox.user') . "@" . config('vbox.host') . ":18083" }}
                </p>

                <p>
                   Version: {{ $status->vbox_version ?: "?" }}
                </p>

                <p>Load : {{ $status->cpu_load }} / {{ 2 * $status->cpu_count }}</p>
                <canvas id="chart_cpu"></canvas>

                <p>Used Memory : {{ round($status->memory_used/1000) }}
                    / {{ round($status->memory_total/1000) }} GB</p>


                <canvas id="chart_memory"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.bundle.min.js"
   integrity="sha512-SuxO9djzjML6b9w9/I07IWnLnQhgyYVSpHZx0JV97kGBfTIsUYlWflyuW4ypnvhBrslz1yJ3R+S14fdCWmSmSA=="
   crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-annotation/0.5.7/chartjs-plugin-annotation.min.js"
   integrity="sha512-9hzM/Gfa9KP1hSBlq3/zyNF/dfbcjAYwUTBWYX+xi8fzfAPHL3ILwS1ci0CTVeuXTGkRAWgRMZZwtSNV7P+nfw=="
   crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="text/javascript">
window.addEventListener('load', function() {
    window.chartColors = {
            red: 'rgb(255, 99, 132)',
            orange: 'rgb(255, 159, 64)',
            yellow: 'rgb(255, 205, 86)',
            green: 'rgb(75, 192, 192)',
            blue: 'rgb(54, 162, 235)',
            purple: 'rgb(153, 102, 255)',
            grey: 'rgb(201, 203, 207)'
    };

    var common_options = {
        aspectRatio: 1.6,
        legend: {
            display: false,
        },
        scales: {
            xAxes: [{
                type: 'time',
            }],
            yAxes: [{
                ticks: {
                    beginAtZero: true
                }
            }]
        }
    };

    var vms_points = {!! json_encode(\App\Status::weeklyVMs()) !!};
    var vms_canvas = document.getElementById('chart_vms').getContext('2d');
    var vms_chart = new Chart(vms_canvas, {
        type: 'line',
        data: {
            datasets: [
                {
                    label: 'VM\'s',
                    backgroundColor: window.chartColors.red,
                    borderColor: window.chartColors.red,
                    fill: false,
                    data: vms_points
                }]
        },
        options: common_options
    });

    var users_points = {!! json_encode(\App\Status::weeklyUsers()) !!};
    var users_canvas = document.getElementById('chart_users').getContext('2d');
    var users_chart = new Chart(users_canvas, {
        type: 'line',
        data: {
            datasets: [
                {
                    label: 'Active Web Users',
                    backgroundColor: window.chartColors.red,
                    borderColor: window.chartColors.red,
                    fill: false,
                    data: users_points
                }]
        },
        options: common_options
    });

    var cpu_points = {!! json_encode(\App\Status::weeklyCpuLoad()) !!};
    var cpu_canvas = document.getElementById('chart_cpu').getContext('2d');
    var cpu_chart = new Chart(cpu_canvas, {
        type: 'line',
        data: {
            datasets: [
                {
                    label: 'CPU Load',
                    backgroundColor: window.chartColors.red,
                    borderColor: window.chartColors.red,
                    fill: false,
                    data: cpu_points
                }]
        },
        options: {
            aspectRatio: 1.6,
            legend: {
                display: false
            },
            scales: {
                xAxes: [{
                    type: 'time',
                    display: true,
                    scaleLabel: {
                            display: false,
                            labelString: 'Time'
                    }
                }],
                yAxes: [{
                    id: 'load',
                    ticks: {
                        beginAtZero:true
                    },
                    scaleLabel: {
                        display: false,
                    }
                }]
            },
            annotation: {
                annotations: [
                    {
                        // !! scaleID is required !!
                        scaleID: 'load',
                        drawTime: 'afterDraw',
                        type: 'line',
                        mode: 'horizontal',
                        value: {{ 2 * $status->cpu_count }},
                        borderColor: window.chartColors.red,
                        borderWidth: 2
                    },
                ]
            }
        },
    });

    var memory_points = {!! json_encode(\App\Status::weeklyMemory()) !!};
    var memory_canvas = document.getElementById('chart_memory').getContext('2d');
    var memory_chart = new Chart(memory_canvas, {
        type: 'line',
        data: {
            datasets: [
                {
                    label: 'Used memory',
                    backgroundColor: window.chartColors.red,
                    borderColor: window.chartColors.red,
                    fill: false,
                    data: memory_points
                }]
        },
        options: {
            aspectRatio: 1.6,
            legend: {
                display: false
            },
            scales: {
                xAxes: [{
                    type: 'time',
                    display: true,
                    scaleLabel: {
                            display: false,
                            labelString: 'Time'
                    }
                }],
                yAxes: [{
                    id: 'memory',
                    ticks: {
                        beginAtZero:true
                    },
                    scaleLabel: {
                        display: false,
                        labelString: 'Used memory [MB]'
                    }
                }]
            },
            annotation: {
                annotations: [
                    {
                        // !! scaleID is required !!
                        scaleID: 'memory',
                        drawTime: 'afterDraw',
                        type: 'line',
                        mode: 'horizontal',
                        value: {{ round($status->memory_total/1000) }},
                        borderColor: window.chartColors.red,
                        borderWidth: 2
                    },
                ]
            }
        },
    });
});
</script>

<div class="text-muted bottom-right">
    Reload in <span id="reload-countdown">60</span> seconds
</div>

<script type="text/javascript">
    var reload_countdown = 60;
    setInterval(function() {
        reload_countdown -= 1;
        $('#reload-countdown').text(reload_countdown);

        if (reload_countdown === 0) {
            console.log('reload...');
            location.reload();
        }
    }, 1000);
</script>
@endsection

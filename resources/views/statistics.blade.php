@extends('layouts.dashboard')

@section('content')
<div class="container-fluid bg-dark text-white h-100 pt-2">

    <h1 class="mt-1 mb-5 text-center">
        {{ config('app.name') }}
        <span style="font-size: 80%">
            [{{ config('app.url') }}]
        </span>
    </h1>

    <div class="row mb-4">
        <div class="col-md-6">
            <h4>Virtual Machines : {{ $status->vm_count }}</h4>
            <canvas id="chart_vms"></canvas>
        </div>

        <div class="col-md-6">
            <h4>Active Web Users : {{ $status->web_accounts_active }} / {{ $status->web_accounts }}</h4>
            <canvas id="chart_users"></canvas>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <h4>CPU Load : {{ $status->cpu_load }} / {{ 2 * $status->cpu_count }}</h4>
            <canvas id="chart_cpu"></canvas>
        </div>

        <div class="col-md-6">
            <h4>Used Memory : {{ round($status->memory_used/1000) }}
                / {{ round($status->memory_total/1000) }} GB</h4>
            <canvas id="chart_memory"></canvas>
        </div>
    </div>
</div>


<script type="text/javascript">
window.addEventListener('load', function() {
    window.chartColors = {
            red: 'rgb(255, 99, 132)',
            blue: 'rgb(54, 162, 235)',
            grey: 'rgb(201, 203, 207)'
    };

    var common_options = {
        aspectRatio: 3.0,
        animation: false,
        legend: {
            display: false,
        },
        scales: {
            xAxes: [{
                type: 'time',
                gridLines: {
                    color: 'rgba(255, 255, 255, 0.2)'
                },
                ticks: {
                    fontColor: 'white'
                }
            }],
            yAxes: [{
                // yAxes id is required to link annotation (horizontal line)
                id: 'main',
                gridLines: {
                    color: 'rgba(255, 255, 255, 0.2)'
                },
                ticks: {
                    beginAtZero: true,
                    fontColor: 'white'
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
                    borderColor: window.chartColors.blue,
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
                    borderColor: window.chartColors.blue,
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
                    borderColor: window.chartColors.blue,
                    fill: false,
                    data: cpu_points
                }]
        },
        options: {
            ...common_options,
            ...{
                annotation: {
                    annotations: [
                        {
                            // !! scaleID is required !!
                            scaleID: 'main',
                            drawTime: 'afterDraw',
                            type: 'line',
                            mode: 'horizontal',
                            value: {{ 2 * $status->cpu_count }},
                            borderColor: window.chartColors.red,
                            borderWidth: 2
                        },
                    ]
                }
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
                    borderColor: window.chartColors.blue,
                    fill: false,
                    data: memory_points
                }]
        },
        options: {
            ...common_options,
            ...{
                annotation: {
                    annotations: [
                        {
                            // !! scaleID is required !!
                            scaleID: 'main',
                            drawTime: 'afterDraw',
                            type: 'line',
                            mode: 'horizontal',
                            value: {{ round($status->memory_total/1000) }},
                            borderColor: window.chartColors.red,
                            borderWidth: 2
                        },
                    ]
                }
            }
        },
    });
});
</script>

<div class="text-muted" style="position: fixed; bottom: 0; width: 100%">

    Powered by <b>cyrange</b>, the open Cyber Range platform.
    <a href="https://gitlab.cylab.be/cylab/cyrange">https://gitlab.cylab.be/cylab/cyrange</a>

    <div style="float: right">
    Reload in <span id="reload-countdown">60</span> seconds
    </div>
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

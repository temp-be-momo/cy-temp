@component('mail::message')
# Your monthly recap

Hi! This is the monthly recap of the machines you have on {{ config('app.name') }}...

@component('mail::table')

|    | Running  | Total  |
|---|---:|---:|
| Machines  | {{ $summary->vm_count_running }}  |  {{ $summary->vm_count }} |
| vCores    | {{ $summary->vcpu_count_running }}  | {{ $summary->vcpu_count }}  |
| Memory    | {{ $summary->memory_running }}MB  | {{ $summary->memory }}MB  |
| Storage   |   | {{ round($summary->disk_size/1e9, 1) }}GB  |
|           |   |   |
| CO<sub>2</sub>  | {{ round($summary->monthlyCO2()/1000, 1) }}kg/month  |   |

@endcomponent

<div style="display:flex;">
@component('mail::button', ['url' => action('VMController@index')])
Manage your machines
@endcomponent

@component('mail::button', ['url' => action('VMController@co2')])
Check your CO<sub>2</sub> report
@endcomponent
</div>

Kind regards,

{{ config('app.name') }}
@endcomponent

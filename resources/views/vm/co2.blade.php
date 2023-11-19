@extends('layouts.app')

@section('title', "CO2 report")

@section('content')
<h1><i class="fas fa-leaf text-green"></i> CO<sub>2</sub> report</h1>


<table class="table mt-4">
    <tr>
        <td>Power per vCore</td>
        <td class="text-end">{{ $summary->powerPerCore() }}</td>
        <td>W/vCore</td>
        <td class="text-end">Estimated electricity consumed by a single vCore on this system.</td>
    </tr>
    
    <tr>
        <td>vCores</td>
        <td class="text-end">{{ $summary->vcpu_count_running }}</td>
        <td>vCores</td>
        <td class="text-end">Number of vCores used by your running virtual machines.</td>
    </tr>
    
    <tr>
        <td>Total power</td>
        <td class="text-end">{{ $summary->power() }}</td>
        <td>W</td>
        <td class="text-end"><code>power per vCore x vCores</code></td>
    </tr>
    
    <tr>
        <td>Month duration</td>
        <td class="text-end">{{ $summary->monthDuration() }}</td>
        <td>h/month</td>
        <td class="text-end"><code>30 days x 24h</code></td>
    </tr>
    
    <tr>
        <td>Monthly energy</td>
        <td class="text-end">{{ round($summary->monthlyEnergy(), 1) }}</td>
        <td>kWh/month</td>
        <td class="text-end"><code>total power x month duration / 1000</code></td>
    </tr>
    
    <tr>
        <td>Carbon intensity</td>
        <td class="text-end">{{ $summary->carbonIntensity() }}</td>
        <td>g/kWh</td>
        <td class="text-end">Average amount of CO<sub>2</sub> released to produce 1 kWh of electricity.</td>
    </tr>
    
    <tr>
        <td>Monthly emission</td>
        <td class="text-end"><b>{{ round($summary->monthlyCO2()) }}</b></td>
        <td><b>g/month</b></td>
        <td class="text-end"><code>monthly energy x carbon intensity</code></td>
    </tr>
    
    <tr>
        <td colspan="4">&nbsp;</td>
    </tr>
    
    <tr>
        <td>Average car emission</td>
        <td class="text-end">{{ $summary->averageCarEmission() }}</td>
        <td>g/km</td>
        <td class="text-end">Average amount of CO<sub>2</sub> released by a car, per kilometer.</td>
    </tr>
    
    <tr>
        <td>Car equivalent</td>
        <td class="text-end">{{ round($summary->carEquivalent(), 1) }}</td>
        <td>km/month</td>
        <td class="text-end"><code>monthly emission / average car emission</code></td>
    </tr>
    
</table>
@endsection
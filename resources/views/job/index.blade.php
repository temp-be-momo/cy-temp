@extends('layouts.app')

@section('title', 'Jobs')

@section('content')
<h1>Jobs</h1>

<table class="table table-lined">
    <tr>
        <th>#</th>
        <th>Type</th>
        <th>Name</th>
        <th>User</th>
        <th>Status</th>
    </tr>
    @foreach($jobs as $job)
    <tr>
        <td>
            <a href="{{ action('JobController@show', ['job' => $job]) }}">
                <b>{{ sprintf('%04d', $job->id) }}</b>
            </a>
        </td>
        <td>{{ $job->type }}</td>
        <td>{{ $job->name }}</td>
        <td>{{ optional($job->user)->name }}</td>
        <td>{{ $job->status() }}</td>
    </tr>
    @endforeach
</table>

{{ $jobs->links() }}
@endsection

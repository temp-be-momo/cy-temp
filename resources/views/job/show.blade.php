@extends('layouts.app')

@section('title', 'Job #' . sprintf('%04d', $job->id))

@section('content')
<div class="card my-3">
  <div class="card-body">
      <p>
          Type: <b>{{ $job->type }}</b>
      </p>

      <p>
          Status: <b>{{ $job->status() }}</b>
      </p>

      <ul>
        <li>
            Queued: <b>{{ $job->timeQueued() }}</b>
        </li>

        <li>
            Started: <b>{{ $job->timeStarted() }}</b>
        </li>

        <li>
            Finished: <b>{{ $job->timeFinished() }}</b>
        </li>
      </ul>

      <p>User: <b>{{ $job->user->name }}</b></p>

      <p>
          VM Name: <b>{{ $job->name }}</b>
      </p>

      <p>
          VM UUID: <b>{{ $job->vm_uuid }}</b>
      </p>

      @if ($job->isFinished())
      <p>
          Execution time: {{ $job->executionTime() }}
      </p>
      @endif
  </div>
</div>

<div class="card">
    <div class="card-body">
        <code id='logs'></code>
        <div id='spinner' class='mt-2'>
        <i class="fas fa-circle-notch fa-spin"></i>
        </div>
    </div>
</div>


<script>
    window.addEventListener('load', function() {
        var status = $("#logs");
        poll = function() {
          $.ajax({
            url: '{{ action("JobController@logs", ["job" => $job]) }}',
            dataType: 'json',
            type: 'get',
            success: function(job) {
                status.html(job.logs.replace(/\n/g,"<br>"));

                // scroll to bottom
                window.scrollTo(0, document.body.scrollHeight);

                if (job.finished ) {
                    clearInterval(pollInterval);
                    $('#spinner').remove();
                }
            },
            error: function() {
              console.log('Error!');
            }
          });
        };

        pollInterval = setInterval(function() {
            poll();
        }, 2000);

        poll();
    });
</script>

@endsection

<?php

namespace App\Http\Controllers;

use App\JobResult;

/**
 * List and view DeploymentResult
 */
class JobController extends Controller
{
    public function index()
    {
        return view(
            'job.index',
            ['jobs' => JobResult::orderBy('id', 'desc')->paginate(50)]
        );
    }

    public function show(JobResult $job)
    {
        return view('job.show', ['job' => $job]);
    }

    public function logs(JobResult $job)
    {
        return response()->json([
                "finished" => $job->isFinished(),
                "logs" => $job->getLog()]);
    }
}

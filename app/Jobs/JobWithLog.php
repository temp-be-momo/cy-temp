<?php

namespace App\Jobs;

use App\JobResult;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Support\Facades\Auth;

/**
 * Description of JobWithLog
 *
 * @author tibo
 */
abstract class JobWithLog implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * In seconds
     * @var int
     */
    public $timeout = 3600;

    /**
     * The number of times the job may be attempted.
     *
     * if tries >= 2, the job will be retried only after the timeout delay is
     * exhausted:
     *
     * https://laravel.com/docs/5.8/queues#job-expirations-and-timeouts
     *
     * @var int
     */
    public $tries = 1;

    /**
     * Must be 'protected' otherwize the field will not be serialized!
     * https://github.com/laravel/framework/issues/24127
     *
     * @var JobResult
     */
    protected $result;

    /**
     * Dispatch the job with the given arguments.
     * We mimic here the normal behavior of Jobs, but return an instance of
     * JobResult
     * see Illuminate\Foundation\Bus\Dispatchable
     * and https://laravel.com/docs/9.x/queues#dispatching-jobs
     *
     * The JobResult instance must be created by the child classes.
     *
     * @return JobResult
     */
    public static function dispatch() : JobResult
    {
        /** https://phpstan.org/blog/solving-phpstan-error-unsafe-usage-of-new-static */
        /** @phpstan-ignore-next-line */
        $job = new static(...func_get_args());

        $result = $job->createJobResultInstance();
        $result->user_id = Auth::id();
        $result->save();
        $job->result = $result;

        new PendingDispatch($job);

        return $result;
    }
    
    public static function dispatchNow() : JobResult
    {
        /** https://phpstan.org/blog/solving-phpstan-error-unsafe-usage-of-new-static */
        /** @phpstan-ignore-next-line */
        $job = new static(...func_get_args());

        $result = $job->createJobResultInstance();
        $result->save();
        $job->result = $result;

        $job->handle();

        return $result;
    }

    abstract public function createJobResultInstance() : JobResult;

    public function result() : JobResult
    {
        return $this->result;
    }
    
    public function logger() : \Monolog\Logger
    {
        return $this->result->logger();
    }

    public function handle()
    {
        $result = $this->result;
        $result->time_started = time();
        $result->save();
        $result->logger()->info("Started...");

        try {
            $this->doHandle();
        } catch (\Exception $ex) {
            $result->logger()->error($ex->getMessage());
        } finally {
            $result->logger()->info("Done!");
            $result->time_finished = time();
            $result->save();
        }
    }

    public function failed(\Exception $exception)
    {
        $result = $this->result;
        $logger = $result->logger();
        $logger->warning($exception->getMessage());
        $result->time_finished = time();
        $result->save();
    }

    abstract protected function doHandle();
}

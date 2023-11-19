<?php

namespace App\Jobs;

use App\User;
use App\VM;
use App\JobResult;

class UpAll extends JobWithLog
{

    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
    
    protected function doHandle()
    {
        foreach (VM::all() as $vm) {
            /** @var VM $vm */
            $this->logger()->notice("Starting " . $vm->name . " ...");
            $vm->getVBoxVM()->up();
            sleep(5);
        }
    }

    public function createJobResultInstance(): JobResult
    {
        $result = new JobResult();
        $result->type = "UP ALL";
        $result->user_id = $this->user->id;
        return $result;
    }
}

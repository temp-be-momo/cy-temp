<?php

namespace App\Jobs;

use App\User;
use App\VM;
use App\JobResult;

class HaltAll extends JobWithLog
{
    
    /**
     *
     * @var User
     */
    private $user;
    
    public function __construct(User $user)
    {
        
        $this->user = $user;
    }
    
    protected function doHandle()
    {
        foreach (VM::all() as $vm) {
            /** @var VM $vm */
            $this->logger()->notice("Stopping " . $vm->name . " ...");
            $vm->getVBoxVM()->halt();
            sleep(5);
        }
    }

    public function createJobResultInstance(): JobResult
    {
        $result = new JobResult();
        $result->type = "HALT ALL";
        $result->user_id = $this->user->id;
        return $result;
    }
}

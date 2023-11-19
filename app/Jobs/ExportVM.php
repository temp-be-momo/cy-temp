<?php

namespace App\Jobs;

use App\User;
use App\VM;
use App\Image;
use App\JobResult;

class ExportVM extends JobWithLog
{
    private $vm;
    private $name;
    private $description;
    private $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        VM $vm,
        string $name,
        string $description,
        User $user
    ) {

        $this->vm = $vm;
        $this->name = $name;
        $this->description = $description;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    protected function doHandle()
    {
        // Force reconnection to VirtualBox
        \App\VBoxVM::connect();

        $result = $this->result();

        $result->logger()->info("Export VM " . $this->vm->name .
                " to image " . $this->name . " ...");

        $image = new Image();
        $image->user_id = $this->result->user_id;
        $image->name = $this->name;
        $image->description = $this->description;
        $image->save();

        $this->vm->getVBoxVM()->export($image->getPathForVBox());

        $result->logger()->info("Done!");
    }

    public function createJobResultInstance(): JobResult
    {
        $result = new JobResult();
        $result->vm_uuid = $this->vm->getUUID();
        $result->name = $this->name;
        $result->type = JobResult::EXPORT;
        $result->user_id = $this->user->id;
        return $result;
    }
}

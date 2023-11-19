<?php

namespace App\Jobs;

use App\User;
use App\VM;
use App\JobResult;

use Cylab\Guacamole\Connection;

/**
 * Destroy a VM
 */
class DestroyVM extends JobWithLog
{

    /**
     *
     * @var \App\VM
     */
    private $vm;
    private $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(VM $vm, User $user)
    {
        $this->vm = $vm;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    protected function doHandle()
    {
        $result = $this->result();

        // Force reconnection to VirtualBox
        \App\VBoxVM::connect();

        $vboxvm = $this->vm->getVBoxVM(true);
        $rdp = $vboxvm->getVRDEServer();
        if ($rdp->isEnabled()) {
            $result->logger()->info("Delete Guacamole connections ...");
            $port = $rdp->getPort();
            foreach (Connection::byPort($port) as $connection) {
                $connection->delete();
            }
        }

        $result->logger()->info("Destroy VirtualBox VM ...");
        $vboxvm->destroy();
        $this->vm->delete();
    }

    public function createJobResultInstance(): JobResult
    {
        $result = new JobResult();
        $result->vm_uuid = $this->vm->getUUID();
        $result->name = $this->vm->getName();
        $result->type = JobResult::DESTROY;
        $result->user_id = $this->user->id;
        return $result;
    }
}

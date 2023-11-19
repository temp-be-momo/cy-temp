<?php

namespace App\Jobs;

use App\User;
use App\VM;
use App\JobResult;
use App\Guacamole;

use App\Cyrange\Blueprint;
use App\Cyrange\BlueprintDeployer;

use Cylab\Vbox\VBox;

class DeployBlueprint extends JobWithLog
{
    private $blueprint;
    private $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Blueprint $blueprint, User $user)
    {
        $this->blueprint = $blueprint;
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
        $logger = $result->logger();

        $vbox = new VBox(
            config('vbox.user'),
            config('vbox.password'),
            "http://" . config('vbox.host') . ":18083"
        );

        $deployer = new BlueprintDeployer($vbox, $logger);
        $vboxvm = $deployer->deploy($this->blueprint);

        $vm = new VM();
        $vm->user_id = $this->user->id;
        $vm->name = $this->blueprint->getHostname();
        $vm->uuid = $vboxvm->getUUID();
        $vm->save();

        $logger->info("Create web access for user " . $this->user->email);
        Guacamole::assignVMToEmail($this->blueprint, $this->user->email);
    }

    public function createJobResultInstance(): JobResult
    {
        $result = new JobResult();
        $result->name = $this->blueprint->getHostname();
        $result->type = JobResult::DEPLOY;
        $result->user_id = $this->user->id;
        return $result;
    }
}

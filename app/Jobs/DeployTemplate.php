<?php

namespace App\Jobs;

use App\VM;
use App\Template;
use App\User;
use App\JobResult;
use App\Guacamole;
use App\Mail\VMDeployed;
use App\Cyrange\BlueprintDeployer;

use Cylab\Vbox\VBox;

use Illuminate\Support\Facades\Mail;

class DeployTemplate extends JobWithLog
{
    public $template;
    public $user;
    public $vm_name;
    public $guacamole_email;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        Template $template,
        User $user,
        string $vm_name,
        ?string $guacamole_email
    ) {
        $this->template = $template;
        $this->user = $user;
        $this->vm_name = $vm_name;
        $this->guacamole_email = $guacamole_email;
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

        $blueprint = $this->template->getBlueprint();
        $blueprint->setHostname($this->vm_name);
        $blueprint->setGroupName("/cyrange/" . $this->user->slug());

        // for the VM name in VirtualBox, we generate a unique random
        // name to avoid directory collision
        // https://gitlab.cylab.be/cylab/cyber-range-manager/-/issues/16
        $name = date('Ymd.His.') . mt_rand(100, 999) . '.' . $this->vm_name;
        $blueprint->setName($name);

        if ($this->guacamole_email != null) {
            $blueprint->setNeedRdp(true);
        }

        $vbox = new VBox(
            config('vbox.user'),
            config('vbox.password'),
            "http://" . config('vbox.host') . ":18083"
        );

        $deployer = new BlueprintDeployer($vbox, $logger);
        $vboxvm = $deployer->deploy($blueprint);

        $vm = new VM();
        $vm->user_id = $this->user->id;
        $vm->name = $this->vm_name;
        $vm->uuid = $vboxvm->getUUID();
        $vm->save();

        $logger->info("Wait for VM to boot...");
        sleep($this->template->boot_delay);
        $logger->info("IP : " . $vboxvm->getNetworkAdapter(0)->getIPAddress());

        // Create guacamole connection and user if needed
        if ($this->guacamole_email != null) {
            $logger->info("Create web access for user " . $this->guacamole_email);
            Guacamole::assignVMToEmail($blueprint, $this->guacamole_email);
        }

        // Notify owner
        Mail::to($this->user->email)->send(
            new VMDeployed($this, $blueprint)
        );
    }

    public function createJobResultInstance(): JobResult
    {
        $result = new JobResult();
        $result->name = $this->vm_name;
        $result->type = JobResult::DEPLOY;
        $result->user_id = $this->user->id;
        return $result;
    }
}

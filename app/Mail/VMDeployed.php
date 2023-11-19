<?php

namespace App\Mail;

use App\Jobs\DeployTemplate;
use App\Cyrange\Blueprint;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VMDeployed extends Mailable
{
    use Queueable, SerializesModels;

    /**
     *
     * @var \App\Cyrange\Blueprint
     */
    public $blueprint;

    /**
     *
     * @var \App\Jobs\DeployTemplate;
     */
    public $job;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(DeployTemplate $job, Blueprint $blueprint)
    {
        $this->job = $job;
        $this->blueprint = $blueprint;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
                ->subject("[Cyber Range] Deployed " . $this->job->vm_name)
                ->markdown('emails.vm.deployed');
    }
}

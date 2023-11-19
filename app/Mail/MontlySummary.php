<?php

namespace App\Mail;

use App\VMSummary;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MontlySummary extends Mailable
{
    use Queueable, SerializesModels;

    public $vms;
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(iterable $vms)
    {
        $this->vms = $vms;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.monthlysummary')
                ->subject("[Cyrange] Your monthly summary")
                ->with([
                    "vms" => $this->vms,
                    "summary" => VMSummary::fromVMList($this->vms)]);
    }
}

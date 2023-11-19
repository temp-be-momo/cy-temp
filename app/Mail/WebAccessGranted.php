<?php

namespace App\Mail;

use App\Cyrange\Blueprint;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WebAccessGranted extends Mailable
{
    use Queueable, SerializesModels;

    /**
     *
     * @var \App\Cyrange\Blueprint
     */
    public $blueprint;

    /**
     *
     * @var string
     */
    protected $note;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Blueprint $blueprint)
    {
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
                ->subject("[Cyber Range] Web Access to " . $this->blueprint->getHostname())
                ->markdown('emails.webaccess.granted');
    }
}

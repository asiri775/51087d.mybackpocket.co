<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EnvelopeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $template;
    public $envelop_name;
    public $link;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject,$envelop_name,$template,$link)
    {
        $this->envelop_name=$envelop_name;
        $this->subject = $subject;
        $this->template = $template;
        $this->link=$link;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->subject('ENVELOPE#'.$this->envelop_name.': '.$this->subject);
        $template=$this->template;
        return $this->view('emails.vendor.EnvelopeEmail', compact('template'));
    }
}

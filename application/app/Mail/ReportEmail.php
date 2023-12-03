<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReportEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $template;
    public $user_id;
    public $link;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject,$transaction,$template,$link)
    {
        $this->transaction_id=$transaction->id;
        $this->subject = str_replace(':transaction_id',$this->transaction_id,$subject);
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
        $this->subject('ORDER#'.$this->transaction_id.': '.$this->subject);
        $template=$this->template;
        $transaction_id=$this->transaction_id;
        return $this->view('emails.vendor.InvoiceEmail', compact('template', 'transaction_id'));
    }
}

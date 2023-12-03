<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $template;
    public $vendor_logo;
    public $vendor_name;
    public $vendor_street_name;
    public $vendor_city;
    public $vendor_state;
    public $vendor_zip_code;
    public $transaction_id;
    public $transaction_bar_qr_code;
    public $transaction_date;
    public $transaction_time;
    public $transaction_payment_method;
    public $transaction_payment_ref;
    public $transaction_auth_id;
    public $transaction_sub_total;
    public $transaction_tax;
    public $transaction_total;
    public $customer_name;
    public $customer_street_name;
    public $customer_city;
    public $customer_state;
    public $customer_zip_code;
    public $customer_phone;
    public $customer_email;
    public $product_listing;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject,$transaction,$product_listing,$vendor,$template)
    {
        $this->vendor_logo=$vendor->logo;
        $this->vendor_name=$vendor->name;
        $this->vendor_street_name=$vendor->street_name;
        $this->vendor_city=$vendor->city;
        $this->vendor_state=$vendor->state;
        $this->vendor_zip_code=$vendor->zip_code;
        $this->transaction_id=$transaction->id;
        $this->transaction_bar_qr_code=$transaction->bar_qr_code;
        $this->transaction_date=date("m/d/y", strtotime($transaction->transaction_date));
        $this->transaction_time=$transaction->transaction_time;
        $this->transaction_payment_method=$transaction->payment_method;
        $this->transaction_payment_ref=$transaction->payment_ref;
        $this->transaction_auth_id=$transaction->auth_id;
        $this->transaction_sub_total=$transaction->sub_total;
        $this->transaction_tax=$transaction->tax_amount;
        $this->transaction_total=$transaction->total;
        $this->customer_name= $transaction->cus_name;
        $this->customer_street_name= $transaction->cus_street_name;
        $this->customer_city= $transaction->cus_city;
        $this->customer_state= $transaction->cus_state;
        $this->customer_zip_code= $transaction->cus_zip_code;
        $this->customer_phone= $transaction->cus_phone;
        $this->customer_email= $transaction->cus_email;
        $this->product_listing= $product_listing;
        $this->subject = str_replace(':transaction_id',$this->transaction_id,$subject);
        $this->template = $template;

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

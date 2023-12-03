<?php

/**
 * Created by PhpStorm.
 * User: DevEnviroment
 * Date: 2020-06-30
 * Time: 22:31
 */

namespace App\Vendors;


use App\Models\Transaction;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CascadesRecoveryEtobic
{
    private $htmlBody;
    private $plainText;
    private $sender;
    private $textArray;
    private $textCollection;
    private $detail = [];

    private $vendor_name;
    private $vendor_email;
    private $vendor_address = null;

    private $products = [];

    private $order_no;
    private $emailDate;
    private $discount;
    private $sub_total = 0;
    private $tax_amount = 0;
    private $total = 0;
    private $auth_id = null;
    private $payment_method = null;
    private $payment_ref = null;
    private $extra_info = null;

    public function __construct($htmlBody, $plainText, $emailDate, $sender, $message_id)
    {
        $this->htmlBody = $htmlBody;
        $this->plainText = $plainText;
        $this->emailDate = $emailDate;
        $this->message_id = $message_id;

        $this->vendor_name = "CascadesRecoveryEtobic";
        $this->sender = $sender;
        $this->vendor_email = $this->sender->mail;

        $this->plainTextToArray();
        $this->setOrderNo();
        $this->setDiscount();
    }

    /**
     *
     */
    private function plainTextToArray()
    {
        /**
         * Convert plaintext into array
         */
        $tmp_content = explode('--tagend--', $this->plainText);

        //removing empty elements from content array
        $tmp_content = array_values(array_filter($tmp_content));

        //removing extra spaces from array
        $this->textArray = array_filter($tmp_content, function ($e) {
            return preg_replace('/\s+/', ' ', $e);
        });
        $this->textCollection = collect($this->textArray);
    }

    //TODO: Set configuration, for example start and end point of parsing
    private function isInvoice()
    {
        if (Str::contains(Str::lower($this->plainText), Str::lower("PURCHASEORDER ID"))) { //If it is not order/invoice then skip it.
            return true;
        }
        return false;
    }

    private function setOrderNo()
    {
        $order_no = null;
        if (Str::contains($this->htmlBody, 'ORDER ID')) {
            $this->order_no = strip_tags(Str::between($this->htmlBody, "ORDER ID", "AMOUNT"));
        }
        $this->order_no = trim($this->order_no);
    }

    private function transactionExists()
    {
        $transaction_exists = Transaction::where('order_no', $this->order_no)->exists();
        if ($transaction_exists) {
            return true;
        }
        return false;
    }

    private function getDiscountIndex()
    {
        return array_search('Discount', $this->textArray);
    }

    private function setDiscount()
    {
        if ($this->getDiscountIndex()) {
            $this->discount = $this->textArray[$this->getDiscountIndex() + 1];
        }
    }

    private function setVendor()
    {
        //If email is forwarded mail
        if (Str::contains(Str::lower($this->plainText), Str::lower("DATE"))) {
            //TODO: check if there are more than forwarded messages

            $date_text = Str::between($this->htmlBody, "VISA<br><br>DATE", "REF NUM");
            $date_text = preg_replace("/\r|\n|\t/", "", $date_text);
            $date_text = trim(preg_replace('/\s+/', ' ', $date_text));
            $date_text = strip_tags(Str::after($date_text, "Date:"));

            $this->emailDate =  Carbon::parse($date_text);
            //If string contains fails then use the default email address
            if (Str::contains($this->htmlBody, 'CASCADES RECOVERY+ ETOBIC &lt;')) {
                $vendorEmailStr = Str::between($this->htmlBody, "CASCADES RECOVERY+ ETOBIC &lt;", "@moneris.com");
                $this->vendor_email = strip_tags($vendorEmailStr) . '@moneris.com';
            } else {
                $this->vendor_email = "esp_receipt@moneris.com";
            }
        }
    }

    private function setProducts()
    {
        /**
         * Products Details
         */
        $prods_text = null;

        //TODO: Check with discounted email
        $prods_text = Str::between($this->plainText, "PURCHASE", "APPROVED");
        if ($this->getDiscountIndex()) {
            $prods_text = Str::between($this->plainText, "PURCHASE", "APPROVED");
        }

        $prods_array = explode('--tagend--', $prods_text);
        $prods_array = array_values(array_filter($prods_array));

        if (Str::contains($this->htmlBody, 'AMOUNT (CAD)')) {
            $total_label_index = str_replace(' ', '', Str::between($this->htmlBody, "AMOUNT (CAD)", "<br><br>CARD NUM"));
            $total_label_index = floatval(str_replace('$', '', $total_label_index));
        }



        $prods = [];
        foreach ($prods_array as $key => $value) {
            if (Str::contains($value, '$')) {
                $prods[] = [
                    'name' => 'Purchase',
                    'price' => $total_label_index
                ];
            }
        }

        $this->products = $prods;
    }

    private function setExtraInfo()
    {

        $delivery_fee_label_index = array_search('Delivery Fee', $this->textArray);
        $tip_label_index = array_search('Tip the Food Courier', $this->textArray);
        $discount_index = array_search('Discount', $this->textArray);
        $ex_info = [];
        if ($delivery_fee_label_index) {
            $ex_info[] = [
                'label' => "Delivery Fee",
                'value' => $this->textArray[$delivery_fee_label_index + 1],
                'key' => 'delivery_fee',
                'type' => 'amount'
            ];
        }

        if ($tip_label_index) {
            $ex_info[] = [
                'label' => "Tip the Food Courier",
                'value' => $this->textArray[$tip_label_index + 1],
                'key' => 'tip',
                'type' => 'amount'
            ];
        }

        if ($discount_index) {
            $ex_info[] = [
                'label' => "Discount",
                'value' => $this->textArray[$discount_index + 1],
                'key' => 'discount',
                'type' => 'amount'
            ];
        }

        $this->extra_info = collect($ex_info)->toJson();
    }

    private function setTransaction()
    {

        if (Str::contains($this->htmlBody, 'AMOUNT (CAD)')) {
            $total_label_index = str_replace(' ', '', Str::between($this->htmlBody, "AMOUNT (CAD)", "<br><br>CARD NUM"));
        }

        if (Str::contains($this->htmlBody, 'REF NUM')) {
            $pay_ref = ltrim(Str::between($this->htmlBody, "REF NUM", "<br><br>AUTH CODE"));
        }

        if (Str::contains($this->htmlBody, 'CARD NUM')) {
            $payment_method = trim(Str::between($this->htmlBody, "CARD NUM", "<br>ACCOUNT"));
            $payment_method = substr($payment_method, -6);
        }

        if (Str::contains($this->htmlBody, 'AUTH CODE')) {
            $auth_id = ltrim(Str::between($this->htmlBody, "AUTH CODE", " <br><br>------------------------"));
        }


        $this->payment_method = $payment_method ?? '';
        $this->payment_ref = $pay_ref ?? '';
        $this->sub_total = floatval(str_replace('$', '', $total_label_index)) ?? '';
        $this->total = floatval(str_replace('$', '', $total_label_index)) ?? '';
        $this->auth_id = $auth_id;
        $this->setExtraInfo();
    }

    public function parseEmail()
    {
        try {

            if (!$this->isInvoice()) return false;

            $this->setOrderNo();

            /**
             * Check if the transaction/order already exists then return false stop further
             * proceeding to avoid any duplication
             */
            if ($this->transactionExists()) return false;

            /**
             * Set vendor properties required for DB
             */
            $this->setVendor();

            /**
             * Set Products properties required for DB
             */
            $this->setProducts();

            /**
             * Set Transaction properties required for DB
             */
            $this->setTransaction();

            //End Products & Transactions

            $this->setDetail();

            return $this->detail;
        } catch (Exception $exception) {
            Log::error("Array Creation Error: " . $exception->getMessage());
            return false;
        }
    }

    public function setDetail()
    {
        $this->detail = [
            'vendor' => [
                'email' => $this->vendor_email,
                'name' => $this->vendor_name,
                'address' => $this->vendor_address
            ]
        ];

        $this->detail['products'] = $this->products;
        $this->detail['transaction'] = [
            'order_no' => $this->order_no,
            'transaction_date' => $this->emailDate->format('Y-m-d H:i:s'),
            'sub_total' => $this->sub_total,
            'discount' => $this->discount,
            'total' => $this->total,
            'tax_amount' => $this->tax_amount,
            'payment_method' => $this->payment_method,
            'payment_ref' => $this->payment_ref,
            'auth_id' =>    $this->auth_id,
            'message_id' => $this->message_id,
            'extra_info' => $this->extra_info
        ];
    }

    public function getDetail()
    {
        return $this->detail;
    }
}

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

class CanadianTire
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
    private $vendor_hst = null;
    private $vendor_street_name = null;
    private $vendor_unit = null;
    private $vendor_city = null;
    private $vendor_state = null;
    private $vendor_zip_code = null;


    private $products = [];

    private $order_no;
    private $emailDate;
    private $discount;
    private $sub_total = 0;
    private $tax_amount = 0;
    private $total = 0;
    private $payment_method = null;
    private $payment_ref = null;
    private $auth_id = null;
    private $extra_info = null;
    private $bar_qr_code = null;

    public function __construct($htmlBody, $plainText, $emailDate, $sender, $message_id)
    {
        $this->htmlBody = $htmlBody;
        $this->plainText = $plainText;
        $this->emailDate = $emailDate;
        $this->message_id = $message_id;

        $this->vendor_name = "CanadianTire";
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
        if (Str::contains(Str::lower($this->plainText), Str::lower("Order #:"))) { //If it is not order/invoice then skip it.
            return true;
        }
        return false;
    }

    private function setOrderNo()
    {
        $order_no = null;
        $order_no = array_search('Order #: ', $this->textArray);
        if ($order_no) {
            $this->order_no = trim($this->textArray[$order_no + 1]);
        }
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
        if (Str::contains(Str::lower($this->plainText), Str::lower("REG #:"))) {
            //TODO: check if there are more than forwarded messages
            $date_text = Str::between($this->htmlBody, "@email.canadiantire.ca", "Subject: ");

            $date_text = preg_grep('/REG #:/', $this->textArray);
            $date_text = implode($date_text);
            $date_text = Str::after($date_text, 'REG #:');
            $date_text = Str::between($date_text, ' ', 'TRANS');

            $date_text = preg_replace("/\r|\n|\t/", "", $date_text);
            $date_text = trim(preg_replace('/\s+/', ' ', $date_text));
            $date_text = strip_tags($date_text);

            $this->emailDate =  Carbon::parse($date_text);

            //If string contains fails then use the default email address
            if (Str::contains($this->htmlBody, 'Canadian Tire &lt;')) {
                $vendorEmailStr = Str::between($this->htmlBody, "Canadian Tire &lt;", "@email.canadiantire.ca");
                $this->vendor_email = strip_tags($vendorEmailStr) . '@email.canadiantire.ca';
            } else {
                $this->vendor_email = "account@email.canadiantire.ca";
            }
        }

        $vendor_tax = preg_grep('/HST:/', $this->textArray);
        if ($vendor_tax) {
            $this->vendor_hst = trim(str_replace('HST:', '', implode($vendor_tax)));
        }

        if (Str::contains($this->plainText, "My Account")) {
            $vendor_address = explode('©', str_replace('--tagend--', '', Str::after($this->plainText, "Canadian Tire Corporation,")));
            $this->vendor_address = trim($vendor_address[0]) ?? '';
            $address = explode(',', $vendor_address[0]) ?? '';
            $this->vendor_street_name = trim($address[0]);
            $this->vendor_city = trim($address[1]);
            $this->vendor_state = substr(trim($address[2]), 0, 2);
            $this->vendor_zip_code = substr(trim($address[2]), 2, -1);
        }
    }

    private function setProducts()
    {
        /**
         * Products Details
         */
        $prods_text = null;

        //TODO: Check with discounted email

        $prods_text = Str::between($this->plainText, "=eCommerce Order", "SUBTOTAL");
        if ($this->getDiscountIndex()) {
            $prods_text = Str::between($this->plainText, "=eCommerce Order", "SUBTOTAL");
        }

        $prods_array = explode('--tagend--', $prods_text);
        //$prods_array = array_map('trim', $prods_array);
        $prods_array = array_values(array_filter($prods_array));
        $prods = [];

        foreach ($prods_array as $key => $value) {
            if (Str::contains($value, '$')) {
                $prods[] = [
                    'name' => explode('$', $value)[0],
                    'price' => floatval(explode('$', $value)[1]),
                ];
            }
        }
        return $this->products = $prods;
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

    public function setTransaction()
    {

        $sub_amount = preg_grep('/^SUBTOTAL\s.*/', $this->textArray);
        $sub_amount = trim(str_replace('SUBTOTAL', '', implode($sub_amount)));

        $total_amount = preg_grep('/^T O T A L\s.*/', $this->textArray);
        $total_amount = trim(str_replace('T O T A L', '', implode($total_amount)));

        $tax = preg_grep('/HST /', $this->textArray);
        $tax = trim(str_replace('HST ', '', implode($tax)));

        $payment_method = preg_grep('/VISA #:/', $this->textArray);
        $payment_method = trim(str_replace('#: ***********', '', implode($payment_method)));

        $payment_ref = preg_grep('/^REF #:\s.*/', $this->textArray);
        $payment_ref = trim(str_replace('REF #:', '', implode($payment_ref)));

        $auth_id = preg_grep('/AUTHORIZATION #:/', $this->textArray);
        $auth_id = trim(Str::after(implode($auth_id), 'AUTHORIZATION #:'));

        $bar_code = array_search('THANK YOU FOR SHOPPING AT CANADIAN TIRE!', $this->textArray);

        $this->sub_total = floatval(str_replace('$', '', $sub_amount));
        $this->tax_amount = floatval(str_replace('$', '', $tax));
        $this->total = floatval(str_replace('$', '', $total_amount));

        if ($bar_code) {
            $this->bar_qr_code = $this->textArray[$bar_code - 1] ?? '   ';
        }



        $this->payment_ref = $payment_ref;
        $this->payment_method = $payment_method;
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
                'address' => $this->vendor_address,
                'Hst' => $this->vendor_hst,
                'street_name' => $this->vendor_street_name,
                'city' => $this->vendor_city,
                'state' => $this->vendor_state,
                'zip_code' => $this->vendor_zip_code,
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
            'auth_id' => $this->auth_id,
            'payment_method' => $this->payment_method,
            'payment_ref' => $this->payment_ref,
            'message_id' => $this->message_id,
            'extra_info' => $this->extra_info,
            'bar_qr_code' => $this->bar_qr_code,
        ];
    }

    public function getDetail()
    {
        return $this->detail;
    }
}

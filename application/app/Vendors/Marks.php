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

class Marks
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
    private $vendor_store_no = null;
    private $vendor_hst =  null;
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
    private $br_code = null;
    private $terminal_no = null;
    private $payment_ref = null;
    private $payment_method = null;
    private $auth_no = null;
    private $extra_info = null;

    public function __construct($htmlBody, $plainText, $emailDate, $sender, $message_id)
    {
        $this->htmlBody = $htmlBody;
        $this->plainText = $plainText;
        $this->emailDate = $emailDate;
        $this->message_id = $message_id;

        $this->vendor_name = "Marks";
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

        if (Str::contains(Str::lower($this->plainText), Str::lower("Thank you for shopping with Mark"))) { //If it is not order/invoice then skip it.
            return true;
        }
        return false;
    }

    private function setOrderNo()
    {
        $order_no = null;

        $order_no = str_replace('--tagend--', '', $this->plainText);

        if (Str::contains($order_no, "Thank you for shopping with Mark")) {
            $order_no = Str::between($order_no, 'CUSTOMER COPY', 'Date');

            $order_no = Str::afterLast($order_no, 'Trn ');
        }

        $this->order_no = $order_no;
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
        if (in_array('Discount', $this->textArray)) {
            return true;
        } else {
            return false;
        }
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
        $date_text = null;

        if (Str::contains(Str::lower($this->plainText), Str::lower("CUSTOMER COPY"))) {

            //If string contains fails then use the default email address
            $vendorEmailStr = str_replace('--tagend--', '', $this->plainText);
            if (Str::contains($vendorEmailStr, 'recent purchase at Mark')) {

                $string = Str::between($this->plainText, 'From: Mark', 'To:');
                $string = Str::between($string, '<', '>');
                $string = str_replace('--tagend--', '', $string);

                $this->vendor_email =  $string;
            }
        }

        $date_text = Str::between($this->plainText, 'DATE/TIME', 'REFERENCE');

        $date_text = preg_replace("/\r|\n|\t/", "", $date_text);

        $date_text = trim(preg_replace('/\s+/', ' ', $date_text));
        $date_text = strip_tags(Str::after($date_text, "Date:"));

        $date = Carbon::parse($date_text);
        $this->emailDate = $date;


        if (Str::contains($this->plainText, "London North")) {

            $vendor_address = str_replace('--tagend--', '', $this->plainText);

            $vendor_address = Str::between($vendor_address, 'receipt. ', '519-');

            $this->vendor_address = $vendor_address;

            $vendor_address = explode(',', $vendor_address);

            $this->vendor_street_name = trim($vendor_address[0]);
            $this->vendor_unit = trim($vendor_address[1]);
            $this->vendor_city = trim($vendor_address[2]);
            $this->vendor_state = trim($vendor_address[3]);
            $this->vendor_zip_code = trim($vendor_address[4]);
        }


        if (Str::contains($this->plainText, 'CUSTOMER COPY')) {

            $store_no = Str::between($this->plainText, 'CUSTOMER COPY', 'Date');
            $store_no = Str::before($store_no, 'Reg');
            $store_no = '#' . preg_replace('/[^0-9]/', '', $store_no);
            $this->vendor_store_no = $store_no;
        }
    }

    private function setProducts()
    {
        /**
         * Products Details
         */
        $prods_text = null;

        //TODO: Check with discounted email

        $prods_text = Str::between($this->plainText, ' ------------------------------------------', 'Sub Total');


        $prods_array = explode(':', $prods_text);
        $prods_array = array_values(array_filter($prods_array));

        $prods = [];
        foreach ($prods_array as $key => $value) {
            if (Str::contains($value, 'Base Price')) {

                $prods[] = [
                    'name'          =>  trim(str_replace('Clr', '', preg_replace('/[^A-Za-z ]/', '', $prods_array[$key - 3]))),
                    'price'         =>  floatval(str_replace(['$', ','], '', trim(Str::beforeLast($prods_array[$key + 1], '$')))),
                    'sku'           =>  substr(preg_replace('/[^0-9]/', '', $prods_array[$key - 3]), -12),
                    'quantity'      =>  trim(Str::before($prods_array[$key], 'Base Price')),
                    'description'   =>  'Crl:' . $prods_array[$key - 2] . ' ' . str_replace('Qty', '', $prods_array[$key - 1]) . ' ',
                ];
            }
        }

        $this->products = $prods;
    }

    private function setExtraInfo()
    {

        $this->plainText = str_replace('--tagend--', '', $this->plainText);

        $type = Str::between($this->plainText, 'TYPE', 'ACCT');
        $type = trim($type);

        $account_type = Str::between($this->plainText, 'Payments', 'TYPE');
        $account_type = preg_replace('/[^A-Za-z]/', '', $account_type);

        $card_number = Str::between($this->plainText, 'CARD NUMBER', 'DATE/TIME');
        $card_number = trim($card_number);
        $card_number = '*' . str_replace('*', '', $card_number);

        $auth_number = Str::between($this->plainText, 'AUTH #', 'VISA CREDIT');
        $auth_number = trim($auth_number);

        $taxes = Str::between($this->plainText, 'Sub Total', 'Total');

        if (Str::contains($taxes, 'ONFedHST')) {
            $fed_hst_percent = Str::between($taxes, 'FedHST', '% ');
            $fed_hst_percent = trim(Str::before($fed_hst_percent, '%')) . '%';

            $fed_hst_value = trim(Str::between($taxes, 'ONFedHST ' . $fed_hst_percent, 'ONProvHST '));
        }

        if (Str::contains($taxes, 'ONProvHST')) {
            $prov_hst_percet = trim(Str::between($taxes, 'ONProvHST ', '$'));

            $prov_hst_value = trim(Str::after($taxes, $prov_hst_percet));
        }


        $total_tax = preg_replace('/[^0-9.]/', '', $fed_hst_value) + preg_replace('/[^0-9.]/', '', $prov_hst_value);
        $tax_hst = '$' . $total_tax;
        $this->tax_amount = floatval(str_replace(['$', ','], '', $tax_hst));

        $ex_info = [];
        if ($type) {
            $ex_info[] = [
                'label'     =>      "Transaction Type",
                'value'     =>       $type,
                'key'       =>      'transaction_type',
                'type'      =>      'invoice_type'
            ];
        }

        if ($account_type) {
            $ex_info[] = [
                'label'      =>      'Account Type',
                'value'     =>       $account_type,
                'key'       =>      'account_type',
                'type'      =>      'account_details'
            ];
        }

        if ($card_number) {
            $ex_info[] = [
                'label'      =>      'Card Number',
                'value'     =>       $card_number,
                'key'       =>      'card_number',
                'type'      =>      'account_details'
            ];
        }


        if ($auth_number) {
            $ex_info[] = [
                'label'      =>      'Auth Number',
                'value'     =>       $auth_number,
                'key'       =>      'auth_number',
                'type'      =>      'account_details'
            ];

            $this->auth_no = $auth_number;
        }

        if ($fed_hst_percent) {
            $ex_info[] = [
                'label'      =>     'Fed HST ' . $fed_hst_percent,
                'value'     =>       $fed_hst_value,
                'key'       =>      'fed_hst_' . $fed_hst_percent,
                'type'      =>      'taxes'
            ];
        }

        if ($prov_hst_percet) {
            $ex_info[] = [
                'label'      =>     'Prov HST ' . $prov_hst_percet,
                'value'     =>       $prov_hst_value,
                'key'       =>      'prov_hst_' . $prov_hst_percet,
                'type'      =>      'taxes'
            ];
        }


        $this->extra_info = collect($ex_info)->toJson();
    }

    private function setTransaction()
    {
        $this->plainText = str_replace('--tagend--', '', $this->plainText);
        $total =  Str::between($this->plainText, 'Total', 'Payments');
        $total =  Str::afterLast($total, 'Total');
        $total = preg_replace('/[^0-9$.,]/', '', $total);

        $sub_total = trim(Str::between($this->plainText, 'Sub Total', 'ONFedHST'));

        $payment_method = trim(Str::between($this->plainText, 'ACCT', 'AMOUNT'));

        $card_number = Str::between($this->plainText, 'CARD NUMBER', 'DATE/TIME');
        $card_number = trim($card_number);
        $card_number = '*' . str_replace('*', '', $card_number);

        $payment_ref = Str::between($this->plainText, 'REFERENCE #', 'TERMINAL #');
        $payment_ref = trim($payment_ref);
        $terminal_no = Str::between($this->plainText, 'TERMINAL #', 'AUTH #');

        $terminal_no = substr($terminal_no, -2, 2);

        $this->sub_total = floatval(str_replace(['$', ','], '', $sub_total));
        $this->total = floatval(str_replace(['$', ','], '', $total));
        $this->payment_ref = $payment_ref;
        $this->terminal_no = $terminal_no;
        $this->payment_method = $payment_method . ' ' . $card_number;

        $textArray =  preg_grep('/HST #:/', $this->textArray);
        if ($textArray) {
            $hst = trim(Str::after(implode($textArray), 'HST #:'));
            $this->vendor_hst = $hst;
        }
        if ($textArray) {
            $br_code = trim(Str::before(implode($textArray), 'HST #:'));
            $this->br_code = $br_code;
        }


        $this->setExtraInfo();
    }

    public function parseEmail()
    {
        try {

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
                'store_no' => $this->vendor_store_no,
                'Hst' => $this->vendor_hst,
                'street_name' => $this->vendor_street_name,
                'unit' => $this->vendor_unit,
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
            'auth_id' => $this->auth_no,
            'payment_ref' => $this->payment_ref,
            'bar_qr_code' => $this->br_code,
            'payment_method' => $this->payment_method,
            'message_id' => $this->message_id,
            'extra_info' => $this->extra_info,
            'terminal_no' => $this->terminal_no

        ];
    }
    public function getDetail()
    {

        return $this->detail;
    }
}

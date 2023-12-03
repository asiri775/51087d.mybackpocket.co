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

class Ikea
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
    private $payment_ref = null;
    private $payment_method = null;
    private $extra_info = null;
    private $cus_address = null;
    private $cus_street_name = null;
    private $cus_city = null;
    private $cus_state = null;
    private $cus_zip_code = null;
    private $cus_email = null;
    private $cus_phone = null;

    public function __construct($htmlBody, $plainText, $emailDate, $sender, $message_id)
    {
        $this->htmlBody = $htmlBody;
        $this->plainText = $plainText;
        $this->emailDate = $emailDate;
        $this->message_id = $message_id;

        $this->vendor_name = "Ikea";
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

        if (Str::contains(Str::lower($this->plainText), Str::lower("Thank you for your IKEA order"))) { //If it is not order/invoice then skip it.
            return true;
        }
        return false;
    }

    private function setOrderNo()
    {
        $order_no = null;
        if (Str::contains($this->plainText, "Thank you for your IKEA order!")) {

            $order_no = preg_grep('/Order number/i', $this->textArray);
            $oKey = array_keys($order_no);
            $order_no = $this->textArray[$oKey[0] + 1];
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
        $date_text = null;

        $plainText = str_replace('--tagend--', '', $this->plainText);

        if (Str::contains(Str::lower($this->plainText), Str::lower("Order Information"))) {

            //TODO: check if there are more than forwarded messages
            $dates = preg_grep('/^Order date/', $this->textArray);
            $dKeys = array_keys($dates);
            $date_text = $this->textArray[$dKeys[0] + 1];

            $date_text =  Carbon::parse($date_text);
            $this->emailDate = $date_text;

            //If string contains fails then use the default email address

        }

        if (Str::contains($plainText, 'IKEA Customer Service')) {

            if (Str::contains($this->htmlBody, 'IKEA order &lt;')) {
                $vendorEmailStr = Str::between($this->htmlBody, "IKEA order &lt;", "@ikea.com");
                $this->vendor_email = strip_tags($vendorEmailStr) . '@ikea.com';
            } else {
                $this->vendor_email = "do-not-reply@ikea.com";
            }
        }

        $vendorStr = preg_grep('/^Delivery address/i', $this->textArray);
        $vendorKeys = array_keys($vendorStr);
        $vendorAddress = '';
        for ($i = 2; $i <= 6; $i++) {
            $vendorAddress .= $this->textArray[$vendorKeys[0] + $i] . ' ';
        }

        $vendorAddress = trim($vendorAddress);
        $this->vendor_address = $vendorAddress;

        $zipcode = Str::afterLast($vendorAddress, ' ');
        $vendorAddress = Str::beforeLast($vendorAddress, ' ');
        $state = Str::afterLast($vendorAddress, ' ');
        $vendorAddress = Str::beforeLast($vendorAddress, ' ');
        $city = Str::afterLast($vendorAddress, ' ');
        $vendorAddress = Str::beforeLast($vendorAddress, ' ');
        $unit = Str::afterLast($vendorAddress, ' ');
        $streetName = $vendorAddress;

        $this->vendor_street_name = $vendorAddress;
        $this->vendor_unit = $unit;
        $this->vendor_city = $city;
        $this->vendor_state = $state;
        $this->vendor_zip_code = $zipcode;
    }

    private function setProducts()
    {
        /**
         * Products Details
         */
        $prods_text = null;

        //TODO: Check with discounted email
        $text = preg_grep('/Article no: /', $this->textArray);

        $prods_keys = array_keys($text);

        $i = 0;
        $prods = [];
        foreach ($prods_keys as $key) {
            $i++;
            $prods[] = [
                'name'          =>  $this->textArray[$key - 2] . ' ' . Str::before($this->textArray[$key - 1], '$'),
                //'sku'         =>  preg_replace('/[^0-9]/', '' ,$this->textArray[$key]),
                'price'         =>  floatval(Str::after($this->textArray[$key + 2], '$')),
                'quantity'      =>  preg_replace('/[^0-9]/', '', $this->textArray[$key + 1]),
                'description'   =>  str_replace('Article', ' Article', $this->textArray[$key])
            ];
        }


        if ($this->getDiscountIndex()) {
            $prods_text = Str::between($this->plainText, "x1", "Discount");
        }

        $this->products = $prods;
    }

    private function setExtraInfo()
    {
        $this->plainText = str_replace('--tagend--', '', $this->plainText);
        $delivery_types = preg_grep('/^Estimated delivery date/i', $this->textArray);
        $dKeys = array_keys($delivery_types);
        foreach ($dKeys as $key) {
            $delivery_type[] = $this->textArray[$key - 1];
        }

        $delivery_timings = preg_grep('/^Estimated delivery date/i', $this->textArray);
        $dtKeys = array_keys($delivery_timings);
        foreach ($dtKeys as $key) {
            $delivery_timing[] = Str::after($this->textArray[$key], 'date:');
        }

        $account_type = preg_grep('/^Payment with/i', $this->textArray);
        $account_type = implode($account_type);
        $account_type = Str::between($account_type, 'Payment with ', '*');
        $account_type = str_replace('*', '', $account_type);

        $card_number = preg_grep('/^Payment with/i', $this->textArray);
        $card_number = implode($card_number);
        $card_number = Str::after($card_number, '*');
        $card_number = '*' . str_replace('*', '', $card_number);

        $dates = preg_grep('/^Order date/i', $this->textArray);
        $dateKeys = array_keys($dates);
        $order_date = $this->textArray[$dateKeys[0] + 1];
        $order_date = Str::before($order_date, ' ');

        $delivery_charge = preg_grep('/^Delivery charge/', $this->textArray);
        $dKeys = array_keys($delivery_charge);
        $delivery_charge = $this->textArray[$dKeys[0] + 1];

        $hst = preg_grep('/^HST - /i', $this->textArray);
        $hst = implode($hst);
        $hst_percent = Str::after($hst, 'HST - ');

        $hstValue = preg_grep('/^HST - /i', $this->textArray);
        $hKeys = array_keys($hstValue);
        $hst_value = $this->textArray[$hKeys[0] + 2];


        $ex_info = [];
        if ($delivery_type) {
            $i = 0;
            foreach ($delivery_type as $type) :
                $i + 1;
                $ex_info[] = [
                    'label'     =>      $type,
                    'value'     =>      $delivery_timing[$i],
                    'key'       =>      strtolower(str_replace(' ', '_', $type)),
                    'type'      =>      'delivery_type'
                ];
            endforeach;
        }

        if ($card_number) {
            $ex_info[] = [
                'label'     =>      'Card Number',
                'value'     =>       $card_number,
                'key'       =>      'card_number',
                'type'      =>      'account'
            ];
        }


        if ($account_type) {
            $ex_info[] = [
                'label'      =>      'Account Type',
                'value'     =>       $account_type,
                'key'       =>      'account_type',
                'type'      =>      'account'
            ];
        }

        if ($order_date) {
            $ex_info[] = [
                'label'     =>      'Order Date',
                'value'     =>       $order_date,
                'key'       =>      'order_date',
                'type'      =>      'date'
            ];
        }

        if ($hst_percent) {
            $ex_info[] = [
                'label'     =>      'HST ' . $hst_percent,
                'value'     =>      floatval(str_replace('$', '', $hst_value)),
                'key'       =>      'hst_' . $hst_percent,
                'type'      =>      'amount'
            ];
        }

        if ($delivery_charge) {
            $ex_info[] = [
                'label'     =>      'Delivery Charge',
                'value'     =>       floatval(str_replace('$', '', $delivery_charge)),
                'key'       =>      'delivery_charge',
                'type'      =>      'amount'
            ];
        }


        $this->extra_info = collect($ex_info)->toJson();
    }

    private function setTransaction()
    {
        $totalWithTax = preg_grep('/^Total including Tax/i', $this->textArray);
        $totKeys = array_keys($totalWithTax);
        $total = $this->textArray[$totKeys[0] + 1];

        $hstTax = preg_grep('/^HST - /i', $this->textArray);
        $hKey = array_keys($hstTax);
        $tax_hst = $this->textArray[$hKey[0] + 2];


        $subTotalStr = preg_grep('/^Subtotal before delivery/i', $this->textArray);

        $subKeys = array_keys($subTotalStr);

        $sub_total = $this->textArray[$subKeys[0] + 1];
        $sub_total = preg_replace('/[^0-9. ]/', '', $sub_total);
        $delivery_charges = preg_grep('/^Delivery charge/', $this->textArray);
        $dKeys = array_keys($delivery_charges);
        $delivery_charges = preg_replace('/[^0-9. ]/', '', $this->textArray[$dKeys[0] + 1]);

        $subTotal_withDelivery_charges = $sub_total + $delivery_charges;
        $subtotal = number_format($subTotal_withDelivery_charges, 2, '.', ',');
        $sub_total = $subtotal;
        //return $this->plainText;
        $payMethod = preg_grep('/^Payment with/i', $this->textArray);
        $payMethod = implode($payMethod);
        $payMethod = Str::between($payMethod, 'with', '*');
        $payMethod = preg_replace('/[^A-Za-z\/ ]/', '', $payMethod);
        $payment_method = $payMethod;

        $paymentRef = preg_grep('/^Payment with/i', $this->textArray);
        $paymentRef = preg_replace('/[^0-9,.]/', '', $paymentRef);
        $paymentRef = '*' . implode($paymentRef);

        /* **********
        $total_label_index = array_search('Total (CAD)', $this->textArray);
        $tax_label_index = array_search('HST', $this->textArray);
        $sub_total_index = array_search('Food/Beverage Total', $this->textArray); 
        

        $payment_method = $this->textArray[$total_label_index + 2];
        $payment_method = str_replace("Paid with ", '', $payment_method);

        ********** */

        $this->sub_total = floatval(str_replace(['$', ','], '', $sub_total)); // $this->textArray[$sub_total_index + 1];
        $this->tax_amount = floatval(str_replace(['$', ','], '', $tax_hst)); //$this->textArray[$tax_label_index + 1];
        $this->total = floatval(str_replace(['$', ','], '', $total)); //$this->textArray[$total_label_index + 1];
        $this->payment_method = $payment_method;
        $this->payment_ref = $paymentRef;

        $cus_address_index = array_search('Delivery address', $this->textArray);
        if ($cus_address_index) {
            $cus_address = $this->textArray[$cus_address_index + 2] . ' ' . $this->textArray[$cus_address_index + 3] . ' ' . $this->textArray[$cus_address_index + 4] . ' ' . $this->textArray[$cus_address_index + 5] . ' ' . $this->textArray[$cus_address_index + 6];
            $this->cus_address = $cus_address;
            $this->cus_street_name = $this->textArray[$cus_address_index + 2] . ' ' . $this->textArray[$cus_address_index + 3];
            $this->cus_city = $this->textArray[$cus_address_index + 4];
            $this->cus_state = $this->textArray[$cus_address_index + 5];
            $this->cus_zip_code = $this->textArray[$cus_address_index + 6];
            $this->cus_email = $this->textArray[$cus_address_index + 7];
        }




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
            'payment_ref' =>    $this->payment_ref,
            'payment_method' => $this->payment_method,
            'message_id' => $this->message_id,
            'extra_info' => $this->extra_info,
            'cus_address' => $this->cus_address,
            'cus_street_name' =>  $this->cus_street_name,
            'cus_city' =>  $this->cus_city,
            'cus_state' =>  $this->cus_state,
            'cus_zip_code' =>  $this->cus_zip_code,
            'cus_email' =>  $this->cus_email,
        ];
    }
    public function getDetail()
    {

        return $this->detail;
    }
}

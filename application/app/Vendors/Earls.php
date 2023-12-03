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

class Earls
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
    private $payment_method = null;
    private $payment_ref = null;
    private $extra_info = null;

    public function __construct($htmlBody, $plainText, $emailDate, $sender, $message_id)
    {
        $this->htmlBody = $htmlBody;
        $this->plainText = $plainText;
        $this->emailDate = $emailDate;
        $this->message_id = $message_id;

        $this->vendor_name = "Earls";
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
        if (Str::contains(Str::lower($this->plainText), Str::lower("Transaction ID"))) { //If it is not order/invoice then skip it.
            return true;
        }
        return false;
    }

    private function setOrderNo()
    {
        $order_no = null;
        $this->textCollection->filter(function ($value, $key) use (&$order_no) {
            if (Str::contains($value, "Transaction ID")) {
                $order_no = trim(Str::after($value, "Transaction ID"));
            }
        });

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
        return array_search('Discount ', $this->textArray);
    }

    private function setDiscount()
    {
        if ($this->getDiscountIndex()) {
            $this->discount = $this->textArray[$this->getDiscountIndex() + 1];
        }
    }

    public function setVendor()
    {
        //If email is forwarded mail
        if (Str::contains(Str::lower($this->plainText), Str::lower("Forwarded message"))) {

            $date_text = Str::between($this->htmlBody, "@xpotech.net", "Subject");

            $date_text = preg_replace("/\r|\n|\t/", "", $date_text);
            $date_text = trim(preg_replace('/\s+/', ' ', $date_text));

            $date_text = strip_tags(Str::after($date_text, "Date:"));
            $date_text = date('Y-m-d', strtotime($date_text));

            $timeNearTotalText = Str::between($this->htmlBody, "Total @", "----");
            $timeNearTotalText = trim($timeNearTotalText);
            $timeNearTotalText = explode(' ', $timeNearTotalText);
            $timeNearTotalText = $timeNearTotalText[0];

            $date_text = $date_text . " " . $timeNearTotalText;
            $date_text = date('Y-m-d H:i:s', strtotime($date_text));

            $this->emailDate = Carbon::parse($date_text);
            //If string contains fails then use the default email address

            $this->vendor_email = "no-reply@xpotech.net";
        }

        if (Str::contains($this->plainText, "EARLS RESTAURANTS")) {

            $this->vendor_address = trim(str_replace('--tagend--', '', Str::between($this->plainText, "EARLS RESTAURANTS", "Tbl")));
            $this->vendor_address = Str::after($this->vendor_address, 'Earls Sherway');
            $this->vendor_address = preg_replace('!\s+!', ' ', $this->vendor_address);
            $this->vendor_address = str_replace('-', '', $this->vendor_address);
            $this->vendor_address = trim($this->vendor_address);

            $vendor_state = Str::after($this->vendor_address, ',');
            $vendor_city = Str::before($this->vendor_address, ',');

            $this->vendor_zip_code = trim(Str::after(Str::between($this->vendor_address, ',', '('), ' '));
            $this->vendor_zip_code = explode(' ', $this->vendor_zip_code);
            unset($this->vendor_zip_code[0]);
            $this->vendor_zip_code = trim(implode(' ', $this->vendor_zip_code));

            $city = preg_replace('/(?<!\ )[A-Z]/', ' $0', $vendor_city);

            $this->vendor_city = Str::afterLast($city, ' ');
            $this->vendor_street_name = trim(Str::beforeLast($city, ' '));
        }
    }

    public function setProducts()
    {
        /**
         * Products Details
         */
        $prods_text = null;
        //TODO: Check with discounted email
        $prods_text = Str::between($this->plainText, "Tbl", "SUBTOTAL");
        $prods_text = Str::after($prods_text, "----------------------------");

        $prods_array = explode('--tagend--', $prods_text);
        $prods_array = array_values(array_filter($prods_array));


        $prods = [];
        foreach ($prods_array as $key => $value) {
            if (Str::contains($value, '.')) {
                $value = explode(' ', $value);
                $valueCp = $value;
                unset($valueCp[0]);
                $valueCp = array_values($valueCp);
                if (count($valueCp) >= 1) {
                    $lastIndex = count($valueCp) - 1;
                    unset($valueCp[$lastIndex]);
                }
                $productName = '';
                if (count($valueCp) > 0) {
                    $productName = implode(' ', $valueCp);
                }
                if ($productName != '') {
                    $prods[] = [
                        'name' => $productName,
                        'quantity' => intval($value[0]),
                        'price' => floatval(end($value))
                    ];
                }
            }
        }

        $this->products = $prods;
    }

    private function setExtraInfo()
    {
        $textArray = array_map('trim', $this->textArray);
        $textArray = array_values(array_filter($textArray));

        $ex_info = [];
        if (is_array($textArray) && count($textArray) > 0) {
            foreach ($textArray as $textItem) {

                if (strpos($textItem, 'Tip') !== false) {
                    $ex_info[] = [
                        'label' => "Tip",
                        'value' => floatval(
                            str_replace(
                                'Tip',
                                '',
                                $textItem
                            )
                        ),
                        'key' => 'tip',
                        'type' => 'amount'
                    ];
                }

                if (strpos($textItem, 'HST Tax') !== false) {
                    $this->tax_amount = floatval(
                        str_replace(
                            'HST Tax',
                            '',
                            $textItem
                        )
                    );
                    $ex_info[] = [
                        'label' => "HST",
                        'value' => $this->tax_amount,
                        'key' => 'hst',
                        'type' => 'amount'
                    ];
                }

            }
        }

        $this->extra_info = collect($ex_info)->toJson();
    }

    public function setTransaction()
    {
        $this->sub_total = floatval(Str::after($this->plainText, 'SUBTOTAL'));
        $this->sub_total = floatval($this->sub_total);

        $this->total = Str::after($this->plainText, 'Transaction ID');
        $this->total = Str::after($this->total, 'Total');
        $this->total = floatval($this->total);

        $payment_method = Str::after($this->plainText, 'Tip');

        $paymentMethodData = explode('--tagend--', $payment_method);
        $paymentMethodData = array_values(array_filter($paymentMethodData));

        if (is_array($paymentMethodData) && array_key_exists(1, $paymentMethodData)) {
            $payment_method = $paymentMethodData[1];
        }

        $payment_method = explode(' ', $payment_method);
        if (is_array($payment_method) && count($payment_method) > 0) {
            $payment_method = $payment_method[0];
        }

        $this->payment_method = $payment_method;


        $reference_no = Str::after($this->plainText, 'Tip');
        $reference_no = Str::between($reference_no, '(', ')');
        $this->payment_ref = $reference_no;

        $this->setExtraInfo();
    }

    public function parseEmail()
    {
        try {

            if (!$this->isInvoice()) return 'not invoice';

            $this->setOrderNo();

            /**
             * Check if the transaction/order already exists then return false stop further
             * proceeding to avoid any duplication
             */
            if ($this->transactionExists()) return 'transaction exist';

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
                'city' => $this->vendor_city,
                'state' => $this->vendor_state,
                'unit' => $this->vendor_unit,
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
            'payment_method' => $this->payment_method,
            'payment_ref' => $this->payment_ref,
            'message_id' => $this->message_id,
            'extra_info' => $this->extra_info
        ];
    }

    public function getDetail()
    {
        return $this->detail;
    }
}

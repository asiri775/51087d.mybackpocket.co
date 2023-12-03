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

class NordStrom
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
    private $vendor_store = null;
    private $vendor_phone = null;
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

        $this->vendor_name = "NordStrom";
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
        if (Str::contains(Str::lower($this->plainText), Str::lower("Transaction number:"))) { //If it is not order/invoice then skip it.
            return true;
        }
        return false;
    }

    private function setOrderNo()
    {
        $order_no = null;
        $order_no = array_search('Transaction number: ', $this->textArray);
        if ($order_no) {
            $this->order_no = $this->textArray[$order_no + 1];
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
        if (Str::contains(Str::lower($this->plainText), Str::lower("Date:"))) {
            //TODO: check if there are more than forwarded messages

            $date_index = array_search('Date: ', $this->textArray);
            $date_text = $this->textArray[$date_index + 1];
            $date_text_time = str_replace(', at', '', $this->textArray[$date_index + 2]);
            $dateStr = $date_text . $date_text_time;

            $dateStr = preg_replace("/\r|\n|\t/", "", $dateStr);
            $dateStr = trim(preg_replace('/\s+/', ' ', $dateStr));
            $dateStr = strip_tags($dateStr);
            $this->emailDate =  Carbon::parse($dateStr);

            //If string contains fails then use the default email address
            if (Str::contains($this->htmlBody, 'NORDSTROM STORES &lt;')) {
                $vendorEmailStr = Str::between($this->htmlBody, "NORDSTROM STORES &lt;", "@eml.nordstrom.com");
                $this->vendor_email = strip_tags($vendorEmailStr) . '@eml.nordstrom.com';
            } else {
                $this->vendor_email = "nordstrom@eml.nordstrom.com";
            }
        }

        $store_no = array_search('Store number: ', $this->textArray);
        if ($store_no) {
            $this->vendor_store = $this->textArray[$store_no + 1];
        }


        //return $this->plainText;
        $vendor_address = array_search('Nordstrom Sherway Gardens', $this->textArray);
        if ($vendor_address) {
            $vendorAddress = $this->textArray[$vendor_address + 2];
            $this->vendor_address = trim($vendorAddress);
            $this->vendor_street_name = trim(Str::before($this->textArray[$vendor_address + 2], 'Unit'));
            $this->vendor_unit = Str::between($this->textArray[$vendor_address + 2], 'Unit', ',');
            $this->vendor_city = trim(Str::after($this->textArray[$vendor_address + 2], ', '));
            $this->vendor_zip_code = Str::after($this->vendor_city, ' ');
            $this->vendor_city = str_replace($this->vendor_zip_code, '', $this->vendor_city);
            $this->vendor_phone = trim($this->textArray[$vendor_address + 3]);
        }
    }

    private function setProducts()
    {
        /**
         * Products Details
         */
        $prods_text = null;
        //TODO: Check with discounted email
        $prods_text = Str::between($this->plainText, "Returned", "Return subtotal");
        if ($this->getDiscountIndex()) {
            $prods_text = Str::between($this->plainText, "Returned", "Return subtotal");
        }

        $prods_array = explode('--tagend--', $prods_text);
        $prods_array = array_values(array_filter($prods_array));

        $prods = [];
        foreach ($prods_array as $key => $value) {
            if (Str::contains($value, '$')) {
                $prods[] = [
                    'name' => $prods_array[$key - 5],
                    'price' => floatval(str_replace(['Price: ', '$'], '', $value)),
                    'description' => $prods_array[$key - 3] . $prods_array[$key - 2] . ' ' . $prods_array[$key - 1]
                ];
            }
        }
        $this->products = $prods;
    }

    private function setExtraInfo()
    {


        $tax_label_index = array_search('GST/HST', $this->textArray);

        $ex_info = [];
        if ($tax_label_index) {
            $ex_info[] = [
                'label' => "GST/HST",
                'value' => floatval(str_replace('$', '', $this->textArray[$tax_label_index + 5])),
                'key' => 'gst_hst',
                'type' => 'amount'
            ];
        }

        $this->extra_info = collect($ex_info)->toJson();
    }

    private function setTransaction()
    {
        $sub_total_index = array_search('Return subtotal', $this->textArray);
        $tax_label_index = array_search('GST/HST', $this->textArray);
        $total_label_index = array_search('Return total', $this->textArray);
        $payment_method = array_search('APPL', $this->textArray);
        $payment_ref = array_search('AID', $this->textArray);

        $this->sub_total = floatval(str_replace('$', '', $this->textArray[$sub_total_index + 5]));
        $this->tax_amount = floatval(str_replace('$', '', $this->textArray[$tax_label_index + 5]));
        $this->total = floatval(str_replace('$', '', $this->textArray[$total_label_index + 5]));
        $this->payment_method = $this->textArray[$payment_method + 1];
        $this->payment_ref = $this->textArray[$payment_ref + 1];
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
                'store_no' => $this->vendor_store,
                'phone' => $this->vendor_phone,
                'street_name' => $this->vendor_street_name,
                'unit'  => $this->vendor_unit,
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

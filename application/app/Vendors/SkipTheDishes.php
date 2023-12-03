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

class SkipTheDishes
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
    private $extra_info = null;

    public function __construct($htmlBody, $plainText, $emailDate, $sender, $message_id)
    {
        $this->htmlBody = $htmlBody;
        $this->plainText = $plainText;
        $this->emailDate = $emailDate;
        $this->message_id = $message_id;

        $this->vendor_name = "SkipTheDishes";
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
        if (Str::contains(Str::lower($this->plainText), Str::lower("Order #"))) { //If it is not order/invoice then skip it.
            return true;
        }
        return false;
    }

    private function setOrderNo()
    {
        $order_no = null;
        $this->textCollection->filter(function ($value, $key) use (&$order_no) {
            if (Str::contains($value, "ORDER #")) {
                $order_no = Str::after($value, "ORDER #");
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
            //TODO: check if there are more than forwarded messages
            $date_text = Str::between($this->htmlBody, "@skipthedishes.com", "Subject");

            $date_text = preg_replace("/\r|\n|\t/", "", $date_text);
            $date_text = trim(preg_replace('/\s+/', ' ', $date_text));

            $date_text = strip_tags(Str::after($date_text, "Date:"));

            $this->emailDate =  Carbon::parse($date_text);

            //If string contains fails then use the default email address

            $this->vendor_email = "order@skipthedishes.com";
        }

        if (Str::contains($this->plainText, "Our mailing address is:")) {
            $this->vendor_address = str_replace('--tagend--', '', Str::after($this->plainText, "Our mailing address is:"));

            $vendor_state =  Str::afterLast($this->vendor_address, ',');
            $vendor_city =  Str::beforeLast($this->vendor_address, ',');
            $this->vendor_zip_code = trim(str_replace('Canada', '', $vendor_state));
            $city = preg_replace('/(?<!\ )[A-Z]/', ' $0', $vendor_city);
            $this->vendor_city =  Str::afterLast($city, ' ');
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
        $prods_text = Str::between($this->plainText, "Placed for Delivery ASAP", "Food/Beverage Total");
        if ($this->getDiscountIndex()) {
            $prods_text = Str::between($this->plainText, "Placed for Delivery ASAP", "Discount");
        }

        $prods_array = explode('--tagend--', $prods_text);
        $prods_array = array_values(array_filter($prods_array));


        $prods = [];
        foreach ($prods_array as $key => $value) {
            if (Str::contains($value, '$')) {
                $prods[] = [
                    'name' => $prods_array[$key - 1],
                    'quantity' => intval(str_replace('x', '', $prods_array[$key - 2])),
                    'price' => floatval(str_replace(['CAD', 'CA', '$', ','], '', $value))
                ];
            }
        }

        $this->products = $prods;
    }

    private function setExtraInfo()
    {
        $textArray = array_map('trim', $this->textArray);
        $textArray = array_values(array_filter($textArray));
        $delivery_fee_label_index = array_search('Delivery Fee', $textArray);
        $tip_label_index = array_search('Tip the Food Courier', $textArray);
        $discount_index = array_search('Discount', $textArray);
        $tax_label_index = array_search('HST', $textArray);

        $ex_info = [];
        if ($delivery_fee_label_index) {
            $ex_info[] = [
                'label' => "Delivery Fee",
                'value' => floatval(
                    str_replace(
                        ['CAD', 'CA', '$', ','],
                        '',
                        $textArray[$delivery_fee_label_index + 1]
                    )
                ),
                'key' => 'delivery_fee',
                'type' => 'amount'
            ];
        }

        if ($delivery_fee_label_index) {
            $ex_info[] = [
                'label' => "HST",
                'value' => floatval(
                    str_replace(
                        ['CAD', 'CA', '$', ','],
                        '',
                        $textArray[$tax_label_index + 1]
                    )
                ),
                'key' => 'hst',
                'type' => 'amount'
            ];
        }

        if ($tip_label_index) {
            $ex_info[] = [
                'label' => "Tip the Food Courier",
                'value' => floatval(
                    str_replace(
                        ['CAD', 'CA', '$', ','],
                        '',
                        $textArray[$tip_label_index + 1]
                    )
                ),
                'key' => 'tip',
                'type' => 'amount'
            ];
        }

        if ($discount_index) {
            $ex_info[] = [
                'label' => "Discount",
                'value' => floatval(
                    str_replace(
                        ['CAD', 'CA', '$', ','],
                        '',
                        $textArray[$discount_index + 1]
                    )
                ),
                'key' => 'discount',
                'type' => 'amount'
            ];
        }

        $this->extra_info = collect($ex_info)->toJson();
    }

    public function setTransaction()
    {

        $textArray = array_map('trim', $this->textArray);
        $textArray = array_values(array_filter($textArray));

        $total_label_index = array_search('Total (CAD)', $textArray);

        $sub_total_index = array_search('Food/Beverage Total', $textArray);

        $payment_method = $textArray[$total_label_index + 2];
        $payment_method = str_replace("Paid with", '', $payment_method);

        $this->sub_total = floatval(str_replace(['CAD', 'CA', '$', ','], '', $this->textArray[$sub_total_index + 1]));

        $this->total = floatval(str_replace(['CAD', 'CA', '$', ','], '', $textArray[$total_label_index + 1]));
        $this->payment_method = $payment_method;
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
                'city' => $this->vendor_city,
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
            'message_id' => $this->message_id,
            'extra_info' => $this->extra_info
        ];
    }

    public function getDetail()
    {
        return $this->detail;
    }
}

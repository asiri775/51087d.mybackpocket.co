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

class Dominos
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
    private $extra_info = null;
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

        $this->vendor_name = "Dominos";
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
        if (Str::contains($this->htmlBody, 'Order #:')) {
            $this->order_no = trim(strip_tags(Str::between($this->htmlBody, "Order #:", "Date:")));
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
        if (Str::contains(Str::lower($this->plainText), Str::lower("Date:"))) {
            //TODO: check if there are more than forwarded messages

            $date_text = preg_grep('/The following order is being delivered/', $this->textArray);
            $date_text = Str::before(implode($date_text), 'The following order is');
            $date_text = preg_replace("/\r|\n|\t/", "", $date_text);
            $date_text = trim(preg_replace('/\s+/', ' ', $date_text));
            $date_text = strip_tags($date_text);

            $this->emailDate =  Carbon::parse($date_text);
            //If string contains fails then use the default email address

            if (Str::contains($this->htmlBody, 'orders@dominos.ca')) {
                $this->vendor_email = "orders@dominos.ca";
            } else {
                $this->vendor_email = "orders@dominos.ca";
            }
        }


        if (Str::contains($this->plainText, "Your Domino's Store")) {
            $vendor_address = str_replace('--tagend--', '', Str::after($this->plainText, "Your Domino's Store"));
            $vendor_address = trim(Str::before($vendor_address, "Delivery Time:"));

            $vendor_address = trim(Str::after($vendor_address, ":"));
            $this->vendor_phone = trim(Str::after($vendor_address, "|"));
            $this->vendor_address = trim(Str::before($vendor_address, "|"));

            $vendor_address = explode(' ', $vendor_address);
            $this->vendor_street_name = trim($vendor_address[0] . ' ' . $vendor_address[1] . ' ' . $vendor_address[2]);
            $this->vendor_unit = trim($vendor_address[3] . ' ' . $vendor_address[4]);
            $this->vendor_city = trim($vendor_address[5]);
            $this->vendor_state = trim($vendor_address[6]);
            $this->vendor_zip_code = trim($vendor_address[7]);
        }

        if (Str::contains($this->plainText, "Your Domino's Store (")) {
            $vendor_store_no = str_replace('--tagend--', '', Str::after($this->plainText, "Your Domino's Store ("));
            $vendor_store_no = trim(Str::before($vendor_store_no, ")"));
            $this->vendor_store_no = $vendor_store_no;
        }
    }

    private function setProducts()
    {
        /**
         * Products Details
         */
        $prods_text = null;

        //TODO: Check with discounted email


        $prods_text = Str::between($this->plainText, "Amount", "Food & Bev Total:");
        if ($this->getDiscountIndex()) {
            $prods_text = Str::between($this->plainText, "Amount", "Discount");
        }

        $prods_array = explode('--tagend--', $prods_text);
        //$prods_array = array_map('trim', $prods_array);
        $prods_array = array_values(array_filter($prods_array));
        $prods = [];
        foreach ($prods_array as $key => $value) {
            if (Str::contains($value, '$')) {
                $prods[] = [
                    'name' => ($prods_array[$key - 4] != 1) ? $prods_array[$key - 4] : ''  . $prods_array[$key - 3],
                    'price' => floatval(str_replace('$', '', $value)),
                    'description' => $prods_array[$key - 1]
                ];
            }
        }
        $this->products = $prods;
    }

    private function setExtraInfo()
    {

        $delivery_fee_label_index = array_search('Delivery:', $this->textArray);


        $tax_label_index = array_search('Tax:', $this->textArray);
        $bottle_index = array_search('Bottle Amount:', $this->textArray);
        $ex_info = [];
        if ($delivery_fee_label_index) {
            $ex_info[] = [
                'label' => "Delivery Fee",
                'value' => floatval(
                    str_replace(
                        '$',
                        '',
                        $this->textArray[$delivery_fee_label_index + 1]
                    )
                ),
                'key' => 'delivery_fee',
                'type' => 'amount'
            ];
        }

        if ($tax_label_index) {
            $ex_info[] = [
                'label' => "Tax",
                'value' => floatval(
                    str_replace(
                        '$',
                        '',
                        $this->textArray[$tax_label_index + 1]
                    )
                ),
                'key' => 'tax',
                'type' => 'amount'
            ];
        }

        if ($bottle_index) {
            $ex_info[] = [
                'label' => "Bottle Amount",
                'value' => floatval(
                    str_replace(
                        '$',
                        '',
                        $this->textArray[$bottle_index + 1]
                    )
                ),
                'key' => 'bottle_amount',
                'type' => 'amount'
            ];
        }

        $this->extra_info = collect($ex_info)->toJson();
    }

    public function setTransaction()
    {
        $sub_total_index = array_search('Food & Bev Total:', $this->textArray);
        $tax_label_index = array_search('Tax:', $this->textArray);
        $total_amount = preg_grep('/^Total:\s.*/', $this->textArray);
        $total_amount = trim(str_replace('Total:', '', implode($total_amount)));
        $payment_method_index = array_search('Payment Method:', $this->textArray);
        $delivery_address_index = preg_grep('/Delivery Address:/', $this->textArray);
        $delivery_address = $this->textArray[key($delivery_address_index) + 1];
        $delivery_address = trim(Str::before($delivery_address, 'Callback'));
        $delivery_address = explode(',', $delivery_address);

        $this->cus_street_name = trim($delivery_address[0]);
        $this->cus_city = trim($delivery_address[1]);
        $split_add = explode(' ', trim($delivery_address[2]));
        $this->cus_state = trim($split_add[0]);
        $this->cus_zip_code = trim($split_add[1]);

        $cus_phone = $this->textArray[key($delivery_address_index) + 2];
        $cus_phone = trim(Str::before($cus_phone, 'Your Domi'));
        $this->cus_phone = $cus_phone;

        $this->sub_total = floatval(str_replace('$', '', $this->textArray[$sub_total_index + 1]));
        $this->tax_amount = floatval(str_replace('$', '', $this->textArray[$tax_label_index + 1]));
        $this->total = floatval(str_replace('$', '', $total_amount));
        $payment_method = explode('$', $this->textArray[$payment_method_index + 1]);
        if (Str::contains(Str::lower($payment_method[0]), Str::lower("cash"))) {
            $this->payment_method = 'Cash';
        } else if (Str::contains(Str::lower($payment_method[0]), Str::lower("debit"))) {
            $this->payment_method = 'Debit Card';
        } else if (Str::contains(Str::lower($payment_method[0]), Str::lower("credit"))) {
            $this->payment_method = 'Credit Card';
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

            // /**
            //  * Set Transaction properties required for DB
            //  */
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
                'phone' => $this->vendor_phone,
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
            'payment_method' => $this->payment_method,
            'message_id' => $this->message_id,
            'extra_info' => $this->extra_info,
            'cus_street_name' =>  $this->cus_street_name,
            'cus_city' =>  $this->cus_city,
            'cus_state' =>  $this->cus_state,
            'cus_zip_code' =>  $this->cus_zip_code,
            'cus_email' =>  $this->cus_email,
            'cus_phone' =>  $this->cus_phone,

        ];
    }

    public function getDetail()
    {
        return $this->detail;
    }
}

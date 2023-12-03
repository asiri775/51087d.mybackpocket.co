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

class Bell
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

        $this->vendor_name = "Bell";
        $this->sender = $sender;
        $this->vendor_email = $this->sender->mail;

        $this->plainTextToArray();
        $this->setOrderNo();
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
        if (Str::contains(Str::lower($this->plainText), Str::lower("Confirmation number"))) { //If it is not order/invoice then skip it.
            return true;
        }
        return false;
    }

    private function setOrderNo()
    {
        $order_nos = null;
        $textArray = array_map('trim', $this->textArray);
        $textArray = array_values(array_filter($textArray));
        $order_nos = preg_grep('/Confirmation number/', $textArray);
        //return $order_nos;
        $get_order = [];
        if ($order_nos) {
            foreach ($order_nos as $key => $order_no) {
                $get_order[] = $textArray[$key + 1];
            }

            $this->order_no = $get_order;
        }
    }

    private function transactionExists()
    {
        foreach ($this->order_no as $order) {
            $transaction_exists = Transaction::where('order_no', $order)->exists();
            if ($transaction_exists) {
                return true;
            }
            return false;
        }
    }

    public function setVendor()
    {
        //If email is forwarded mail
        $textArray = array_map('trim', $this->textArray);
        $textArray = array_values(array_filter($textArray));

        if (Str::contains(Str::lower($this->plainText), Str::lower("Transaction date"))) {
            //TODO: check if there are more than forwarded messages
            $date_text = preg_grep('/Transaction date/', $textArray);
            $date_text = $textArray[key($date_text) + 1];

            $date_text = preg_replace("/\r|\n|\t/", "", $date_text);
            $date_text = trim(preg_replace('/-\s+/', ' ', $date_text));
            $date_text = strip_tags(str_replace('(EST)', '', $date_text));


            $this->emailDate =  Carbon::parse($date_text);

            //If string contains fails then use the default email address
            if (Str::contains($this->htmlBody, 'Bell &lt;')) {
                $vendorEmailStr = Str::between($this->htmlBody, 'Bell &lt;', "@bell.ca");
                $this->vendor_email = strip_tags($vendorEmailStr) . '@bell.ca';
            } else {
                $this->vendor_email = "noreply@bell.ca";
            }
        }


        if (Str::contains($this->plainText, "Privacy")) {
            $address = preg_grep('/Corporate Secretary/', $textArray);
            $address_index = array_keys($address)[0];
            $this->vendor_address = '1' . Str::between($textArray[$address_index], '1', 'bell.ca');
            $address_parts = explode(',', $this->vendor_address);
            $this->vendor_street_name = trim($address_parts[0]) . ' ' . trim($address_parts[1]);
            $this->vendor_city = trim($address_parts[2]);
            $this->vendor_state = trim($address_parts[3]);
            $this->vendor_zip_code = trim($address_parts[4]);
        }
    }

    private function setProducts()
    {
        /**
         * Products Details
         */
        $textArray = array_map('trim', $this->textArray);
        $textArray = array_values(array_filter($textArray));

        $bills = preg_grep('/Bill/', $textArray);
        $get_bill = [];
        if ($bills) {
            foreach ($bills as $key => $bill) {
                $get_bill[] = $textArray[$key + 1];
            }
        }

        if ($get_bill) {
            foreach ($this->order_no as $key => $value) {
                $prods[] = [
                    'name' => 'Bill',
                    'price' => $get_bill[$key]
                ];

                $this->products = $prods;
            }
        }
        //TODO: Check with discounted email



    }

    private function setExtraInfo()
    {
        $ex_info = [];

        $extra_info[] = $ex_info;
        $ex_info = null;

        $this->extra_info = collect($extra_info);
    }

    private function setTransaction()
    {
        $textArray = array_map('trim', $this->textArray);
        $textArray = array_values(array_filter($textArray));


        $card_type = array_search('Card type', $textArray);
        $card_number = array_search('Card number', $textArray);
        $expiration_date = array_search('Expiration date', $textArray);

        $your_payments = preg_grep('/Your payment/', $textArray);
        $get_payment = [];
        if ($your_payments) {
            foreach ($your_payments as $key => $your_payment) {
                $get_payment[] = $textArray[$key + 1];
            }
        }

        if ($card_type) {
            $this->payment_method = $textArray[$card_type + 1];
        }
        if ($card_number) {
            $this->payment_ref = '*' . str_replace('*', '', $textArray[$card_number + 1]);
        }
        if ($expiration_date) {
            //$this->tax_amount = $textArray[$expiration_date + 1];
        }

        if ($get_payment) {
            foreach ($this->order_no as $key => $value) {
                $sub_total[] = $get_payment[$key];
                $this->sub_total = $sub_total;
            }
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
        foreach ($this->order_no as $key => $value) {
            $details = [
                'vendor' => [
                    'email' => $this->vendor_email,
                    'name' => $this->vendor_name,
                    'address' => $this->vendor_address,
                    'street_name' => $this->vendor_street_name,
                    'city' => $this->vendor_city,
                    'state' => $this->vendor_state,
                    'zip_code' => $this->vendor_zip_code,
                ]
            ];
            $details['products'][] = $this->products[$key];
            $details['transaction'] = [
                'order_no' => $value,
                'transaction_date' => $this->emailDate->format('Y-m-d H:i:s'),
                'sub_total' => floatval(str_replace(
                    '$',
                    '',
                    $this->sub_total[$key]
                )),
                'total' => floatval(
                    str_replace(
                        '$',
                        '',
                        $this->sub_total[$key]
                    )
                ),
                'payment_method' => $this->payment_method,
                'payment_ref'   => $this->payment_ref,
                'message_id' => $this->message_id,
            ];

            $this->detail[] = $details;
        }
    }

    public function getDetail()
    {
        return $this->detail;
    }
}

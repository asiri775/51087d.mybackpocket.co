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

class Spotify
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

    public function __construct($htmlBody, $plainText, $emailDate, $sender, $message_id)
    {
        $this->htmlBody = $htmlBody;
        $this->plainText = $plainText;
        $this->emailDate = $emailDate;
        $this->message_id = $message_id;

        $this->vendor_name = "Spotify";
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

        if (Str::contains(Str::lower($this->plainText), Str::lower("Your Spotify Premium receipt"))) { //If it is not order/invoice then skip it.
            return true;
        }
        return false;
    }

    private function setOrderNo()
    {
        $order_no = null;
        if (Str::contains($this->plainText, "Your Spotify Premium receipt")) {

            $order_no = preg_grep('/^Order ID:/', $this->textArray);
            $order_no = implode($order_no);
            $order_no = Str::after($order_no, 'Order ID: ');
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
        if (in_array('Discount Applied:', $this->textArray)) {
            return true;
        } else {
            return false;
        }
    }

    private function setDiscount()
    {
        if (in_array('Discount Applied:', $this->textArray)) {
            $discountStr = preg_grep('/^Discount Applied:/', $this->textArray);
            $disKey = array_keys($discountStr);
            $discount = '$' . $this->textArray[$disKey[0] + 2];
            $this->discount = $discount;
        }
    }

    public function setVendor()
    {
        //If email is forwarded mail
        $date_text = null;
        $plainText = str_replace('--tagend--', '',  $this->plainText);
        if (Str::contains(Str::lower($plainText), Str::lower("Your Spotify Premium receipt"))) {

            //TODO: check if there are more than forwarded messages
            $date_text = Str::between($plainText, 'Your Spotify Premium receipt', 'Order ID:');
            $dateStr = preg_grep('/^Order ID:/', $this->textArray);

            $dKeys = array_keys($dateStr);
            $date_text = $this->textArray[$dKeys[0] - 2];

            $this->emailDate = Carbon::parse($date_text);

            //If string contains fails then use the default email address

            if (Str::contains($this->htmlBody, 'Spotify &lt;')) {
                $vendorEmailStr = Str::between($this->htmlBody, "Spotify &lt;", "@spotify.com");
                $this->vendor_email = strip_tags($vendorEmailStr) . '@spotify.com';
            } else {
                $this->vendor_email = "no-reply@spotify.com";
            }
        }

        if (Str::contains($plainText, "Contact Us")) {

            $vendor_address = Str::between($plainText, 'Contact Us', 'VAT:');
            $this->vendor_address = $vendor_address;
            $address = explode(',', $vendor_address);
            $this->vendor_street_name = $address[0] . ' ' . $address[1];
            $this->vendor_city = Str::afterLast($address[2], ' ');
            $this->vendor_state = $address[3];
            $this->vendor_zip_code = Str::beforeLast($address[2], ' ');
        }
    }

    public function setProducts()
    {
        /**
         * Products Details
         */
        $prods_text = null;
        //TODO: Check with discounted email

        $prods_text = Str::between($this->plainText, 'Order ID:', 'Total');
        $prods_array = explode('--tagend--', $prods_text);
        $prods_array = array_values(array_filter($prods_array));

        $prods = [];
        foreach ($prods_array as $key => $value) {
            if (Str::contains($value, 'CAD ')) {
                $prods[] = [
                    'name'          =>  $prods_array[$key - 1],
                    'price'         =>  floatval(trim(preg_replace('/[A-Za-z]/', '', $prods_array[$key]))),
                    'description'   =>  $prods_array[$key + 1],
                ];
            }
        }

        $this->products = $prods;
    }

    private function setExtraInfo()
    {

        $plainText = str_replace('--tagend--', '', $this->plainText);
        $card_number = Str::between($plainText, 'Payment Method', 'Username');
        $card_number = preg_replace('/[^0-9]/', '', $card_number);
        $card_number = '*' . str_replace('*', '', $card_number);

        $username = Str::between($plainText, 'Username', 'You agree');
        $username = trim($username);


        $ex_info = [];
        if ($card_number) {
            $ex_info[] = [
                'label'     =>      "Card Number",
                'value'     =>       $card_number,
                'key'       =>      'card_number',
                'type'      =>      'account_details'
            ];
        }

        if ($username) {
            $ex_info[] = [
                'label'      =>      'Username',
                'value'     =>       $username,
                'key'       =>      'username',
                'type'      =>      'name'
            ];
        }

        $this->extra_info = collect($ex_info)->toJson();
    }

    private function setTransaction()
    {
        $plainText = str_replace('--tagend--', '', $this->plainText);

        $totalStr = preg_grep('/^Total/', $this->textArray);
        $totKeys = array_keys($totalStr);
        $total = $this->textArray[$totKeys[0] + 1];
        $total = preg_replace('/[^0-9.]/', '', $total);

        $tax_hst =  '';
        $sub_total = '';

        $payment_method = Str::between($plainText, 'Payment Method', 'Username');
        $payment_method_no = preg_replace('/[^0-9]/', '', $payment_method);
        $payment_method = preg_replace('/[^A-Za-z]/', '', $payment_method);

        $payment_ref = preg_grep('/^Order ID:/', $this->textArray);
        $payment_ref = implode($payment_ref);
        $payment_ref = Str::after($payment_ref, 'Order ID: ');

        $this->sub_total = $sub_total;
        $this->tax_amount = $tax_hst;
        $this->total = floatval($total);
        $this->payment_ref = $payment_ref;
        $this->payment_method = $payment_method . ' #' . $payment_method_no;

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
            'payment_ref'   => $this->payment_ref,
            'message_id' => $this->message_id,
            'extra_info' => $this->extra_info
        ];
    }
    public function getDetail()
    {

        return $this->detail;
    }
}

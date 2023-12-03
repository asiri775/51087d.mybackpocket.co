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

class AldoShoes
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
    private $payment_ref = null;
    private $auth_id = null;
    private $payment_method = null;
    private $extra_info = null;
    private $register_no = null;


    public function __construct($htmlBody, $plainText, $emailDate, $sender, $message_id)
    {
        $this->htmlBody = $htmlBody;
        $this->plainText = $plainText;
        $this->emailDate = $emailDate;
        $this->message_id = $message_id;

        $this->vendor_name = "AldoShoes";
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
        //return $this->plainText;
        if (Str::contains(Str::lower($this->plainText), Str::lower("Thanks for shopping at Aldo"))) { //If it is not order/invoice then skip it.
            return true;
        }
        return false;
    }

    private function setOrderNo()
    {
        $order_no = null;

        $plainText = str_replace('--tagend--', '', $this->plainText);

        if (Str::contains($plainText, 'Trans #:')) {
            $orderString = preg_grep('/^Trans #/', $this->textArray);
            $orderKeys = array_keys($orderString);
            $order_no = $this->textArray[$orderKeys[0] + 1];
        }

        /* if(Str::contains($plainText, "PM")){
                $orderStr = trim(Str::between($plainText, 'PM', 'GP'));
                
                $orderStr = preg_replace('/[^0-9A-Za-z.,$-_;: ]/', '', $orderStr);
                $orderStr = Str::between($orderStr, 'PM ', 'GP');
                $orderStr = preg_replace('/[^0-9,. ]/', '', $orderStr);
                $orderStr = trim($orderStr);
                $orderStr = substr($orderStr, 0, 25);
                $orderStr = trim($orderStr);
                $order_no = $orderStr;
                
                //$this->order_no = $order_no;  
            }else{
                if(Str::contains($plainText, 'AM')){
                    $orderString = Str::between($plainText, 'AM', 'GP');

                    $orderStr = preg_replace('/[^0-9A-Za-z.,$-_;: ]/', '', $orderStr);
                    $orderStr = Str::between($orderStr, 'PM ', 'GP');
                    $orderStr = preg_replace('/[^0-9,. ]/', '', $orderStr);
                    $orderStr = trim($orderStr);
                    $orderStr = substr($orderStr, 0, 25);
                    $orderStr = trim($orderStr);
                    $order_no = $orderStr;
                
                    //$this->order_no = $order_no;  
                }
            } */

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

    public function setVendor()
    {
        //If email is forwarded mail

        if (Str::contains(Str::lower($this->plainText), Str::lower("Thanks for shopping at Aldo"))) {

            //TODO: check if there are more than forwarded messages

            $dateStr = preg_grep('/^Date & Time/i', $this->textArray);
            $dateKeys = array_keys($dateStr);
            $date_time = $this->textArray[$dateKeys[0] + 1];
            $date_time = preg_replace('/[^0-9A-Za-z: ]/', '/', $date_time);
            $date_text = $date_time;

            $date_text = Carbon::parse($date_text);
            $this->emailDate = $date_text;

            $plainText = str_replace('--tagend--', '', $this->plainText);

            if (Str::contains($plainText, 'Thanks for shopping at Aldo')) {

                if (Str::contains($plainText, 'From: ALDO')) {
                    $emailStr = Str::between($plainText, 'From: ALDO', 'To:');
                    $emailStr = Str::between($emailStr, '<', '>');
                }
                $vendorEmailStr = $emailStr;

                $this->vendor_email =  $vendorEmailStr;
            }
        }

        if (Str::contains($plainText, 'Store')) {

            $storeStr = preg_grep('/^Store:/', $this->textArray);
            $sKeys = array_keys($storeStr);
            $store_no = $this->textArray[$sKeys[0] + 1];
        }

        $this->vendor_store_no = $store_no;

        if (Str::contains($plainText, "Purchase Store")) {

            $addressStr = Str::between($plainText, 'Purchase Store', 'Your Purchase');
            $vendor_address = $addressStr;
            /*             
            if(Str::contains($plainText, 'PM')){
                $vendor_address = Str::between($plainText, ' web browser', 'PM');
                $vendor_address = preg_replace('/[^0-9A-Za-z ,.:-]/', '', $vendor_address);
                $vendor_address = Str::before($vendor_address, '-');

                $vendor_address = substr($vendor_address, 0, -3);
                $vendor_address = trim($vendor_address);

                $months = array(
                    'Jan', 'JAN', 'jan',
                    'Feb', 'FEB', 'feb',
                    'Mar', 'MAR', 'mar',
                    'Apr', 'APR', 'apr',
                    'May', 'MAY', 'may',
                    'Jun', 'JUN', 'jun',
                    'Jul', 'JUL', 'jul',
                    'Aug', 'AUG', 'aug',
                    'Sep', 'SEP', 'sep',
                    'Oct', 'OCT', 'oct',
                    'Nov', 'NOV', 'nov',
                    'Dec', 'DEC', 'dec',
                );
                $monthPos = strpos($vendor_address, 'Aug');
                
                foreach($months as $month){
                    if(Str::contains($vendor_address, $month)){
                        $vendor_address = Str::before($vendor_address, $month);

                    }
                }
                
            }

            if(Str::contains($plainText, 'AM')){
                $vendor_address = Str::between($plainText, ' web browser', 'PM');
                $vendor_address = preg_replace('/[^0-9A-Za-z ,.:-]/', '', $vendor_address);
                $vendor_address = Str::before($vendor_address, '-');

                $vendor_address = substr($vendor_address, 0, -4);
                $vendor_address = trim($vendor_address);
            } 
            */
            $vendor_address = str_replace('ALDO', 'ALDO ', $vendor_address);
            $vendor_address = str_replace('GARDENS', 'GARDENS ', $vendor_address);
            $vendor_address = str_replace('MALL', 'MALL ', $vendor_address);
            $vendor_address = str_replace('(', ' (', $vendor_address);

            $phone = '(' . Str::after($vendor_address, '(');
            $this->vendor_phone = $phone;
            $this->vendor_address = Str::before($vendor_address, '(');


            $vendor_address = explode(',', $this->vendor_address);

            $this->vendor_street_name = trim(Str::beforeLast($vendor_address[0], ' '));
            $this->vendor_city = trim(Str::afterLast($vendor_address[0], ' '));
            $split = trim($vendor_address[1]);
            $split = explode(' ', $split);
            $this->vendor_state = trim($split[0]);
            $this->vendor_zip_code = trim($split[1]);
        }
    }

    private function setProducts()
    {
        /**
         * Products Details
         */
        $prods_text = null;

        //TODO: Check with discounted email
        //$plainText = str_replace('--tagend--', '', $this->plainText);

        if (Str::contains(Str::lower($this->plainText), Str::lower('Your Purchase'))) {
            $prods_text = null;


            $prods_text = Str::after($this->plainText, 'Your Purchase');
            $prods_text = Str::before($prods_text, 'Sub-Total');

            $prods_array = explode('--tagend--', $prods_text);

            $prods_array = array_values(array_filter($prods_array));
            $prods = [];
            foreach ($prods_array as $key => $value) {
                if (Str::contains($value, 'Price')) {
                    $prods[] = [
                        'name'          =>      ucfirst($prods_array[$key - 1]),
                        'Sku'           =>      preg_replace('/[^0-9 ]/', '', $prods_array[$key + 9]),
                        'price'         =>      floatval(str_replace(['$', ','], '', $prods_array[$key + 1])),
                        'quantity'      =>      $prods_array[$key + 7],
                        'description'   =>      $prods_array[$key + 2] . " : " . $prods_array[$key + 3] . " | " . $prods_array[$key + 4] . " : " . $prods_array[$key + 5] . '| Associate:' . $prods_array[$key + 9]
                    ];
                }
            }
        }

        $this->products = $prods;
    }

    public function setExtraInfo()
    {
        $plainText = str_replace('--tagend--', '', $this->plainText);

        $type = Str::between($this->plainText, 'TYPE', 'ACCT');
        $type = trim(preg_replace('/[^A-Za-z ]/', '', $type));

        $account_type = Str::between($plainText, 'ACCT', 'CARD NUMBER');
        $account_type = trim(preg_replace('/[^A-Za-z ]/', '', $account_type));

        $card_number = Str::between($this->plainText, 'CARD NUMBER:', 'DATE/TIME');
        $card_number = '*' . preg_replace('/[^0-9]/', '', $card_number);

        $author = Str::between($plainText, 'AUTHOR', 'AID');
        $author = preg_replace('/[^0-9A-Za-z ]/', '', $author);
        $author = trim($author);
        $registerStr = preg_grep('/^Register/', $this->textArray);
        $rKeys = array_keys($registerStr);
        $register = $this->textArray[$rKeys[0] + 1];

        $ex_info = [];
        if ($type) {
            $ex_info[] = [
                'label'     =>  "Receipt Type",
                'value'     =>   $type,
                'key'       =>  'receipt_type',
                'type'      =>  'invoice'
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


        if ($author) {
            $ex_info[] = [
                'label'      =>      'Author Id',
                'value'     =>       $author,
                'key'       =>      'author_id',
                'type'      =>      'details'
            ];
        }

        if ($register) {
            $ex_info[] = [
                'label'      =>      'Register Number',
                'value'     =>       $register,
                'key'       =>      'register_number',
                'type'      =>      'details'
            ];
            $this->register_no = $register;
        }


        $this->extra_info = collect($ex_info)->toJson();
    }

    private function setTransaction()
    {
        $plainText = str_replace('--tagend--', '', $this->plainText);


        $totalStr = preg_grep('/^Total/', $this->textArray);
        $totKey = array_keys($totalStr);
        $total = floatval(str_replace(['$', ','], '', $this->textArray[$totKey[0] + 1]));

        if (Str::contains($plainText, 'HST') || Str::contains($plainText, 'HST/TVH')) {
            $tax = preg_grep('/^HST/', $this->textArray);
            $taxKey = array_keys($tax);
            $taxValue = $this->textArray[$taxKey[0] + 1];
        }
        $tax_hst = floatval(str_replace(['$', ','], '', $taxValue));

        //$tax_hst = '$'.number_format((float)$tax_hst, 2, '.', '');
        $subtotalStr = preg_grep('/^Sub-Total/', $this->textArray);
        $subtotKey = array_keys($subtotalStr);
        $sub_total = floatval(str_replace(['$', ','], '', $this->textArray[$subtotKey[0] + 1]));

        $payment_method = Str::between($plainText, 'ACCT', 'CARD NUMBER');
        $payment_method = trim(preg_replace('/[^A-Za-z ]/', '', $payment_method));

        $cardnoStr = preg_grep('/^Payment Method/', $this->textArray);
        $cKeys = array_keys($cardnoStr);
        $card_no = '*' . preg_replace('/[^0-9]/', '', $this->textArray[$cKeys[0] + 1]);


        $ref_no = Str::between($plainText, 'REFERENCE #: ', 'TERM:');
        $ref_no = trim($ref_no);

        $auth_id = Str::between($plainText, 'AUTHOR.# : ', 'AID');
        $auth_id = trim($auth_id);

        $this->sub_total = $sub_total;
        $this->tax_amount = $tax_hst;
        $this->total = $total;
        $this->payment_ref = $ref_no;
        $this->auth_id = $auth_id;
        $this->payment_method = $payment_method . ' ' . $card_no;

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
                'store_no' => $this->vendor_store_no,
                'phone' => $this->vendor_phone,
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
            'register_no' => $this->register_no,
            'payment_method' => $this->payment_method,
            'payment_ref'   =>  $this->payment_ref,
            'auth_id'   => $this->auth_id,
            'message_id' => $this->message_id,
            'extra_info' => $this->extra_info
        ];
    }

    public function getDetail()
    {

        return $this->detail;
    }
}

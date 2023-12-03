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

class McDonald
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
    private $payment_ref;
    private $payment_method = null;
    private $extra_info = null;

    public function __construct($htmlBody, $plainText, $emailDate, $sender, $message_id)
    {
        $this->htmlBody = $htmlBody;
        $this->plainText = $plainText;
        $this->emailDate = $emailDate;
        $this->message_id = $message_id;

        $this->vendor_name = "McDonalds";
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
        //return $this->textArray;
        if (Str::contains(Str::lower($this->plainText), Str::lower("Order Receipt"))) { //If it is not order/invoice then skip it.
            return true;
        }
        return false;
    }

    private function setOrderNo()
    {
        $order_no = null;
        if (Str::contains($this->plainText, "Order Receipt")) {

            $orderStr = preg_grep('/^Order Number:/', $this->textArray);
            $oKeys = array_keys($orderStr);
            $order_no = $this->textArray[$oKeys[0] + 1];

            //return $order_no;
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
        //return array_search('Discount Applied:', $this->textArray);
    }

    private function setDiscount()
    {

        if (in_array('Discount Applied:', $this->textArray)) {
            $discountStr = preg_grep('/^Discount Applied:/', $this->textArray);
            $disKey = array_keys($discountStr);
            $discount = '$' . $this->textArray[$disKey[0] + 2];
            $this->discount = floatval(str_replace('$', '', $discount));
        }
    }

    private function setVendor()
    {
        //If email is forwarded mail
        $date_text = null;

        if (Str::contains(Str::lower($this->plainText), Str::lower("Order Receipt"))) {

            //TODO: check if there are more than forwarded messages

            $dateStr = preg_grep('/^Order Date:/', $this->textArray);
            $dKeys = array_keys($dateStr);
            $date_text = $this->textArray[$dKeys[0] + 1];

            $date_text = str_replace('T', ' ', $date_text);


            $this->emailDate =  Carbon::parse($date_text);


            //If string contains fails then use the default email address

            if (Str::contains($this->plainText, "Here’s your McDonald’s")) {

                // $emailStr = preg_grep('/^From:/i', $this->textArray);
                // $eKeys = array_keys($emailStr);
                // $emailStr = $this->textArray[$eKeys[0] + 1];
                // $vendorEmailStr = Str::between($emailStr, '<', '>');

                $this->vendor_email =  'mobile@ca.mcdonalds.com';
            }
        }

        if (Str::contains($this->plainText, "Address")) {

            $addressStr = preg_grep('/^Address/', $this->textArray);
            $aKeys = array_keys($addressStr);

            $vendor_address = $this->textArray[$aKeys[0] + 1] . ' ' . $this->textArray[$aKeys[0] + 3] . ' ' . $this->textArray[$aKeys[0] + 5] . ' ' . $this->textArray[$aKeys[0] + 7];
            $this->vendor_address = $vendor_address;

            $this->vendor_street_name = $this->textArray[$aKeys[0] + 1];
            $this->vendor_city = $this->textArray[$aKeys[0] + 3];
            $this->vendor_state = $this->textArray[$aKeys[0] + 5];
            $this->vendor_zip_code = $this->textArray[$aKeys[0] + 7];
        }

        $this->vendor_address;
    }

    private function setProducts()
    {
        /**
         * Products Details
         */
        $prods_text = null;

        //TODO: Check with discounted email


        if (Str::contains($this->plainText, 'Discount Applied')) {
            $prods_text = Str::between($this->plainText, 'Item Cost', 'Discount Applied');
        } else {
            $prods_text = Str::between($this->plainText, 'Item Cost', 'Sub-Total');
        }

        $prods_array = explode('--tagend--', $prods_text);

        foreach ($prods_array as $key => $value) {
            $prods_array[$key]  = preg_replace('/[^0-9 A-Za-z,.$@+~ ]/', '',  $value);
            if (Str::contains($value, '+')) {
                unset($prods_array[$key]);
            }
        }

        $prods_array = array_values(array_filter($prods_array));

        /*
        //return $prods_array;
        $prods = [];
        foreach($prods_array as $key => $value){
            if(Str::contains($value, '$') && !is_numeric($value)) {
                $prods[] = [
                    'name'      =>      (!is_numeric($prods_array[$key]) ? $prods_array[$key] : $prods_array[$key]),//$prods_array[$key-1],
                    'price'     =>      $prods_array[$key+1],
                    //'quantity'  =>      $prods_array[$key-2]
                ];
            
            }
        }
        return $prods; */

        $text = preg_grep('/^Item Cost/i', $this->textArray);

        $discount = preg_grep('/^Discount Applied/i', $this->textArray);

        $discountKey = array_keys($discount);

        $prods_keys = array_keys($text);
        $prods_text = array_values(array_filter($prods_array));

        $prods = [];

        foreach ($prods_array as $key => $value) {

            if (Str::contains($value, 'FREE')) {
                $prods[] = [
                    'name'      =>  $prods_array[$key],
                    'price'     =>  ''
                ];
            }

            if (Str::contains($value, '$')) {

                $prods[] = [
                    'name'          =>  $prods_array[$key - 1],
                    'price'         =>  floatval(str_replace('$', '', $prods_array[$key])),
                    //'quantity'      =>  $prods_array[$key-2]

                ];
            }
        }


        $this->products = $prods;

        //return $this->products;
    }

    private function setExtraInfo()
    {
        $typeStr = preg_grep('/Please do not reply/', $this->textArray);
        $typeStr = implode($typeStr);
        if (Str::contains($typeStr, ' system-generated email')) {
            $type = 'System Generated Email';
        }

        $accountCardStr = preg_grep('/Card Issuer/', $this->textArray);

        $accKey = array_keys($accountCardStr);
        $account_type = $this->textArray[$accKey[0] + 1];

        $date = $this->emailDate;

        $cardNumStr = preg_grep('/Card number:/', $this->textArray);
        $cKeys = array_keys($cardNumStr);
        $card_number = '*' . $this->textArray[$cKeys[0] + 1];

        $authNumStr = preg_grep('/Authorization/', $this->textArray);
        $authKeys = array_keys($authNumStr);
        $auth_number = $this->textArray[$authKeys[0] + 1];

        $restNumStr = preg_grep('/^Restaurant Number:/', $this->textArray);
        $rKeys = array_keys($restNumStr);
        $restaurant_number = $this->textArray[$rKeys[0] + 1];

        $restNameStr = preg_grep('/^Restaurant Name/', $this->textArray);
        $resKeys = array_keys($restNameStr);
        $restaurant_name = $this->textArray[$resKeys[0] + 1];


        $approvedAmtStr = preg_grep('/^Approved Amount/', $this->textArray);

        $apprKeys = array_keys($approvedAmtStr);
        $approved_amount_value = '$' . $this->textArray[$apprKeys[0] + 2];
        $approved_amount_type = str_replace(' ', '', $this->textArray[$apprKeys[0] + 3]);

        $ex_info = [];
        if ($type) {
            $ex_info[] = [
                'label'     =>      "Email Type",
                'value'     =>       $type,
                'key'       =>      'email_type',
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
                'label'      =>     'Card Number',
                'value'     =>       $card_number,
                'key'       =>      'card_number',
                'type'      =>      'account_details'
            ];
        }

        if ($auth_number) {
            $ex_info[] = [
                'label'      =>     'Auth Number',
                'value'     =>       $auth_number,
                'key'       =>      'auth_number',
                'type'      =>      'auth_number'
            ];
        }


        if ($date) {
            $ex_info[] = [
                'label'      =>     'Transaction Date',
                'value'     =>       $date,
                'key'       =>      'transaction_date',
                'type'      =>      'date_time'
            ];
        }
        if ($restaurant_name) {
            $ex_info[] = [
                'label'      =>     'Restaurant Name',
                'value'     =>      $restaurant_name,
                'key'       =>      'restaurant_name',
                'type'      =>      'restaurant_name'
            ];
        }

        if ($restaurant_number) {
            $ex_info[] = [
                'label'      =>     'Restaurant Number',
                'value'     =>      $restaurant_number,
                'key'       =>      'restaurant_number',
                'type'      =>      'restaurant_number'
            ];
        }

        if ($approved_amount_type) {
            $ex_info[] = [
                'label'      =>     'Approved Amount ' . $approved_amount_type,
                'value'     =>      $approved_amount_value,
                'key'       =>      'approved_amount_' . strtolower($approved_amount_type),
                'type'      =>      'amount_type'
            ];
        }
        $this->extra_info = collect($ex_info)->toJson();
    }

    private function setTransaction()
    {
        $totalStr = preg_grep('/^Total/', $this->textArray);
        $totKeys = array_keys($totalStr);
        $total = $this->textArray[$totKeys[0] + 2];
        $total = '$' . preg_replace('/[^0-9.]/', '', $total);

        $hstStr = preg_grep('/HST/', $this->textArray);
        $hstKeys = array_keys($hstStr);
        $tax_hst = '$' . $this->textArray[$hstKeys[0] + 2];

        $subStr = preg_grep('/Sub-Total/', $this->textArray);
        $subKeys = array_keys($subStr);
        $sub_total = '$' . $this->textArray[$subKeys[0] + 2];

        $paymentStr = preg_grep('/^Card Issuer/', $this->textArray);
        $payKeys = array_keys($paymentStr);
        $payment_method = $this->textArray[$payKeys[0] + 1];

        $refStr = preg_grep('/^Authorization/', $this->textArray);
        $refKeys = array_keys($refStr);
        $reference_no = $this->textArray[$refKeys[0] + 1];

        $acc_card_no = preg_grep('/Card number:/', $this->textArray);
        $accKeys = array_keys($acc_card_no);
        $acc_card_no = '*' . $this->textArray[$accKeys[0] + 1];


        $this->sub_total = floatval(str_replace('$', '', $sub_total)); // $this->textArray[$sub_total_index + 1];
        $this->tax_amount = floatval(str_replace('$', '', $tax_hst)); //$this->textArray[$tax_label_index + 1];
        $this->total = floatval(str_replace('$', '', $total)); //$this->textArray[$total_label_index + 1];
        $this->payment_ref = $reference_no;
        $this->payment_method = $payment_method . ' ' . $acc_card_no;

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
            'payment_ref' => $this->payment_ref,
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

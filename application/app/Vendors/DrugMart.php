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

class DrugMart
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

        $this->vendor_name = "DrugMart";
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
        if (Str::contains(Str::lower($this->plainText), Str::lower("CUSTOMER COPY"))) { //If it is not order/invoice then skip it.
            return true;
        }
        return false;
    }

    private function setOrderNo()
    {
        $order_no = null;

        $plainText = str_replace('--tagend--', '', $this->plainText);

        if (Str::contains($plainText, "PM")) {
            $orderStr = trim(Str::between($plainText, 'PM', 'GP'));

            $orderStr = preg_replace('/[^0-9A-Za-z.,$-_;: ]/', '', $orderStr);
            $orderStr = Str::between($orderStr, 'PM ', 'GP');
            $orderStr = preg_replace('/[^0-9,. ]/', '', $orderStr);
            $orderStr = trim($orderStr);
            $orderStr = substr($orderStr, 0, 25);
            $orderStr = trim($orderStr);
            $order_no = $orderStr;

            //$this->order_no = $order_no;  

        } else {
            if (Str::contains($plainText, 'AM')) {
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

        if (Str::contains(Str::lower($this->plainText), Str::lower("Drug Mart Receipt"))) {

            //TODO: check if there are more than forwarded messages
            $dateStr = preg_grep('/PM/i', $this->textArray);
            $dateStr = implode($dateStr);
            $dateStr = preg_replace('/[^0-9A-Za-z ,.:]/', '', $dateStr);



            if (Str::contains($dateStr, 'PMTo:')) {
                $dateArray = explode('To:', $dateStr);

                if ($dateArray) {
                    $dateArray = end($dateArray);
                    $dateStr = trim($dateArray);
                }
            } else if (Str::contains($dateStr, 'AMTo:')) {
                $dateArray = explode('To:', $dateStr);

                if ($dateArray) {
                    $dateArray = end($dateArray);
                    $dateStr = trim($dateArray);
                };
            }
            $date_text = Carbon::parse($dateStr);

            $this->emailDate = $date_text;

            //If string contains fails then use the default email address
            $plainText = str_replace('--tagend--', '', $this->plainText);

            if (Str::contains($plainText, 'Drug Mart Receipt')) {

                $vendorEmailStr = Str::between($plainText, '==From :', 'To : <');
                if (Str::contains($vendorEmailStr, 'Shoppers Drug Mart')) {
                    $vendorEmailStr = Str::between($vendorEmailStr, 'From: Shoppers Drug Mart', 'To:');

                    if (Str::contains($vendorEmailStr, '<') || Str::contains($vendorEmailStr, '>')) {
                        $vendorEmailStr = Str::between($vendorEmailStr, '<', '>');
                        $vendorEmailStr = Str::between($vendorEmailStr, '[mailto:', ']');
                    }
                    if (Str::contains($vendorEmailStr, '[') || Str::contains($vendorEmailStr, ']')) {
                        $vendorEmailStr = Str::between($vendorEmailStr, '<', '>');
                        $vendorEmailStr = Str::between($vendorEmailStr, '[mailto:', ']');
                    } else {
                        $vendorEmailStr = Str::before($vendorEmailStr, '<');
                    }
                }
                $vendorEmailStr = Str::after($vendorEmailStr, '<');
                $vendorEmailStr = Str::before($vendorEmailStr, '>');
                $this->vendor_email =  $vendorEmailStr;
            }
            /* $vendor_no = Str::between($this->plainText, 'Visit PCFinancial.ca', ' Retain Receipt ');
            $vendor_no = str_replace('*', '', $vendor_no); */

            //$this->vendor_tax_no = null;

        }
        //dd($this->plainText);
        if (Str::contains($plainText, "View the receipt in web browser")) {

            if (Str::contains($plainText, 'PM')) {
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

                foreach ($months as $month) {
                    if (Str::contains($vendor_address, $month)) {
                        $vendor_address = Str::before($vendor_address, $month);
                    }
                }
            }

            if (Str::contains($plainText, 'AM')) {
                $vendor_address = Str::between($plainText, ' web browser', 'PM');
                $vendor_address = preg_replace('/[^0-9A-Za-z ,.:-]/', '', $vendor_address);
                $vendor_address = Str::before($vendor_address, '-');

                $vendor_address = substr($vendor_address, 0, -4);
                $vendor_address = trim($vendor_address);
            }
            $this->vendor_address = $vendor_address;
            $address = explode(',', $vendor_address) ?? '';

            $this->vendor_street_name = trim($address[0]);
            $this->vendor_city = trim($address[1]);
            $this->vendor_state = trim($address[2]);
            $this->vendor_zip_code = trim($address[3]);
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

        if (Str::contains(Str::lower($this->plainText), Str::lower('SUBTOTAL:'))) {
            $prods_text = null;

            $prods_text = Str::after($this->plainText, 'View the receipt in web browser');
            $prods_text = Str::before($prods_text, 'SUBTOTAL:');

            $prods_array = explode('--tagend--', $prods_text);

            foreach ($prods_array as $key => $value) {
                if ($value == ""  || $value == " " || $value == "&nbsp;" || Str::contains($value, '======')) {
                    unset($prods_array[$key]);
                } else {
                    $prods_array[$key] = trim($value);
                }
            }

            $prods_array = array_values(array_filter($prods_array));
            $prods_collect = collect($prods_array);

            $split_delimeter = "GP";
            $split_index = $prods_collect->filter(function ($item, $key) use ($split_delimeter) {
                if (Str::contains($item, $split_delimeter)) {
                    return $key;
                }
            });
            $p_array = implode('GP', $split_index->toArray());

            $prodArray = explode('GP', $p_array);


            $textArrays = array_map('trim', $prodArray);
            $product = array_values(array_filter($textArrays));
            $prods = [];
            for ($i = 0; $i < (count($product)); $i++) {
                if ($i % 2 == 0) {
                    if (isset($product[$i]) && $product[$i + 1]) {
                        $price = '$' . preg_replace('/[^0-9 .,]/', '', $product[$i + 1]);
                        $prods[] = [
                            'name' => Str::beforeLast(preg_replace('/[^0-9A-Za-z .,]/', '', $product[$i]), ' '),
                            'price' => floatval(str_replace('$', '', $price)),
                        ];
                    }
                }
            }
        }

        $this->products = $prods;
    }

    private function setExtraInfo()
    {
        $plainText = str_replace('--tagend--', '', $this->plainText);
        $type =  Str::between($plainText, 'TYPE :', 'ACCT  :');
        $type = trim(Str::before($type, 'ACCT'));

        $account_type = Str::between($plainText, 'ACCT  :', 'Card Type');
        if (Str::contains($account_type, 'ACCT')) {
            $account_type = Str::after($account_type, $type);
            $account_type = trim(Str::between($account_type, ':', '$'));
            $account_type = preg_replace('/[^A-Za-z ]/', '', $account_type);
            $account_type = trim(Str::before($account_type, 'CARD NUMBER'));
        }

        $card_number = Str::between($this->plainText, 'CARD NUMBER:', 'DATE/TIME:');
        $card_number = '*' . preg_replace('/[^0-9]/', '', $card_number);

        /* 
        $date_time = Str::between($plainText, 'DATE/TIME:', 'REFERENCE');
        $date_time = preg_replace('/[^0-9:\/ ]/', '', $date_time);
        //$dateTime = \DateTime::createFromFormat('m/d/Y', $dateStr)->setTime($hours, $minutes);
        $date_time = trim($date_time);
        $date = Str::before($date_time, ' ');
        $date = str_replace('/', '-', $date);
        $time = Str::after($date_time, ' '); 
        
        
        $convertedDate = date('d-M-Y H:i:s', strtotime($date.' '.$time));

        $date_time = Carbon::parse($convertedDate);
        */



        //$date_time = Carbon::parse($date_time);


        $author = Str::between($plainText, 'AUTHOR. #:', 'APPROVED');
        $author = preg_replace('/[^0-9A-Za-z ]/', '', $author);
        $author = substr($author, 0, 7);
        $author = trim($author);


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


        $this->extra_info = collect($ex_info)->toJson();
    }

    private function setTransaction()
    {
        $plainText = str_replace('--tagend--', '', $this->plainText);
        $total = Str::between($plainText, 'TOTAL:', '*');
        $total = preg_replace('/[^0-9A-Za-z$,. ]/', '', $total);

        $total = Str::after($total, 'TOTAL');
        $total = trim($total);
        $total = Str::before($total, ' ');
        //$total = substr($total, 0, 15);
        $total = preg_replace('/[^0-9$,.]/', '', $total);

        $tax_hst = Str::between($plainText, 'HST :', 'Items');
        $tax_hst = preg_replace('/[^0-9,.$]/', '', $tax_hst);

        //$tax_hst = '$'.number_format((float)$tax_hst, 2, '.', '');

        $sub_total = Str::between($plainText, 'SUBTOTAL:', 'HST :');
        $sub_total = preg_replace('/[^0-9,.$]/', '', $sub_total);


        $payment_method = Str::between($plainText, 'Items', '*');
        $payment_method = preg_replace('/[^0-9A-Za-z:,.$ ]/', '', $payment_method);
        $payment_method = Str::beforeLast($payment_method, ':');
        $payment_method = Str::before($payment_method, 'you');
        $payment_method = Str::after($payment_method, ':');

        $payment_method = preg_replace('/[^A-Za-z ]/', '', $payment_method);
        $payment_method = trim($payment_method);
        $payment_method = Str::before($payment_method, ' ');

        $reference_no = Str::between($this->plainText, 'REFERENCE #: ', 'AUTHOR. #: ');
        $ref_no = Str::between($plainText, 'REFERENCE #:', 'AUTHOR');
        $ref_no = preg_replace('/[^0-9A-Za-z ]/', '', $ref_no);
        $ref_no = trim($ref_no);


        $this->sub_total = floatval($sub_total); // $this->textArray[$sub_total_index + 1];
        $this->tax_amount = floatval($tax_hst); //$this->textArray[$tax_label_index + 1];
        $this->total = floatval(str_replace('$', '', $total)); //$this->textArray[$total_label_index + 1];
        $this->payment_ref = $ref_no;
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
            'payment_ref'   =>  $this->payment_ref,
            'message_id' => $this->message_id,
            'extra_info' => $this->extra_info
        ];
    }

    public function getDetail()
    {

        return $this->detail;
    }
}

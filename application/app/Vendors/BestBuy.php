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

class BestBuy
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
    private $payment_ref;
    private $payment_method = null;
    private $extra_info = null;

    public function __construct($htmlBody, $plainText, $emailDate, $sender, $message_id)
    {
        $this->htmlBody = $htmlBody;
        $this->plainText = $plainText;
        $this->emailDate = $emailDate;
        $this->message_id = $message_id;

        $this->vendor_name = "BestBuy";
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

        if (Str::contains(Str::lower($this->plainText), Str::lower("YOUR BEST BUY e-RECEIPT IS HERE"))) { //If it is not order/invoice then skip it.
            return true;
        }
        return false;
    }

    private function setOrderNo()
    {
        $order_no = null;

        $plainText = str_replace('--tagend--', '', $this->plainText);

        $orderString = Str::between($plainText, 'Val', 'SALES');

        $order_no = preg_match('/([0-9]{4})-([0-9]{4})-([0-9]{4})-([0-9]{4})/', $orderString, $orderMatch);
        $order_no = array_shift($orderMatch);


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
        $plainText = str_replace('--tagend--', '', $this->plainText);

        if (Str::contains(Str::lower($plainText), Str::lower("Yours truly"))) {

            $dateString = Str::between($plainText, 'Best Buy Canada', 'Your Best Buy eReceipt');

            if (Str::contains($dateString, 'Sent:')) {
                if (Str::contains($dateString, 'AMTo')) {
                    $dateString = Str::between($dateString, 'Sent:', 'AMTo');
                } else {
                    $dateString = Str::between($dateString, 'Sent:', 'PMTo');
                }
            }

            $dateString = Str::between($plainText, 'Val', 'SALES');
            $dateString = trim($dateString);

            $date_string = preg_match('/([0-9]{2})\/([0-9]{2})\/([0-9]{2})/', $dateString, $dateMatch);
            $time_string = preg_match('/([0-9]{2}):([0-9]{2})/', $dateString, $timeMatch);
            $date_text = array_shift($dateMatch);
            $time_text = array_shift($timeMatch);

            $date_time = $date_text . ' ' . $time_text;

            $date_time = Carbon::parse($date_time);
            $this->emailDate = $date_time;

            //TODO: check if there are more than forwarded messages

            //If string contains fails then use the default email address
            $plainText = str_replace('--tagend--', '', $this->plainText);

            if (Str::contains($plainText, 'YOUR BEST BUY e-RECEIPT IS HERE')) {
                $emailStr = preg_grep('/Best Buy Canada/', $this->textArray);
                $emailStr = array_values(array_filter($emailStr));

                $emailSting = null;
                foreach ($emailStr as $key => $value) {
                    if (Str::contains($value, '@')) {
                        $emailString = $value;
                    }
                    if (Str::contains($value, '<')) {
                        $emailString = Str::after($value, '<');
                    }
                    if (Str::contains($value, '>')) {
                        $emailString = Str::after($value, '>');
                    }
                }

                $vendorEmailStr = $emailString;

                $this->vendor_email =  $vendorEmailStr;
            }
        }

        if (Str::contains($plainText, "Yours truly")) {

            $addressStr = Str::between($plainText, 'BEST BUYbestbuy.ca', 'Store Phone');

            $addressStr = trim(Str::after($addressStr, 'Thousands'));
            $addressStr = Str::between($addressStr, 'Yours', '#');
            $addressStr = Str::before($addressStr, 'Geek');
            $addressStr = trim($addressStr);

            $addressStr = trim($addressStr);

            $vendor_address = $addressStr;

            //$this->vendor_address = $vendor_address;

            $string = htmlentities($vendor_address, null, 'utf-8');
            $content = str_replace("&nbsp;", " ", $string);
            $content = html_entity_decode($content);
            $content = trim($content);


            $phone = Str::after($content, 'Store Phone #:');
            $this->vendor_phone =  $phone;

            $address = Str::before($content, 'Store Phone #:');
            $this->vendor_address =  $address;

            $address = explode(',', $address);
            $this->vendor_street_name = $address[0];
            $this->vendor_city = trim($address[1]);
        }
    }

    private function setProducts()
    {
        /**
         * Products Details
         */
        $prods_text = null;

        //TODO: Check with discounted email

        if (Str::contains(Str::lower($this->plainText), Str::lower('SALES'))) {

            $prods_text = null;


            $prods_text = Str::after($this->plainText, 'SALES');
            $prods_text = Str::before($prods_text, '----------');

            $prods_array = explode('--tagend--', $prods_text);

            $prods_array = array_values(array_filter($prods_array));
            $prods_text = preg_grep('/^\d{4}/', $prods_array);


            $prods = [];
            foreach ($prods_text as $key => $value) {

                $price = preg_replace('/[^0-9 .,]/', '', substr($value, -6));
                $description =  Str::before($prods_array[$key + 1], $price); //$value; //$prods_array[$key+1];
                $name = str_replace($price, '', $value);
                $prods[] = [
                    'name'        => trim($name),
                    'price'       => floatval(str_replace('$', '', $price)),
                    'description' => trim($description),
                ];
            }
        }

        $this->products = $prods;
    }

    private function setExtraInfo()
    {
        $plainText = str_replace('--tagend--', '', $this->plainText);

        $type = Str::between($plainText, 'TOTAL', 'Approved');
        $type = Str::between($type, 'TOTAL', 'x');
        $type = Str::before($type, 'x');
        $type = strip_tags($type);

        $type = trim(preg_replace('/[^A-Za-z]/', ' ', $type));

        $account_type = Str::between($plainText, 'TOTAL', 'Approved');
        $accountTypeStr = Str::after($account_type, 'x');
        $account_type = Str::after($accountTypeStr, 'C');
        $account_type = trim(preg_replace('/[^A-Za-z ]/', ' ', $account_type));

        $card_number = Str::between($plainText, 'TOTAL', 'Approved');
        $type_last_word = Str::afterLast($type, ' ');

        $card_number = Str::between($card_number, $type_last_word, $account_type);
        $card_number = '*' . preg_replace('/[^0-9. ,]/', '', $card_number);

        $approved_number = Str::between($plainText, 'Approved', 'TERM');
        $approved_number = preg_replace('/[^0-9. ,]/', '', $approved_number);

        $aid_no = preg_grep('/^AID/', $this->textArray);
        $aid_no = implode($aid_no);
        $aid_no = trim(Str::after($aid_no, 'AID:'));

        $aid_no = preg_replace('/[^0-9A-Za-z]/', '',  $aid_no);

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


        if ($approved_number) {
            $ex_info[] = [
                'label'      =>      'Approved Number',
                'value'     =>       $approved_number,
                'key'       =>      'approved_number',
                'type'      =>      'details'
            ];
        }

        if ($aid_no) {
            $ex_info[] = [
                'label'      =>     'AUTH Number',
                'value'     =>       $aid_no,
                'key'       =>      'auth_number',
                'type'      =>      'auth_number'
            ];
            $this->auth_id = $aid_no;
        }



        $this->extra_info = collect($ex_info)->toJson();
    }

    private function setTransaction()
    {
        $plainText = str_replace('--tagend--', '', $this->plainText);

        $total = Str::between($plainText, 'TOTAL', 'Transaction');
        $total = Str::after($total, 'TOTAL');
        $total = '$' . preg_replace('/[^0-9,.$ ]/', '', $total);;

        $tax = Str::between($plainText, 'HST', 'TOTAL');
        $tax = Str::after($tax, 'ON');
        $taxValue = preg_replace('/[^0-9,. ]/', '', $tax);

        $tax_hst = $taxValue;

        $subtotalStr = Str::between($plainText, 'SUBTOTAL', 'HST');
        $sub_total = preg_replace('/[^0-9.,$ ]/', '', $subtotalStr);

        $payment_method = Str::between($plainText, 'APN', 'TVR ');
        $payment_method = Str::before($payment_method, 'TVR');
        $payment_method = trim(preg_replace('/[^A-Za-z]/', ' ', $payment_method));

        $card_no = Str::between($plainText, 'Transaction', 'Approved');
        $card_no = Str::after($card_no, 'SALE');
        $card_no = Str::before($card_no, 'C');
        $card_no = '#' . preg_replace('/[^0-9]/', '', $card_no);

        $ref_no = preg_grep('/^Approved/', $this->textArray);
        $ref_no = implode($ref_no);
        $ref_no = Str::after($ref_no, 'Approved');

        $ref_no = preg_replace('/[^0-9A-Za-z]/', '', $ref_no);

        $this->sub_total = floatval(str_replace('$', '', $sub_total));
        $this->tax_amount = floatval(str_replace('$', '', $tax_hst));
        $this->total = floatval(str_replace('$', '', $total));
        $this->payment_ref = $ref_no;
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
                'phone' => $this->vendor_phone,
                'street_name' => $this->vendor_street_name,
                'city' => $this->vendor_city,

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
            'extra_info' => $this->extra_info,
            'auth_id' => $this->auth_id,
        ];
    }

    public function getDetail()
    {

        return $this->detail;
    }
}

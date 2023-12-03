<?php


namespace App\Vendors;


use App\Models\Transaction;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class BedBath
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
    private $vendor_city = null;
    private $vendor_state = null;
    private $vendor_zip_code = null;
    private $vendor_contact_no = null;

    private $products = [];

    private $order_no;
    private $emailDate;
    private $discount;
    private $sub_total = 0;
    private $tax_amount = 0;
    private $total = 0;
    private $payment_method = null;
    private $payment_ref = null;

    public function __construct($htmlBody, $plainText, $emailDate, $sender, $message_id)
    {
        $this->htmlBody = $htmlBody;
        $this->plainText = $plainText;
        $this->emailDate = $emailDate;
        $this->message_id = $message_id;

        $this->vendor_name = "Bed Bath & Beyond";
        $this->sender = $sender;
        $this->vendor_email = $this->sender->mail;

        $this->plainTextToArray();


        $this->setOrderNo();

        $this->setDiscount();
    }

    private function filterArray($array)
    {

        $tmp = [];

        //removing extra spaces from array
        $array = array_filter($array, function ($e) {
            return str_replace(' ', '', trim(preg_replace('/\s+/', ' ', $e)));;
        });
        foreach ($array as $key => $value) {
            //removing utf-8 characters like &nbsp;
            $value = str_replace("\xc2\xa0", ' ', $value);
            $tmp[] = trim($value);
        }

        //removing empty elements from content array using array_values
        return array_values(array_filter($tmp));
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

        $this->textArray = $this->filterArray($tmp_content);

        $this->textCollection = collect($this->textArray);
    }

    private function arraySearchIndex($string)
    {
        $element = array_filter($this->textArray, function ($text) use ($string) {
            return preg_match("/\b$string\b/i", $text);
        });
        $index = array_key_first($element);

        return $index;
    }

    //TODO: Set configuration, for example start and end point of parsing
    private function isInvoice()
    {
        if (Str::contains(Str::lower($this->plainText), Str::lower("RVN"))) { //If it is not order/invoice then skip it.
            return true;
        }
        return false;
    }

    private function setOrderNo()
    {
        //If string contains fails then use the default email address
        if (Str::contains($this->htmlBody, 'RVN')) {
            $this->order_no = strip_tags(Str::between($this->plainText, "BEYOND", "RVN"));
            $this->order_no = strip_tags(Str::between($this->order_no, "#", "--tagend----tagend--"));
            $this->order_no = explode(' ', $this->order_no);
            $this->order_no = $this->order_no[0];

            $this->order_no = mb_convert_encoding($this->order_no, 'UTF-8', 'UTF-8');
            $this->order_no = strip_tags($this->order_no);
            $this->order_no = preg_replace('/[?]/', '', $this->order_no);
            $this->order_no = str_replace('&nbsp;', '', $this->order_no);
            $this->order_no = str_replace('--tagend--;', '', $this->order_no);
            $this->order_no = trim($this->order_no);

            $this->order_no = Str::before($this->order_no, '--tagend');
            $this->order_no = trim($this->order_no);

            $this->order_no = $this->removeExtraWords($this->order_no);

            //echo $this->order_no;die;
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

    private function removeExtraWords($string, $removeSpaces = true)
    {
        $string = mb_convert_encoding($string, 'UTF-8', 'UTF-8');
        $string = strip_tags($string);
        $string = preg_replace('/[?]/', '', $string);
        $string = str_replace('&nbsp;', '', $string);
        $string = str_replace('--tagend--;', '', $string);
        $string = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $string);
        $string = preg_replace('/[-\-tagend\-\-]/', '', $string);
        if($removeSpaces) {
            $string = preg_replace('/\s\s+/', ' ', $string);
        }
        $string = trim($string);
        return $string;
    }

    private function setVendor()
    {
        preg_match('/[0-9]{2}\/[0-9]{2}\/[0-9]{2}-[0-9]{4}/iUsm', $this->plainText, $date_text);
        if (is_array($date_text) && array_key_exists(0, $date_text)) {
            $date_text = explode('-', $date_text[0]);
            $date = trim($date_text[0]);
            $time = trim($date_text[1]);
            $timeD = substr($time, 0, 2);
            $timeN = substr($time, -2);
            $time = $timeD . ":" . $timeN . ":00";
            $date = $date . " " . $time;
            $this->emailDate = Carbon::parse(trim($date));
        }

        $this->vendor_email = "BedBath&Beyond@emailbedbathandbeyond.ca";

        $this->vendor_address = Str::between($this->plainText, "#" . $this->order_no, 'RVN');
        $this->vendor_address = Str::before($this->vendor_address, $this->order_no);
        $this->vendor_address = $this->removeExtraWords($this->vendor_address, false);

        $addParts = explode(',', $this->vendor_address);
        $addPart1 = trim($addParts[0]);
        $addPart1 = explode(' ', $addPart1);
        $this->vendor_city = last($addPart1);

        $addPart2 = trim($addParts[1]);

        $addPart2Exploded = explode(' ', $addPart2);
        $this->vendor_state = $addPart2Exploded[0];

        $addPart2 = $this->removeExtraWords($addPart2);

        echo $addPart2 . ' - ' . $this->vendor_state;


        $addPart2 = trim(str_replace($this->vendor_state, '', $addPart2));
        $addPart2 = explode('(', $addPart2);
        $this->vendor_zip_code = trim($addPart2[0]);
        $this->vendor_contact_no = "(" . $addPart2[1];
    }

    private function setProducts()
    {
        /**
         * Products Details
         */
        $prods_text = null;

        $prods_text = Str::between($this->htmlBody, "RVN", "SUBTOTAL");
        $prods_array = explode('<br>', $prods_text);
        unset($prods_array[0]);
        $prods_array = array_reverse($prods_array);
        $prods_array = array_reverse($prods_array);
        unset($prods_array[0]);
        $prods_array = array_reverse($prods_array);
        $prods_array = array_reverse($prods_array);

        if (is_array($prods_array) && count($prods_array) > 0) {
            unset($prods_array[(count($prods_array) - 1)]);
            $prods_array = array_reverse($prods_array);
            $prods_array = array_reverse($prods_array);
        }

        $prods = [];

        if (is_array($prods_array) && count($prods_array) > 0) {

            $rows = count($prods_array);

            if (($rows % 2) == 0) {

                $len = $rows / 2;

                for ($sNo = 0; $sNo < $len; $sNo++) {

                    $sNoLKey = $sNo;
                    $sNoLKey = $sNoLKey * 2;

                    $nameRow = trim(strip_tags($prods_array[$sNoLKey]));
                    $priceRow = trim(strip_tags($prods_array[($sNoLKey + 1)]));

                    $nameRow = str_replace('&nbsp;', ' ', $nameRow);
                    $priceRow = str_replace('&nbsp;', ' ', $priceRow);

                    $nameRow = trim($nameRow);
                    $priceRow = trim($priceRow);

                    // echo $nameRow."  --------------------------  ".$priceRow;die;

                    if ($nameRow != null && $priceRow != null) {
                        $priceRow = explode(' ', $priceRow);
                        $price = last($priceRow);

                        $nameRowExploded = explode('    ', $nameRow);
                        $name = $nameRowExploded[0];

                        $quantity = str_replace($name, '', $nameRow);
                        $quantity = trim($quantity);
                        $quantity = explode('T', $quantity);
                        $quantity = $quantity[0];
                        $quantity = trim($quantity);

                        $prods[] = [
                            'name' => $name,
                            'quantity' => $quantity,
                            'price' => $price,
                        ];

                    }

                }

            }

        }


        $this->products = $prods;
    }

    private function setTransaction()
    {
        $this->sub_total = trim(Str::between($this->plainText, 'SUBTOTAL', 'TAX'));
        $this->sub_total = $this->removeExtraWords($this->sub_total);
        $this->sub_total = explode(' ', $this->sub_total);
        $this->sub_total = $this->sub_total[0];
        $this->sub_total = explode('TAX', $this->sub_total);
        $this->sub_total = $this->sub_total[0];
        $this->sub_total = $this->removeExtraWords($this->sub_total);

        $this->tax_amount = trim(Str::between($this->plainText, 'TAX', 'BALANCE'));
        $this->tax_amount = trim($this->tax_amount);
        $this->tax_amount = rtrim($this->tax_amount, "****");
        $this->tax_amount = $this->removeExtraWords($this->tax_amount);
        $this->tax_amount = explode(' ', $this->tax_amount);
        $this->tax_amount = $this->tax_amount[0];
        $this->tax_amount = $this->removeExtraWords($this->tax_amount);
        $this->tax_amount = str_replace('*', '', $this->tax_amount);

        $this->total = trim(Str::between($this->plainText, 'BALANCE', 'Sale'));
        $this->total = $this->removeExtraWords($this->total);
        $this->total = explode(' ', $this->total);
        $this->total = $this->total[0];
        $this->total = $this->removeExtraWords($this->total);

        $this->payment_method = trim(Str::between($this->plainText, 'Sale - APPROVED', 'ENTRY'));
        $this->payment_method = $this->removeExtraWords($this->payment_method);
        $this->payment_method = trim(Str::between($this->payment_method, 'APPROVED', 'ENTRY'));
        $this->payment_method = $this->removeExtraWords($this->payment_method);
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
            echo $exception->getLine();
            echo $exception->getMessage();
            die;
            Log::error("Array Creation Error: " . $this->vendor_name . " - " . $exception->getMessage());
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
                'city' => $this->vendor_city,
                'state' => $this->vendor_state,
                'zip_code' => $this->vendor_zip_code,
                'vendor_contact_no' => $this->vendor_contact_no,
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
            'message_id' => $this->message_id
        ];
    }

    public function getDetail()
    {
        return $this->detail;
    }
}

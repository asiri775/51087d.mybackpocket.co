<?php


namespace App\Vendors;


use App\Models\Transaction;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class Champs
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

        $this->vendor_name = "Champs";
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
        if (Str::contains(Str::lower($this->plainText), Str::lower("Transaction"))) { //If it is not order/invoice then skip it.
            return true;
        }
        return false;
    }

    private function setOrderNo()
    {
        //If string contains fails then use the default email address
        if (Str::contains($this->plainText, 'Transaction')) {
            $this->order_no = strip_tags(Str::between($this->plainText, "Trans:", "Cashier"));
            $this->order_no = $this->removeExtraWords($this->order_no);
            $this->order_no = explode(' ', $this->order_no);
            $this->order_no = $this->order_no[0];
            $this->order_no = trim($this->order_no);
            $this->order_no = $this->removeExtraWords($this->order_no);
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

    private function removeExtraWords($string)
    {
        $string = mb_convert_encoding($string, 'UTF-8', 'UTF-8');
        $string = strip_tags($string);
        $string = preg_replace('/[?]/', '', $string);
        $string = str_replace('&nbsp;', '', $string);
        $string = str_replace('--tagend--;', '', $string);
        $string = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $string);
        $string = preg_replace('/[-\-tagend\-\-]/', '', $string);
        $string = preg_replace('/\s\s+/', ' ', $string);
        $string = trim($string);
        return $string;
    }

    private function setVendor()
    {
        $date_text = Str::between($this->plainText, 'Date:', 'Trans');
        $date_text = Str::after($date_text, 'Date');
        $date_text = str_replace('Time:', '', $date_text);
        $date_text = explode('--tagend--', $date_text);
        $date_text = $date_text[0];
        $date_text = ltrim($date_text, ':');
        $date_text = trim($date_text);
        preg_match('/[0-9]{2}\/[0-9]{2}\/[0-9]{4}.*[0-9].*:[0-9]{2}.*[A-Za-z]{2}/iUsm', $date_text, $date_text);
        if (is_array($date_text) && array_key_exists(0, $date_text)) {
            $date_text = $date_text[0];
            $date_text = preg_replace('/[\x00-\x1F\x7F-\xFF]/', ' ', $date_text);
            $date_textExploded = explode('  ', $date_text);
            $date_text = trim($date_textExploded[0]);
            unset($date_textExploded[0]);
            $date_textExploded = implode(' ', $date_textExploded);
            $date_textExploded = trim($date_textExploded);
            $date_text = $date_text . " " . $date_textExploded;
            $this->emailDate = Carbon::parse(trim($date_text));
        }

        $this->vendor_email = "StoreReceipt@champssports.ca";

        $this->vendor_address = Str::between($this->plainText, "excluded", 'GST/HST');
        $this->vendor_address = preg_replace('/[-\-tagend\-\-]/', '', $this->vendor_address);
        $this->vendor_address = preg_replace('/[\x00-\x1F\x7F-\xFF]/', ' ', $this->vendor_address);
        $this->vendor_address = trim($this->vendor_address);
        $this->vendor_address = ltrim($this->vendor_address, '.');
        $this->vendor_address = str_replace(' C ', ' ', $this->vendor_address);
        $this->vendor_address = trim($this->vendor_address);

        $addParts = explode(',', $this->vendor_address);
        $addPart1 = trim($addParts[0]);
        $addPart1 = explode(' ', $addPart1);
        $this->vendor_city = last($addPart1);

        $addPart2 = trim($addParts[1]);

        $addPart2Exploded = explode(' ', $addPart2);
        $this->vendor_state = $addPart2Exploded[0];

        $addPart2 = trim(str_replace($this->vendor_state, '', $addPart2));
        $addPart2 = trim($addPart2);
        $addPart2 = explode(' ', $addPart2);
        $this->vendor_zip_code = trim($addPart2[0]);
        $this->vendor_contact_no = trim(last($addPart2));


        $this->vendor_address = $this->removeExtraWords($this->vendor_address);
    }

    private function setProducts()
    {
        /**
         * Products Details
         */
        $prods = [];

        $prods_text = Str::between($this->htmlBody, "Item", "Sales");
        $prods_text = Str::after($prods_text, "Tax");
        $prods_text = Str::after($prods_text, "%");
        $prods_text = trim($prods_text);

        preg_match('/<b>(.*)<\/b>/iUsm', $prods_text, $name);
        if(is_array($name) && array_key_exists(1, $name)){
            $nameOr = $name[1];
            $name = str_replace('&nbsp;', ' ', $nameOr);

            $quantity = Str::between($prods_text, $nameOr, '$');
            $quantity = str_replace('&nbsp;', ' ', $quantity);
            $quantity = strip_tags($quantity);
            $quantity = preg_replace('/\s\s+/', ' ', $quantity);
            $quantity = trim($quantity);
            $quantityExploded = explode(' ', $quantity);
            if(is_array($quantityExploded) && array_key_exists(2, $quantityExploded)) {
                $quantity = $quantityExploded[1];
                $price = $quantityExploded[2];
                $price =  str_replace('$', '', $price);

                $prods[] = [
                    'name' => $name,
                    'quantity' => $quantity,
                    'price' => $price,
                ];
            }
        }


        $this->products = $prods;
    }

    private function setTransaction()
    {
        $this->sub_total = trim(Str::between($this->plainText, 'Subtotal', 'HST'));
        $this->sub_total = str_replace('$', '', $this->sub_total);
        $this->sub_total = $this->removeExtraWords($this->sub_total);

        $this->tax_amount = trim(Str::between($this->plainText, 'Subtotal', 'Total'));
        $this->tax_amount = trim(Str::after($this->tax_amount, 'HST'));
        $this->tax_amount = str_replace('$', '', $this->tax_amount);
        $this->tax_amount = $this->removeExtraWords($this->tax_amount);

        $this->total = trim(Str::between($this->plainText, 'Total', '_______________'));
        $this->total = str_replace('_', '', $this->total);
        $this->total = str_replace('$', '', $this->total);
        $this->total = $this->removeExtraWords($this->total);

        $this->payment_method = trim(Str::between($this->plainText, $this->total, '$'));
        $this->payment_method = trim(Str::between($this->payment_method, '____________________________________________', '$'));
        $this->payment_method = explode('      ', $this->payment_method);
        $this->payment_method = $this->payment_method[0];
        $this->payment_method = str_replace('--tagend--', '', $this->payment_method);
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

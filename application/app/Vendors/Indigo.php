<?php


namespace App\Vendors;


use App\Models\Transaction;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class Indigo
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

        $this->vendor_name = "Indigo";
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
        if (Str::contains(Str::lower($this->plainText), Str::lower("ORDER"))) { //If it is not order/invoice then skip it.
            return true;
        }
        return false;
    }

    private function setOrderNo()
    {
        //If string contains fails then use the default email address
        if (Str::contains($this->htmlBody, 'ORDER')) {
            $this->order_no = strip_tags(Str::between($this->plainText, "NUMBER:", "Your"));
            $this->order_no = strip_tags(Str::before($this->order_no, "Your"));
            $this->order_no = preg_replace('/[-\-tagend\-\-]/', '', $this->order_no);
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
        $transactionDate = Str::between($this->plainText, 'placed', 'send');
        $transactionDate = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $transactionDate);
        $transactionDate = Str::between($transactionDate, 'on', '--tagend---');
        $transactionDate = Str::after($transactionDate, 'on');
        $transactionDate = trim($transactionDate);
        $transactionDate = $transactionDate . " 00:00:00";

        $this->emailDate = Carbon::parse(trim($transactionDate));

        $this->vendor_email = "Service@online.indigo.ca";

        $this->vendor_address = Str::after($this->htmlBody, "Policy");
        $this->vendor_address = strip_tags($this->vendor_address);
        $this->vendor_address = explode('- - - - ', $this->vendor_address);
        $this->vendor_address = $this->vendor_address[0];
        $this->vendor_address = trim($this->vendor_address);
        $this->vendor_address = ltrim($this->vendor_address, '.');
        $this->vendor_address = trim($this->vendor_address);

        $this->vendor_address = preg_replace('/,/', '', $this->vendor_address, 1);


        $addParts = explode(',', $this->vendor_address);
        $addPart1 = trim($addParts[0]);
        $addPart1 = explode(' ', $addPart1);
        $this->vendor_city = last($addPart1);

        $addPart2 = trim($addParts[1]);

        $addPart2Exploded = explode(' ', $addPart2);
        $this->vendor_state = $addPart2Exploded[0];

        $addPart2 = str_replace($this->vendor_state, '', $addPart2);
        $this->vendor_zip_code = $addPart2;
        $this->vendor_zip_code = trim($addPart2);
    }

    private function setProducts()
    {
        /**
         * Products Details
         */
        $prods_text = Str::between($this->htmlBody, "PRICE", "See how");
        $prods_text = Str::before($prods_text, "See how");

        $prods = [];

        preg_match_all('/<div.*class="x_details x_GR".*><a.*>(.*)<\/a>/iUsm', $prods_text, $productNames);
        if (is_array($productNames) && array_key_exists(1, $productNames)) {
            $productNames = $productNames[1];
            foreach ($productNames as $productName) {
                if (strpos($productName, '<img') === false) {
                    $prods[] = [
                        'name' => $productName,
                        'quantity' => '',
                        'price' => '',
                    ];
                }
            }
        }

        preg_match_all('/<div.*class="x_i000000 x_details x_GR".*>(.*)<\/div>/iUsm', $prods_text, $otherInfos);
        if (is_array($otherInfos) && array_key_exists(1, $otherInfos)) {
            $otherInfos = $otherInfos[1];
            foreach ($otherInfos as $key => $otherInfo) {
                if (array_key_exists($key, $prods)) {

                    $quantity = 1;
                    $price = '';

                    if (strpos($otherInfo, 'Qty') !== false) {
                        $quantity = Str::between($otherInfo, 'Qty', '@');
                        $quantity = trim($quantity);
                    }

                    $exploded = explode('ea', $otherInfo);
                    $price = $exploded[0];
                    $price = strip_tags($price);
                    $price = Str::after($price, '$');

                    $prods[$key]['quantity'] = $quantity;
                    $prods[$key]['price'] = $price;
                }
            }
        }

        $this->products = $prods;
    }

    private function setTransaction()
    {
        $this->sub_total = trim(Str::between($this->plainText, 'Subtotal:', 'Shipping'));
        $this->sub_total = str_replace('$', '', $this->sub_total);
        $this->sub_total = $this->removeExtraWords($this->sub_total);

        $this->tax_amount = trim(Str::between($this->plainText, 'HST:', 'Order'));
        $this->tax_amount = str_replace('$', '', $this->tax_amount);
        $this->tax_amount = $this->removeExtraWords($this->tax_amount);

        $this->total = trim(Str::between($this->plainText, 'Total:', 'Paid'));
        $this->total = str_replace('$', '', $this->total);
        $this->total = $this->removeExtraWords($this->total);

        $this->payment_method = trim(Str::between($this->htmlBody, 'Paid', '$'));
        $this->payment_method = strip_tags($this->payment_method);
        $this->payment_method = explode(':', $this->payment_method);
        $this->payment_method = $this->payment_method[0];
        $this->payment_method = str_replace('by', '', $this->payment_method);
        $this->payment_method = trim($this->payment_method);
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
            /*echo $exception->getLine();
            echo $exception->getMessage();
            die;*/
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

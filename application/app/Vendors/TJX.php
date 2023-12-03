<?php


namespace App\Vendors;


use App\Models\Transaction;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class TJX
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

        $this->vendor_name = "TJX";
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
        if (Str::contains(Str::lower($this->plainText), Str::lower("Receipt ID"))) { //If it is not order/invoice then skip it.
            return true;
        }
        return false;
    }

    private function setOrderNo()
    {
        //If string contains fails then use the default email address
        if (Str::contains($this->htmlBody, 'Receipt ID')) {
            $this->order_no = strip_tags(Str::between($this->htmlBody, "Receipt ID:", "*****************************************"));
            $this->order_no = trim($this->order_no);
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

    private function setVendor()
    {
        $date_text = Str::between($this->htmlBody, "Member", "--tagend--");
        $date_text = Str::between($date_text, "________________________________________", "WE VALUE");
        $date_text = explode(' ', $date_text);
        if (is_array($date_text) && count($date_text) >= 5) {
            $date_text = $date_text[3] . ' ' . $date_text[4];
            $this->emailDate = Carbon::parse(trim($date_text));
        }

        $this->vendor_email = Str::between($this->plainText, 'TJX Canada STYLE+ <', '>');
        $this->vendor_email = explode('--tagend', $this->vendor_email);
        $this->vendor_email = $this->vendor_email[0];

        $this->vendor_address = str_replace('--tagend--', '', Str::between($this->plainText, "Check your account status", "#"));
        $this->vendor_address = ltrim($this->vendor_address, '+');
        $this->vendor_address = ltrim($this->vendor_address, '.');
        $this->vendor_address = explode('#', $this->vendor_address);
        $this->vendor_address = str_replace('+.', '', $this->vendor_address);
        $this->vendor_address = $this->vendor_address[0];

        $this->vendor_street_name = explode('GST', $this->vendor_address);
        $this->vendor_street_name = $this->vendor_street_name[0];

        $stateData = explode(' ', $this->vendor_street_name);
        $this->vendor_state = trim(last($stateData));

        $this->vendor_street_name = str_replace($this->vendor_state, '', $this->vendor_street_name);
        $this->vendor_street_name = trim($this->vendor_street_name);

    }

    private function setProducts()
    {
        /**
         * Products Details
         */
        $prods_text = null;

        $prods_text = Str::between($this->htmlBody, "SALE", "Subtotal");
        $prods_array = explode('<br>', $prods_text);

        $prods = [];
        foreach ($prods_array as $key => $value) {
            preg_match_all('/<b>(.*)<\/b>/iUsm', $value, $matches);
            if (is_array($matches) && array_key_exists(0, $matches)) {
                $matches = $matches[0];
                $price = '';
                preg_match('/\$(.*)\s/iUsm', $value, $prices);
                if (is_array($prices) && array_key_exists(1, $prices)) {
                    $price = $prices[1];
                }
                if (is_array($matches) && count($matches) >= 3) {
                    $prods[$key]['name'] = htmlspecialchars_decode(strip_tags($matches[1]) . " " . strip_tags($matches[2]));
                    $prods[$key]['quantity'] = 1;
                    $prods[$key]['price'] = floatval($price);
                }
            }
        }

        $this->products = $prods;
    }

    private function setTransaction()
    {
        $this->sub_total = trim(Str::between($this->plainText, 'Subtotal', 'ON'));
        $this->sub_total = explode(' ', $this->sub_total);
        $this->sub_total = $this->sub_total[0];
        $this->sub_total = str_replace('$', '', $this->sub_total);

        $this->tax_amount = Str::between($this->plainText, 'HST', 'Total');
        $this->tax_amount = Str::after($this->tax_amount, '%');
        $this->tax_amount = str_replace('$', '', $this->tax_amount);

        $this->total = Str::between($this->plainText, 'HST', 'TRANSACTION RECORD');
        $this->total = Str::between($this->total, '--tagend--', '--tagend--');
        $this->total = str_replace('$', '', $this->total);

        $this->payment_method = Str::between($this->plainText, '________________________________________', 'TRANSACTION RECORD');
        $this->payment_method = explode(' ', $this->payment_method);
        $this->payment_method = $this->payment_method[0];

        $this->payment_ref = Str::between($this->plainText, 'Trans#', 'Card');
        $this->payment_ref = trim($this->payment_ref);
        $this->payment_ref = explode(' ', $this->payment_ref);
        $this->payment_ref = $this->payment_ref[0];
        $this->payment_ref = strtolower($this->payment_ref);
        $this->payment_ref = str_replace('card', '', $this->payment_ref);
        $this->payment_ref = trim($this->payment_ref);

        $this->setExtraInfo();

    }

    private function setExtraInfo()
    {
        $hstText = Str::between($this->plainText, 'HST', 'Total');
        $hstText = trim($hstText);
        if ($hstText != null) {
            if (strpos($hstText, '%') !== false && strpos($hstText, '$') !== false) {
                $hstTextPercentage = Str::before($hstText, '%');
                $hstTextValue = Str::after($hstText, '$');

                $this->tax_amount = floatval($hstTextValue);

                $ex_info[] = [
                    'label' => "HST",
                    'value' => $this->tax_amount,
                    'percentage' => $hstTextPercentage,
                    'key' => 'hst',
                    'type' => 'amount'
                ];

            }
        }

        $this->extra_info = collect($ex_info)->toJson();
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
                'street_name' => $this->vendor_street_name,
                'city' => $this->vendor_city,
                'state' => $this->vendor_state,
                'unit' => $this->vendor_unit,
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
            'payment_ref' => $this->payment_ref,
            'message_id' => $this->message_id,
            'extra_info' => $this->extra_info
        ];
    }

    public function getDetail()
    {
        return $this->detail;
    }
}

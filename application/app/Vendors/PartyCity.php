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

class PartyCity
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
    private $tax_no = null;
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
    private $terminal_no = null;
    private $payment_ref = null;
    private $payment_method = null;
    private $extra_info = null;

    public function __construct($htmlBody, $plainText, $emailDate, $sender, $message_id)
    {
        $this->htmlBody = $htmlBody;
        $this->plainText = $plainText;
        $this->emailDate = $emailDate;
        $this->message_id = $message_id;

        $this->vendor_name = "PartyCity";
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

        if (Str::contains(Str::lower($this->plainText), Str::lower("Thank you for shopping with Mark"))) { //If it is not order/invoice then skip it.
            return true;
        }
        return false;
    }

    private function setOrderNo()
    {
        $order_no = null;

        $order_no = str_replace('--tagend--', '', $this->plainText);

        if (Str::contains($order_no, "Thank you for shopping at Party City")) {
            $order_no = preg_grep('/TRN/', $this->textArray);
            $order_no = implode($order_no);
            $order_no = Str::after($order_no, 'STORE');
            $order_no = 'TRN ' . trim(Str::between($order_no, 'TRN', 'REG'));
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
        if (in_array('Discount', $this->textArray)) {
            return true;
        } else {
            return false;
        }
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
        $date_text = null;
        $plainText = str_replace('--tagend--', '', $this->plainText);
        if (Str::contains(Str::lower($this->plainText), Str::lower("Party City"))) {
            //If string contains fails then use the default email address
            if (Str::contains($plainText, 'Thank you for shopping at Party City')) {
                if (Str::contains($this->htmlBody, 'Party City &lt;')) {
                    $vendorEmailStr = Str::between($this->htmlBody, "Party City &lt;", "@e.partycity.ca");
                    $this->vendor_email = strip_tags($vendorEmailStr) . '@e.partycity.ca';
                } else {
                    $this->vendor_email = "partycity@e.partycity.ca";
                }
            }
        }

        $dateStr = preg_grep('/REG 4/', $this->textArray);
        $dKeys = array_keys($dateStr);
        $date_text = $this->textArray[$dKeys[0] + 1];
        $date_text = str_replace('-', '/', $date_text);

        $date_text = preg_replace("/\r|\n|\t/", "", $date_text);

        $date_text = trim(preg_replace('/\s+/', ' ', $date_text));
        $date_text = strip_tags(Str::after($date_text, "Date:"));

        $date = Carbon::parse($date_text);
        $this->emailDate = $date;

        if (Str::contains($plainText, "Party City")) {

            $vendorStr = preg_grep('/Party City in/', $this->textArray); //str_replace('--tagend--', '', $this->plainText);
            $vKeys = array_keys($vendorStr);
            $vendor_address = $this->textArray[$vKeys[0] + 2] . ' ' . $this->textArray[$vKeys[0] + 3];
            $this->vendor_address = $vendor_address;

            $this->vendor_phone = $this->textArray[$vKeys[0] + 4];
            $this->vendor_street_name = $this->textArray[$vKeys[0] + 2];
            $vendor_address = explode(' ', $this->textArray[$vKeys[0] + 3]);
            $this->vendor_city = trim($vendor_address[0]);
            $this->vendor_state = trim($vendor_address[1]);
            $this->vendor_zip_code = trim($vendor_address[2]);
        }

        if (Str::contains($this->plainText, 'STORE')) {

            $store_no = trim(Str::between($plainText, 'STORE', 'TRN'));
        }

        $this->vendor_store_no = $store_no;

        if (Str::contains($plainText, 'HST ')) {
            $tax_no = Str::between($plainText, 'HST', 'STORE');
            $tax_no = Str::after($tax_no, 'HST');
        }

        $this->tax_no = $tax_no;
    }

    private function setProducts()
    {
        /**
         * Products Details
         */
        $prods_text = null;

        //TODO: Check with discounted email
        $prods_text = Str::between($this->plainText, "Thank you for shopping", "SUBTOTAL");
        if ($this->getDiscountIndex()) {
            $prods_text = Str::between($this->plainText, "x1", "Discount");
        }

        $prods_array = explode('--tagend--', $prods_text);
        $prods_array = array_values(array_filter($prods_array));
        $prods = [];
        foreach ($prods_array as $key => $value) {
            if (preg_match('/^T$/', $value)) {
                if (isset($prods_array[$key + 2]) && Str::contains($prods_array[$key + 2], '@')) {
                    $quantity = explode('@', $prods_array[$key + 2])[0];
                    $quantity = intval($quantity) ?? 1;
                }

                $prods[] = [
                    'name' => ($prods_array[$key - 3] ?? '') . ' ' . ($prods_array[$key - 2] ?? ''),
                    'price' => floatval(str_replace(['$', ','], '', $prods_array[$key - 1])),
                    'sku' => $prods_array[$key - 3] ?? '',
                    'description' => $prods_array[$key + 1] ?? '',
                    'quantity' => $quantity,
                ];
            }

            $quantity = 1;
        }
        return  $this->products = $prods;
    }

    private function setExtraInfo()
    {

        $textArray = array_map('trim', $this->textArray);
        $textArray = array_values(array_filter($textArray));

        $hst_index = array_search('HST', $textArray);
        $tip_index = array_search('Tip', $textArray);

        $ex_info = [];
        if ($hst_index) {
            $ex_info[] = [
                'label' => "HST",
                'value' => floatval(
                    str_replace(
                        ['$', ','],
                        '',
                        $textArray[$hst_index + 1]
                    )
                ),
                'key' => 'hst',
                'type' => 'amount'
            ];
        }

        $this->extra_info = collect($ex_info)->toJson();
    }

    private function setTransaction()
    {


        $textArray = array_map('trim', $this->textArray);
        $textArray = array_values(array_filter($textArray));

        $sub_total_index = array_search('SUBTOTAL', $textArray);
        $total_index = array_search('TOTAL', $textArray);

        if ($sub_total_index) {
            $this->sub_total = floatval(str_replace(['$', ','], '', $textArray[$sub_total_index + 1]));
        }

        if ($total_index) {
            $this->total = floatval(str_replace(['$', ','], '', $textArray[$total_index + 1]));
        }


        if ($total_index) {
            $this->payment_method = $textArray[$total_index + 2];
        }

        $this->setExtraInfo();
    }

    public function parseEmail()
    {
        try {

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
                'Hst'   =>   $this->tax_no,
                'phone' => $this->vendor_phone,
                'street_name' => $this->vendor_street_name,
                'unit' => $this->vendor_unit,
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
            'extra_info' => $this->extra_info,
            'terminal_no' => $this->terminal_no

        ];
    }
    public function getDetail()
    {

        return $this->detail;
    }
}

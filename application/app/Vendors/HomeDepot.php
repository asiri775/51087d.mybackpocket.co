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

class HomeDepot
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
    private $vendor_hst = null;
    private $vendor_qst = null;
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
    private $payment_method = null;
    private $payment_ref = null;
    private $auth_id = null;
    private $extra_info = null;

    public function __construct($htmlBody, $plainText, $emailDate, $sender, $message_id)
    {
        $this->htmlBody = $htmlBody;
        $this->plainText = $plainText;
        $this->emailDate = $emailDate;
        $this->message_id = $message_id;

        $this->vendor_name = "HomeDepot";
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
        if (Str::contains(Str::lower($this->plainText), Str::lower("TOTAL"))) { //If it is not order/invoice then skip it.
            return true;
        }
        return false;
    }

    private function setOrderNo()
    {
        $order_no = null;
        $textArrays = array_map('trim', $this->textArray);
        $textArray = array_values(array_filter($textArrays));
        $order_id = str_replace('&nbsp;', '', strip_tags(Str::after($this->htmlBody, '(416)626-9800')));
        $order = trim(substr($order_id, 2, 22));
        if ($order) {
            $p_o_box = null;
            $p_o_box = preg_grep('/NAME:/', $textArray);
            if ($p_o_box) {
                $p_o_box = implode($p_o_box);
            }
        }

        $this->order_no = $order;
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
        if (Str::contains(Str::lower($this->plainText), Str::lower("Forwarded message"))) {
            //TODO: check if there are more than forwarded messages
            $textArrays = array_map('trim', $this->textArray);
            $textArray = array_values(array_filter($textArrays));
            $date_text = array_search('Please keep this email for your records.', $textArray);
            $date_text = $textArray[$date_text - 1];

            $date_text = Str::after($this->htmlBody, $this->order_no);

            if (Str::contains($date_text, 'PM')) {
                $date_text = Str::between($this->htmlBody, $this->order_no, " PM") . 'PM';
            } else {
                $date_text = Str::between($this->htmlBody, $this->order_no, " AM") . 'AM';
            }
            $date_text = preg_replace("/\r|\n|\t/", "", $date_text);
            $date_text = trim(preg_replace('/\s+/', ' ', $date_text));
            $date_text = strip_tags($date_text);
            $date_text = explode('/', $date_text);
            $dateStr = $date_text[1] . '/' . $date_text[0] . '/' . $date_text[2];
            $this->emailDate = Carbon::parse($dateStr);

            //If string contains fails then use the default email address
            if (Str::contains($this->htmlBody, 'Please add')) {
                $vendorEmailStr = Str::between($this->plainText, "Please add", "@homedepot.com");

                $this->vendor_email = trim(strip_tags($vendorEmailStr)) . '@homedepot.com';
            } else {
                $this->vendor_email = "HomeDepotReceipt@homedepot.com";
            }
        }

        if (Str::contains($this->plainText, "193 N.")) {
            $textArrays = array_map('trim', $this->textArray);
            $textArray = array_values(array_filter($textArrays));
            $vendor_address_index = preg_grep('/^193 N.\s.*/', $textArray);
            if ($vendor_address_index) {
                $vendor_address_key = array_keys($vendor_address_index)[0];
                $vendor_address = $textArray[$vendor_address_key];
                $this->vendor_address = $vendor_address;
                $address = explode(',', $vendor_address);

                $this->vendor_street_name = trim(Str::before($address[0], 'STREET'));
                $this->vendor_city = trim(Str::after($address[0], 'STREET'));
                $state = trim($address[1]);
                $this->vendor_state = trim(explode(' ', $state)[0]);
                $this->vendor_zip_code = trim(explode(' ', $state)[1]);


                $this->vendor_phone = '(' . Str::after($textArray[$vendor_address_key + 1], '(');
            }
        }

        if (Str::contains($this->plainText, " HST ")) {
            $vendor_tax_no = null;
            $vendor_tax_index = preg_grep('/ HST /', $this->textArray);
            if ($vendor_tax_index) {
                $vendor_tax_no = trim(explode('HST', implode($vendor_tax_index))[1]);
            }

            $this->vendor_hst = $vendor_tax_no;
        }
    }

    private function setProducts()
    {
        /**
         * Products Details
         */

        $textArrays = array_map('trim', $this->textArray);
        $textArray = array_values(array_filter($textArrays));
        $prods_text = null;

        if (Str::contains(Str::lower($this->plainText), Str::lower('SUBTOTAL'))) {
            $prods_text = null;

            $prods_text = Str::after($this->plainText, "(416)626-9800");
            $prods_text = Str::before($prods_text, "SUBTOTAL");

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


            $split_delimeter = "<A>";
            $split_delimeter2 = ".";
            $split_delimeter3 = "ORDER ID:";

            $split_index = $prods_collect->filter(function ($item, $key) use ($split_delimeter, $split_delimeter2, $split_delimeter3) {
                if (Str::contains($item, $split_delimeter)) {
                    return $key;
                }
                if (Str::contains($item, $split_delimeter2)) {
                    return $key;
                }
                if (Str::contains($item, $split_delimeter3)) {
                    return $key;
                }
            });

            $products = $split_index->toArray();
            $pro_str = implode('<A>', $products);
            $prodArray = explode('<A>', $pro_str);
            $textArrays = array_map('trim', $prodArray);
            $product = array_values(array_filter($textArrays));
            $prods = [];
            for ($i = 0; $i < (count($product)); $i++) {
                if ($i % 2 == 0) {
                    if (isset($product[$i]) && $product[$i + 1]) {
                        $prods[] = [
                            'name' => $product[$i],
                            'price' => floatval(Str::after(substr($product[$i + 1], -7), ' ')),
                        ];
                    }
                }
            }
        }
        $this->products = $prods;
    }

    private function setExtraInfo()
    {

        if (Str::contains($this->htmlBody, 'GST/HST')) {
            $hst = trim(strip_tags(Str::between($this->htmlBody, "GST/HST", "TOTAL")));
        }
        $ex_info = [];
        if ($hst) {
            $ex_info[] = [
                'label' => "GST/HST",
                'value' => floatval($hst),
                'key' => 'gst_hst',
                'type' => 'amount'
            ];
        }

        $this->extra_info = collect($ex_info)->toJson();
    }

    private function setTransaction()
    {
        if (Str::contains($this->htmlBody, 'SUBTOTAL')) {
            $this->sub_total = floatval(trim(strip_tags(Str::between($this->htmlBody, "SUBTOTAL", "GST/HST"))));
        }



        $total_amount = preg_grep('/^ TOTAL\s.*/', $this->textArray);
        if ($total_amount) {
            $total_amount = trim(str_replace('TOTAL', '', implode($total_amount)));
            $this->total = floatval(str_replace('$', '', $total_amount));
        }

        $pay_ref_index = preg_grep('/AUTH CODE/', $this->textArray);
        if ($pay_ref_index) {
            $pay_ref = trim(str_replace('AUTH CODE', '', implode($pay_ref_index)));
            $this->payment_ref = $pay_ref;
        }

        $pay_method_index = preg_grep('/ TOTAL/', $this->textArray);
        if ($pay_method_index) {
            $pay_method_key = array_keys($pay_method_index)[0];
            $pay_method = $this->textArray[$pay_method_key + 1];
            $pay_method = trim(str_replace('X', '', $pay_method));
            $this->payment_method = 'X' . $pay_method;
        }

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
                'Hst'  => $this->vendor_hst,
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
            'payment_method' => $this->payment_method,
            'payment_ref' => $this->payment_ref,
            'auth_id' => $this->auth_id,
            'message_id' => $this->message_id,
            'extra_info' => $this->extra_info
        ];
    }

    public function getDetail()
    {
        return $this->detail;
    }
}

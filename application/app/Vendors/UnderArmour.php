<?php

namespace App\Vendors;


use App\Models\Transaction;
use Carbon\Carbon;
use Exception;
use FastSimpleHTMLDom\Document;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UnderArmour
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
    private $vendor_store_no;
    private $products = [];

    private $order_no;
    private $emailDate;
    private $discount;
    private $sub_total = 0;
    private $tax_amount = 0;
    private $total = 0;
    private $payment_method = null;
    private $payment_ref = null;
    private $bar_qr_code = null;
    private $extra_info = null;
    private $auth_id = null;


    public function __construct($htmlBody, $plainText, $emailDate, $sender, $message_id)
    {
        $this->htmlBody = $htmlBody;
        $this->plainText = $plainText;
        $this->emailDate = $emailDate;
        $this->message_id = $message_id;

        $this->vendor_name = "UnderArmour";
        $this->sender = $sender;
        $this->vendor_email = $this->sender->mail;

        $this->plainTextToArray();
        $this->setOrderNo();
        $this->setDiscount();
    }

    private function removeExtraWords($string)
    {
        $string = mb_convert_encoding($string, 'UTF-8', 'UTF-8');
        $string = strip_tags($string);
        $string = preg_replace('/[?]/', '', $string);
        $string = str_replace('&nbsp;', '', $string);
        $string = trim($string);
        return $string;
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
        if (Str::contains(Str::lower($this->plainText), Str::lower("Trans"))) { //If it is not order/invoice then skip it.
            return true;
        }
        return false;
    }

    public function setOrderNo()
    {
        $order_no = null;
        $order_index = array_search('Trans #:', $this->textArray);
        if ($order_index) {
            $order_no = $this->textArray[$order_index + 1];
            $order_no = Str::before($order_no, "(");
        }
        $this->order_no = $this->removeExtraWords(str_replace(' ', '', $order_no));
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
        if (Str::contains(Str::lower($this->plainText), Str::lower("Total"))) {
            //TODO: check if there are more than forwarded messages

            $order_index = array_search('Trans #:', $this->textArray);
            if ($order_index) {
                $order_no = $this->textArray[$order_index + 1];
                $order_no = Str::after($order_no, "(");
            }
            $date_text = Str::before($order_no, ")");

            $date_text = preg_replace("/\r|\n|\t/", "", $date_text);
            $date_text = trim(preg_replace('/\s+/', ' ', $date_text));
            $date_text = strip_tags(Str::after($date_text, "Date:"));
            $this->emailDate = Carbon::parse($date_text);

            //If string contains fails then use the default email address
            if (Str::contains($this->htmlBody, 'Under Armour')) {
                $vendorEmailStr = Str::between($this->htmlBody, "Please add", "@underarmour.com");
                $this->vendor_email = strip_tags($vendorEmailStr) . '@underarmour.com';
            } else {
                $this->vendor_email = "no-reply@underarmour.com";
            }

            $store_no_index = array_search('Store #:', $this->textArray);
            if ($store_no_index) {
                $this->vendor_store_no = $this->textArray[$store_no_index + 1];
            }
        }


        if (Str::contains($this->plainText, "SaleTransaction")) {
            $vendor_address = str_replace('--tagend--', '', Str::between($this->plainText, "TELL US HOW WE'RE DOING", "SaleTransaction"));
            $this->vendor_address = $this->removeExtraWords(Str::before($vendor_address, '('));
            $this->vendor_phone = $this->removeExtraWords("(" . trim(Str::after($vendor_address, "(")));
            $address = array_search('COOKSTOWN', $this->textArray);
            $this->vendor_street_name = $this->textArray[$address + 1];
            $state = explode(' ', $this->textArray[$address + 2]);
            $this->vendor_city = $state[0];
            $this->vendor_state = $state[1];
            $this->vendor_zip_code = $state[2] . ' ' . $state[3];
        }
    }

    private function setProducts()
    {
        /**
         * Products Details
         */
        $prods_text = null;
        //TODO: Check with discounted email
        $prods_text = Str::between($this->htmlBody, "Your Purchase", "Sub-Total");
        preg_match_all('/<tbody>(.*)<\/tbody>/iUsm', $prods_text, $prods_array);
        if (is_array($prods_array) && array_key_exists(1, $prods_array)) {
            $prods_array = $prods_array[1];
            $prods = [];
            foreach ($prods_array as $key => $value) {
                $html = new Document($value);
                $plainText = $html->plaintext;
                $plainText = trim(preg_replace('/\s+/', ' ', $plainText));

                $prods_array[$key] = $plainText;
            }

            foreach ($prods_array as $key => $value) {
                preg_match('/(.*) [A-Z0-9]{5}/Usm', $value, $name);
                if (is_array($name) && array_key_exists(1, $name)) {
                    $name = $name[1];

                    $value = str_replace($name, '', $value);
                    $value = trim($value);

                    preg_match('/(.*)[A-Z0-9]{2}%/Usm', $value, $sku);
                    $sku = $sku[1];

                    $value = str_replace($sku, '', $value);

                    preg_match('/TX(.*)×/Usm', $value, $quantity);
                    $quantity = $quantity[1];

                    preg_match('/× \$(.*)/sm', $value, $price);
                    $price = $price[1];

                    preg_match('/(.*)%/Usm', $value, $discountPer);
                    $discountPer = $discountPer[1];

                    preg_match('/\(\$(.*)\)/Usm', $value, $discount);
                    $discount = $discount[1];

                    $prods[] = [
                        'name' => $name,
                        'sku' => $sku,
                        'description' => $name . ' ' . $sku,
                        'price' => floatval($price),
                        'quantity' => $quantity,
                        /*'discount_percentage' => $discountPer,
                        'discount' => $discount,*/
                    ];
                }
            }
            $this->products = $prods;
        }
    }

    private function setExtraInfo()
    {

        $textArray = array_map('trim', $this->textArray);
        $textArray = array_values(array_filter($textArray));
        $hst_index = preg_grep('/^HST/', $textArray);
        $hst = $textArray[key($hst_index) + 1];
        $hst_per = implode($hst_index);

        $netAmount = array_search("Net Amount", $textArray);
        $netAmount = $textArray[$netAmount + 1];


        $ex_info = [];

        if ($netAmount) {
            $ex_info[] = [
                'label' => 'Net Amount',
                'value' => floatval(
                    str_replace(
                        '$',
                        '',
                        $netAmount
                    )
                ),
                'key' => 'net_amount',
                'type' => 'amount'
            ];
        }

        if ($hst) {
            $ex_info[] = [
                'label' => $hst_per,
                'value' => floatval(
                    str_replace(
                        '$',
                        '',
                        $hst
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

        $subtotal_index = array_search("Sub-Total", $textArray);
        $subtotal = floatval(str_replace('$', '', $textArray[$subtotal_index + 1]));

        $total_index = array_search("Total", $textArray);
        $total = floatval(str_replace('$', '', $textArray[$total_index + 1]));

        $payMethod_index = array_search("Payment Information", $textArray);
        $payMethod = $textArray[$payMethod_index + 3];
        $payMethod = substr($payMethod, -5);


        $payRef_index = preg_grep('/^Auth/', $textArray);
        $payRef = str_replace('Auth:', '', implode($payRef_index));

        $this->sub_total = $subtotal;
        $this->total = $total;
        $this->payment_method = $payMethod;
        $this->auth_id = $this->removeExtraWords($payRef);
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
            'payment_method' => $this->payment_method,
            'payment_ref' => $this->payment_ref,
            'auth_id' => $this->auth_id,
            'bar_qr_code' => $this->bar_qr_code,
            'message_id' => $this->message_id,
            'extra_info' => $this->extra_info
        ];
    }

    public function getDetail()
    {
        return $this->detail;
    }
}

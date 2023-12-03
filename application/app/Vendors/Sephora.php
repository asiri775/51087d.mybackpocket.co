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

class Sephora
{
    private $htmlBody;
    private $plainText;
    private $sender;
    private $textArray;
    private $textCollection;
    private $detail = [];

    private $vendor_name;
    private $vendor_email;
    private $vendor_store_no = null;
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
    private $br_code = null;
    private $auth_id = null;
    private $payment_ref = null;
    private $payment_method = null;
    private $extra_info = null;

    public function __construct($htmlBody, $plainText, $emailDate, $sender, $message_id)
    {
        $this->htmlBody = $htmlBody;
        $this->plainText = $plainText;
        $this->emailDate = $emailDate;
        $this->message_id = $message_id;

        $this->vendor_name = "Sephora";
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

        if (Str::contains(Str::lower($this->plainText), Str::lower("thanks for shopping at Sephora"))) { //If it is not order/invoice then skip it.
            return true;
        }
        return false;
    }

    private function setOrderNo()
    {
        $order_no = null;

        if (Str::contains($this->plainText, "thanks for shopping at Sephora")) {
            $order_no = preg_grep('/^Transaction #/', $this->textArray);
            $order_no = preg_replace('/[^0-9]/', '', $order_no);
            $order_no = implode($order_no);
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
        $date_text = null;

        if (Str::contains(Str::lower($this->plainText), Str::lower("receipt from your visit"))) {

            //TODO: check if there are more than forwarded messages

            $date_text = str_replace('--tagend--', '', $this->plainText);
            $date_text = Str::between($date_text, 'your visit on ', 'P.S.');
            $date_text = preg_replace('/[.]|[ ]/', '', $date_text);

            $date_text = Carbon::parse($date_text);

            $this->emailDate = $date_text;

            if (Str::contains($this->htmlBody, 'Sephora &lt;')) {
                $vendorEmailStr = Str::between($this->htmlBody, "Sephora &lt;", "@beauty.sephora.com");
                $this->vendor_email = strip_tags($vendorEmailStr) . '@beauty.sephora.com';
            } else {
                $this->vendor_email = "shop@beauty.sephora.com";
            }
        }


        if (Str::contains($this->plainText, "SEPHORA SHERWAY GARDENS")) {

            $this->vendor_address = null;

            $vendor_address = array_search('SEPHORA SHERWAY GARDENS', $this->textArray);


            $this->vendor_street_name = $this->textArray[$vendor_address + 1];
            $this->vendor_unit = $this->textArray[$vendor_address + 2];

            $split = trim(str_replace('$', '', $this->textArray[$vendor_address + 3]));
            $split = explode(' ', $split);

            $this->vendor_city = trim($split[0]);
            $this->vendor_state = trim($split[1]);
            $this->vendor_zip_code = trim($split[2]) . ' ' . trim($split[3]);
            $this->vendor_phone = $this->textArray[$vendor_address + 4];

            $vendor_address = str_replace('--tagend--', '', $this->plainText);
            $vendor_address = Str::between($vendor_address, 'SEPHORA SHERWAY GARDENS', '(416) ');

            $vendor_address = str_replace('MALL', 'MALL ', $vendor_address);
            $vendor_address = str_replace('TORONTO', ' TORONTO', $vendor_address);

            $this->vendor_address = $vendor_address;
        }

        if (Str::contains($this->plainText, 'Store #')) {
            $store_no = preg_grep('/^Store #/', $this->textArray);
            $store_no = implode($store_no);
            $store_no = preg_replace('/[^0-9#]/', '', $store_no);
        }

        $this->vendor_store_no = $store_no;
    }

    private function setProducts()
    {
        /**
         * Products Details
         */
        $prods_text = null;

        //TODO: Check with discounted email
        $text = preg_grep('/^Item /i', $this->textArray);
        $prods_keys = array_keys($text);
        $i = 0;
        $prods = [];
        foreach ($prods_keys as $key) {
            $i++;
            $prods[] = [
                'name'          =>  $this->textArray[$key - 1],
                'price'         =>  floatval(str_replace(['Price: ', '$', ','], '', $this->textArray[$key + 1])),
                'quantity'      =>  preg_replace('/[^0-9]/', '', $this->textArray[$key + 3]),
                'description'   =>  $this->textArray[$key]
            ];
        }

        if ($this->getDiscountIndex()) {
            $prods_text = Str::between($this->plainText, "x1", "Discount");
        }

        $this->products = $prods;
    }

    private function setExtraInfo()
    {
        $this->plainText = str_replace('--tagend--', '', $this->plainText);

        $type = Str::between($this->plainText, 'Trans Type: ', 'Payment: ');


        $account_type = Str::between($this->plainText, 'Application Label : ', 'AID: ');

        $card_number = Str::between($this->plainText, 'Payment: ', 'Card Entry:');
        $card_number = '*' . str_replace('*', '', $card_number);

        $auth_number = Str::between($this->plainText, 'Auth #: ', 'Application Label : ');

        $date = Str::between($this->plainText, 'DATE: ', 'SEPHORA SHERWAY GARDENS');
        $store_number = preg_grep('/^Store #/', $this->textArray);
        $store_number = implode(preg_replace('/^Store #/', '', $store_number));

        $name = preg_grep('/^Name/i', $this->textArray);
        $name = implode(str_replace('NAME: ', '', $name));

        $ontario_hst_percent = preg_grep('/^Ontario Hst/i', $this->textArray);
        $ontario_hst_percent = implode($ontario_hst_percent);
        $ontario_hst_percent = Str::between($ontario_hst_percent, 'HST ', '$');
        $ontario_hst_percent = str_replace(': ', '', $ontario_hst_percent);

        $ontario_hst_value = Str::between($this->plainText, 'ONTARIO HST', 'CANADA ');
        $ontario_hst_value = Str::after($ontario_hst_value, ': ');

        $canada_gst_percent = preg_grep('/^CANADA GST/i', $this->textArray);
        $canada_gst_percent = implode($canada_gst_percent);
        $canada_gst_percent = Str::between($canada_gst_percent, 'GST/TPS ', ': $');

        $canada_gst_value = Str::between($this->plainText, 'GST/TPS', 'Total');
        $canada_gst_value = Str::after($canada_gst_value, ': ');

        $barcode_number = '';
        if (Str::contains($this->htmlBody, 'csbarcode')) {
            $barcode_number = Str::after($this->htmlBody, 'csbarcode', 'style=');
            $barcode_number = Str::before($barcode_number, 'style=');
            $barcode_number = Str::after($barcode_number, 'D=');
            $barcode_number = preg_replace('/[^0-9]/', '', $barcode_number);
        } else {
            $barcode_number = '';
        }

        $ex_info = [];
        if ($type) {
            $ex_info[] = [
                'label'     =>      "Transaction Type",
                'value'     =>       $type,
                'key'       =>      'transaction_type',
                'type'      =>      'invoice_type'
            ];
        }

        if ($account_type) {
            $ex_info[] = [
                'label'      =>      'Account Type',
                'value'     =>       $account_type,
                'key'       =>      'account_type',
                'type'      =>      'name'
            ];
        }

        if ($auth_number) {
            $ex_info[] = [
                'label'      =>      'Auth Number',
                'value'     =>       $auth_number,
                'key'       =>      'auth_number',
                'type'      =>      'auth_number'
            ];
        }

        if ($date) {
            $ex_info[] = [
                'label'      =>      'Transaction Date',
                'value'     =>       $date,
                'key'       =>      'transaction_date',
                'type'      =>      'date_time'
            ];
        }

        if ($store_number) {
            $ex_info[] = [
                'label'      =>      'Store Number',
                'value'     =>      $store_number,
                'key'       =>      'store_number',
                'type'      =>      'store_number'
            ];
        }

        if ($barcode_number != '') {
            $ex_info[] = [
                'label'      =>     'Barcode Number',
                'value'     =>       $barcode_number,
                'key'       =>      'barcode_number',
                'type'      =>      'barcode'
            ];

            $this->br_code = $barcode_number;
        }

        if ($name) {
            $ex_info[] = [
                'label'      =>     'Name of Purchaser',
                'value'     =>      $name,
                'key'       =>      'name_of_purchaser',
                'type'      =>      'name'
            ];
        }

        if ($ontario_hst_percent) {
            $ex_info[] = [
                'label'      =>      'Ontario HST-' . $ontario_hst_percent,
                'value'     =>      floatval(
                    str_replace(
                        ['$', ','],
                        '',
                        $ontario_hst_value
                    )
                ),
                'key'       =>      'ontario_hst',
                'type'      =>      'amount'
            ];
        }

        if ($canada_gst_percent) {
            $ex_info[] = [
                'label'      =>      'Canada GST/TPS-' . $canada_gst_percent,
                'value'     =>      floatval(
                    str_replace(
                        ['$', ','],
                        '',
                        $canada_gst_value
                    )
                ),
                'key'       =>      'canada_gst_tps',
                'type'      =>      'amount'
            ];
        }

        $this->extra_info = collect($ex_info)->toJson();
    }

    private function setTransaction()
    {
        $this->plainText = str_replace('--tagend--', '', $this->plainText);
        $total =  Str::between($this->plainText, 'Total: ', 'Trans Type');

        $sub_total = Str::between($this->plainText, 'Subtotal: ', 'ONTARIO HST');
        $sub_total = str_replace(' ', '', $sub_total);

        $payment_method = Str::between($this->plainText, 'Application Label : ', 'AID: ');
        $payment_ref = Str::between($this->plainText, 'Auth #:', 'Application Label');
        $payment_ref = trim($payment_ref);

        $this->sub_total = floatval(str_replace(['$', ','], '', $sub_total));
        $this->total = floatval(str_replace(['$', ','], '', $total));
        $this->auth_id = $payment_ref;
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
                'store_no'  =>  $this->vendor_store_no,
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
            'payment_ref'  =>   $this->payment_ref,
            'bar_qr_code' => $this->br_code,
            'auth_id'   => $this->auth_id,
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

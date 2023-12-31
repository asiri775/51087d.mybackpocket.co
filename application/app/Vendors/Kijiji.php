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

class Kijiji
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
    private $extra_info = null;

    public function __construct($htmlBody, $plainText, $emailDate, $sender, $message_id)
    {
        $this->htmlBody = $htmlBody;
        $this->plainText = $plainText;
        $this->emailDate = $emailDate;
        $this->message_id = $message_id;

        $this->vendor_name = "Kijiji";
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
        if (Str::contains(Str::lower($this->plainText), Str::lower("Reference Number:"))) { //If it is not order/invoice then skip it.
            return true;
        }
        return false;
    }

    private function setOrderNo()
    {
        $order_no = null;
        $order_no = array_search('Reference Number:', $this->textArray);
        if ($order_no) {
            $order_no = $this->textArray[$order_no + 1];
        }
        $this->order_no = trim(str_replace('Order Placed:', '', $order_no));
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
            $date_text = Str::between($this->htmlBody, "@kijiji.ca", "Subject");

            $date_text = preg_replace("/\r|\n|\t/", "", $date_text);
            $date_text = trim(preg_replace('/\s+/', ' ', $date_text));

            $date_text = strip_tags(Str::after($date_text, "Date:"));

            $this->emailDate =  Carbon::parse($date_text);

            //If string contains fails then use the default email address
            if (Str::contains($this->htmlBody, 'Kijiji Canada &lt;')) {
                $vendorEmailStr = Str::between($this->htmlBody, "Kijiji Canada &lt;", "@kijiji.ca");
                $this->vendor_email = strip_tags($vendorEmailStr) . '@kijiji.ca';
            } else {
                $this->vendor_email = "donot-reply@kijiji.ca";
            }
        }

        if (Str::contains($this->plainText, "Merchant Information:")) {

            $vendor_address_preg = preg_grep('/©20/', $this->textArray);
            $vendor_address = Str::after(implode($vendor_address_preg), '.');
            $vendor_address =  explode('|', str_replace(',', '|', $vendor_address));

            $this->vendor_address = trim($vendor_address[1]) . ' ' .
                trim($vendor_address[2]) . ' '
                . trim($vendor_address[3]) . ' '
                . trim($vendor_address[4]) . ' '
                . trim($vendor_address[5]);
            $this->vendor_street_name = trim($vendor_address[1]);
            $this->vendor_unit = trim($vendor_address[2]);
            $this->vendor_city = trim($vendor_address[3]);
            $this->vendor_state = trim($vendor_address[4]);
            $this->vendor_zip_code = trim($vendor_address[5]);
        }

        if (Str::contains($this->plainText, "GST/HST ID:")) {
            $vendor_tax_no = str_replace('--tagend--', '', Str::after($this->plainText, "GST/HST ID:"));
            $vendor_tax_no = trim(Str::before($vendor_tax_no, "Order Details"));
            $this->vendor_hst = trim(Str::before($vendor_tax_no, 'QST ID:'));
            $this->vendor_qst = trim(Str::after($vendor_tax_no, 'QST ID:'));
        }
    }

    private function setProducts()
    {
        /**
         * Products Details
         */
        $prods_text = null;

        //TODO: Check with discounted email
        $prods_text = Str::between($this->plainText, "Order Details", "Subtotal:");
        if ($this->getDiscountIndex()) {
            $prods_text = Str::between($this->plainText, "Order Details", "Total Price:");
        }

        $prods_array = explode('--tagend--', $prods_text);
        $prods_array = array_values(array_filter($prods_array));

        $prods = [];
        foreach ($prods_array as $key => $value) {
            if (Str::contains($value, '$')) {
                $prods[] = [
                    'name' => $prods_array[$key - 2],
                    'description' => $prods_array[$key - 1],
                    'price' => floatval(
                        str_replace(
                            '$',
                            '',
                            $value
                        )
                    )
                ];
            }
        }

        $this->products = $prods;
    }

    private function setExtraInfo()
    {

        $billing_address_index = array_search('Billing Address:', $this->textArray);

        $ex_info = [];
        if ($billing_address_index) {
            $ex_info[] = [
                'label' => "Billing Address",
                'value' => $this->textArray[$billing_address_index + 1],
                'key' => 'billing address',
                'type' => 'address'
            ];
        }
        $this->extra_info = collect($ex_info)->toJson();
    }

    public function setTransaction()
    {

        $sub_total = preg_grep('/^Subtotal:\s.*/', $this->textArray);
        $sub_total = trim(str_replace('Subtotal:', '', implode($sub_total)));

        $tax = preg_grep('/^HST:\s.*/', $this->textArray);
        $tax = trim(str_replace('HST:', '', implode($tax)));

        $total_amount = preg_grep('/^Total Price:\s.*/', $this->textArray);
        $total_amount = trim(str_replace('Total Price:', '', implode($total_amount)));

        $payment_method = preg_grep('/Payment Method:/', $this->textArray);
        $payment_method = $this->textArray[key($payment_method) + 1];
        $payment_method = str_replace("Order Total:", '', $payment_method);


        $this->sub_total = floatval(str_replace('$', '', $sub_total));
        $this->tax_amount = floatval(str_replace('$', '', $tax));
        $this->total = floatval(str_replace('$', '', $total_amount));
        $this->payment_method = trim($payment_method);

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
                'Hst' => $this->vendor_hst,
                'Qst' => $this->vendor_qst,
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

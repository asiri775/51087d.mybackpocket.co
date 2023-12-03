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
use Facade\FlareClient\Flare;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GreenP
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
    private $vendor_tax_no = null;
    private $vendor_phone = null;
    private $vendor_street_name = null;
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
    private $payment_ref = null;
    private $payment_method = null;
    private $extra_info = null;

    public function __construct($htmlBody, $plainText, $emailDate, $sender, $message_id)
    {
        $this->htmlBody = $htmlBody;
        $this->plainText = $plainText;
        $this->emailDate = $emailDate;
        $this->message_id = $message_id;

        $this->vendor_name = "GreenP";
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
        //return $this->textArray;
        if (Str::contains(Str::lower($this->plainText), Str::lower("Thank you, The GreenP Team"))) { //If it is not order/invoice then skip it.
            return true;
        }
        return false;
    }

    private function setOrderNo()
    {
        $order_no = null;

        $order_no = str_replace('--tagend--', '', $this->plainText);

        //return $order_no;
        if (Str::contains($order_no, "Thank you, The GreenP Team")) {
            $order_no = Str::between($order_no, 'Transaction Number: ', 'Location ID:');
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

    private function setVendor()
    {
        //If email is forwarded mail
        $date_text = null;

        if (Str::contains(Str::lower($this->plainText), Str::lower("Parking Receipt"))) {

            //TODO: check if there are more than forwarded messages

            $date_text = str_replace('--tagend--', '', $this->plainText);

            $date_text = Str::between($date_text, 'Start:', 'End:');

            $date_text = Carbon::parse($date_text);

            $this->emailDate = $date_text;

            if (Str::contains($this->htmlBody, 'Parking Receipt &lt;')) {
                $vendorEmailStr = Str::between($this->htmlBody, "Parking Receipt &lt;", "@greenp.com");
                $this->vendor_email = strip_tags($vendorEmailStr) . '@greenp.com';
            } else {
                $this->vendor_email = "customerservice@greenp.com";
            }
        }

        if (Str::contains($this->plainText, "Location Name:")) {
            /* $vendor_address = str_replace('--tagend--', '', $this->plainText);
            $vendor_address = Str::between($vendor_address, 'Location Name:', 'License Plate:'); */
            $this->vendor_phone = '416-393-7303';
            $vendor_address = "Toronto Parking Authority 33 Queen Street East Toronto, Ontario M5C 1R5";

            $this->vendor_address = $vendor_address;
            $this->vendor_street_name = 'Toronto Parking Authority 33 Queen Street East Toronto';
            $this->vendor_city = 'Toronto';
            $this->vendor_state = 'Ontario';
            $this->vendor_zip_code = 'M5C 1R5';
        }

        $tax_no =  Str::between($this->plainText, 'HST INCLUDED ', 'If you have any');
        $tax_no = str_replace('--tagend--', '', $tax_no);
        $tax_no = preg_replace('/[#()]/', '', $tax_no);

        $this->vendor_tax_no = '#' . $tax_no;
    }

    private function setProducts()
    {
        /**
         * Products Details
         */
        $prods_text = null;

        //TODO: Check with discounted email
        $plainText = str_replace('--tagend--', '', $this->plainText);

        $location_id = 'Location ID:' . Str::between($plainText, 'Location ID:', 'Location');
        $location_name = 'Location Name:' . Str::between($plainText, 'Location Name:', 'License');
        $license_plate = 'License Plate:' . Str::between($plainText, 'License Plate:', 'Start:');
        $start_date = 'Start:' . Str::between($plainText, 'Start:', 'End');
        $end_date = 'End:' . Str::between($plainText, 'End:', 'Payment ');

        $transaction_no = 'Transaction #' . Str::between($plainText, 'Transaction Number:', 'Location ID:');

        $description = $location_id . ', ' . $location_name . ', ' . $license_plate . ', ' . $start_date . ', ' . $end_date;

        $prods = [
            'name'          =>      $transaction_no,
            'price'         =>      '',
            'description'   =>      $description
        ];
        $this->products[] = $prods;
    }

    private function setExtraInfo()
    {

        $this->plainText = str_replace('--tagend--', '', $this->plainText);

        $type = Str::between($this->plainText, 'Subject: ', 'Transaction Number');
        $type = Str::before($type, '-');
        $type = trim($type);

        $account_type = trim(Str::between($this->plainText, 'Payment Info:', 'Parking Fee'));

        $date_start = Str::between($this->plainText, 'Start:', 'End:');
        $date_end   = Str::between($this->plainText, 'End:', 'Payment Info');

        $location_id = trim(Str::between($this->plainText, 'Location ID:', 'Location Name'));

        $license_plate = Str::between($this->plainText, 'License Plate: ', 'Start:');

        $parking_fee = Str::between($this->plainText, 'Parking Fee: ', 'Total Fee:');

        $parking_location = Str::between($this->plainText, 'Location Name: ', 'License Plate:');

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
                'type'      =>      'account_details'
            ];
        }


        if ($date_start) {
            $ex_info[] = [
                'label'      =>     'Start Date',
                'value'     =>       $date_start,
                'key'       =>      'start_date',
                'type'      =>      'date_time'
            ];
        }

        if ($date_end) {
            $ex_info[] = [
                'label'      =>     'End Date',
                'value'     =>       $date_end,
                'key'       =>      'end_date',
                'type'      =>      'date_time'
            ];
        }

        if ($location_id) {
            $ex_info[] = [
                'label'      =>     'Location ID',
                'value'     =>       $location_id,
                'key'       =>      'location_id',
                'type'      =>      'parking_details'
            ];
        }

        if ($license_plate) {
            $ex_info[] = [
                'label'      =>     'License Plate',
                'value'     =>       $license_plate,
                'key'       =>      'license_plate',
                'type'      =>      'parking_details'
            ];
        }

        if ($parking_fee) {
            $ex_info[] = [
                'label'      =>     'Parking Fee',
                'value'     =>       $parking_fee,
                'key'       =>      'parking_fee',
                'type'      =>      'parking_details'
            ];
        }

        $this->extra_info = collect($ex_info)->toJson();
    }

    private function setTransaction()
    {
        $this->plainText = str_replace('--tagend--', '', $this->plainText);

        $total =  trim(Str::between($this->plainText, 'Total Fee:', 'HST INCLUDED'));
        $payment_method = trim(Str::between($this->plainText, 'Payment Info: ', 'Parking Fee:'));
        $payment_ref = Str::between($this->plainText, 'Transaction Number: ', 'Location ID');

        $this->sub_total = null;
        $this->tax_amount = '';
        $this->total = floatval(str_replace('$', '', $total));
        $this->payment_ref = '';
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
                'Hst' => $this->vendor_tax_no,
                'phone' => $this->vendor_phone,
                'address' => $this->vendor_address,
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
            'payment_ref' => $this->payment_ref,
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

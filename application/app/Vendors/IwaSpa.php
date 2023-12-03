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

class IwaSpa
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

        $this->vendor_name = "iwa Spa";
        $this->sender = $sender;
        $this->vendor_email = $this->sender->mail;

        $this->plainTextToArray();
        $this->setOrderNo();
        $this->setDiscount();
    }

    /**
     *
     */
    private function plainTextToArray(){
        /**
         * Convert plaintext into array
         */
        $tmp_content = explode('--tagend--', $this->plainText);

        //removing empty elements from content array
        $tmp_content = array_values(array_filter($tmp_content));

        //removing extra spaces from array
        $this->textArray = array_filter($tmp_content, function($e){
            return preg_replace('/\s+/', ' ', $e);
        });
        $this->textCollection = collect($this->textArray);
    }

    //TODO: Set configuration, for example start and end point of parsing
    private function isInvoice(){
        if(Str::contains(Str::lower($this->plainText), Str::lower("Order #"))){ //If it is not order/invoice then skip it.
            return true;
        }
        return false;
    }

    private function setOrderNo(){
        $order_no = null;
        
        $order_index = array_search('Order #', $this->textArray);
        if($order_index){
            $order_no =  $this->textArray[$order_index + 1];
        }
        $this->order_no = $order_no;
    }

    private function transactionExists(){
        $transaction_exists = Transaction::where('order_no', $this->order_no)->exists();
        if($transaction_exists){
            return true;
        }
        return false;
    }

    private function getDiscountIndex(){
        return array_search('Discount', $this->textArray);
    }

    private function setDiscount(){
        if($this->getDiscountIndex()){
            $this->discount = $this->textArray[$this->getDiscountIndex() + 1];
        }
    }

    private function setVendor(){
        //If email is forwarded mail
        if (Str::contains(Str::lower($this->plainText), Str::lower("Order #"))) {
            //TODO: check if there are more than forwarded messages
            
            $order_index = array_search('Order #', $this->textArray);
            if($order_index){
                $date_text =  $this->textArray[$order_index - 1];
                $date_text = $date_text;
            }
            $date_text = preg_replace("/\r|\n|\t/", "", $date_text);
            $date_text = trim(preg_replace('/\s+/', ' ', $date_text));
            $date_text = strip_tags(Str::after($date_text, "Date:"));
            $this->emailDate =  Carbon::parse($date_text);
    
            //If string contains fails then use the default email address
            
            if(Str::contains($this->htmlBody, 'iwa Spa Blue Mountain Village')){
                $vendorEmailStr = Str::between($this->htmlBody, "iwa Spa Blue Mountain Village [mailto:", "@secure-booker.com");
                $this->vendor_email = strip_tags($vendorEmailStr) . '@secure-booker.com';
            } else{
                $this->vendor_email = "emails@secure-booker.com";
            }
        }
       
        if(Str::contains($this->plainText, "View Map")){
            $phone_index = array_search("Visit our Website", $this->textArray);
            if($phone_index){
                $phone =  $this->textArray[$phone_index - 1];
            }
           $address = str_replace('--tagend--', '', Str::between($this->plainText, "Thanks again.", "View Map"));
           $this->vendor_address = str_replace('iwa Spa','', $address).' '. $phone;
        }
    }

    private function setProducts(){
        /**
         * Products Details
         */
        $prods_text = null;

        //TODO: Check with discounted email
        $prods_text = Str::between($this->plainText, "Items", "Sub Total");
        if($this->getDiscountIndex()){
            $prods_text = Str::between($this->plainText, "x1", "Discount");
        }

        $prods_array = explode('--tagend--', $prods_text);
        $prods_array = array_values(array_filter($prods_array));

        $prods = [];
        foreach ($prods_array as $key => $value){
            if(Str::contains($value, '$')){
                $prods[] = [
                    'name' => $prods_array[$key - 1],
                    'price' => $value
                ];
            }
        }

        $this->products = $prods;
    }

    private function setExtraInfo(){

        $textArray = array_map('trim', $this->textArray);
        $textArray = array_values(array_filter($textArray));
        
        $tip_index = array_search("Tip", $textArray);
        $tip = $textArray[$tip_index + 1];

        $ex_info = [];

        if($tip) {
            $ex_info[] = [
                'label' => 'Tip',
                'value' => $tip,
                'key'   => 'net_amount',
                'type'  => 'amount'
            ];
        }

        $this->extra_info = collect($ex_info)->toJson();
    }

    private function setTransaction(){
        $textArray = array_map('trim', $this->textArray);
        $textArray = array_values(array_filter($textArray));

        $subtotal_index = array_search("Sub Total", $textArray);
        $subtotal = $textArray[$subtotal_index + 1];
        
        $total_index = array_search("Grand Total", $textArray);
        $total = $textArray[$total_index + 1];

        $tax_index = array_search("Taxes", $textArray);
        $tax = $textArray[$tax_index + 1];

        $payMethod_index = array_search("Payment", $textArray);
        $payMethod = $textArray[$payMethod_index + 1];
        

        $this->sub_total = $subtotal;
        $this->total = $total;
        $this->tax_amount = $tax;
        $this->payment_method = $payMethod;
       
        $this->setExtraInfo();
    }

    public function parseEmail(){
        try{

            if(!$this->isInvoice()) return false;

            $this->setOrderNo();

            /**
             * Check if the transaction/order already exists then return false stop further
             * proceeding to avoid any duplication
             */
            if($this->transactionExists()) return false;

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
        } catch (Exception $exception){
            Log::error("Array Creation Error: " . $exception->getMessage());
            return false;
        }
    }

    public function setDetail(){
        $this->detail = [
            'vendor' => [
                'email' => $this->vendor_email,
                'name' => $this->vendor_name,
                'address' => $this->vendor_address,
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

    public function getDetail(){
        return $this->detail;
    }
}

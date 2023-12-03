<?php


namespace App\Vendors;


use App\Models\Transaction;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class Levis
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

        $this->vendor_name = "Levis";
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
        if (Str::contains(Str::lower($this->plainText), Str::lower("Receipt"))) { //If it is not order/invoice then skip it.
            return true;
        }
        return false;
    }

    private function setOrderNo()
    {
        //If string contains fails then use the default email address
        if (Str::contains($this->htmlBody, 'Transaction')) {
            $this->order_no = strip_tags(Str::between($this->htmlBody, "Transaction:", "Cashier"));
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
        $date_text = Str::between($this->plainText, "Time:", "Store Code");
        $date_text = Str::between($date_text, "--tagend--", "--tagend----tagend----tagend");
        $date_text = explode('--tagend--', $date_text);
        $date_text = $date_text[0];
        $this->emailDate = Carbon::parse(trim($date_text));

        $this->vendor_email = Str::between($this->plainText, 'Levi\'s Stores <', '>');
        $this->vendor_email = explode('--tagend', $this->vendor_email);
        $this->vendor_email = $this->vendor_email[0];

        $this->vendor_address = str_replace('--tagend--', '', Str::between($this->plainText, "Time:", "Store Code"));
        $this->vendor_address = str_replace($date_text, '', $this->vendor_address);

        $explodedAddress = explode(',', $this->vendor_address);

        $lastAdded = trim(last($explodedAddress));
        if ($lastAdded != null) {
            $lastAdded = explode(' ', $lastAdded);
            if (is_array($lastAdded) && count($lastAdded) >= 1) {
                $this->vendor_state = trim($lastAdded[0]);
                unset($lastAdded[0]);
                $lastAdded = implode(' ', $lastAdded);
                $this->vendor_zip_code = trim($lastAdded);
            }
        }

        if (is_array($explodedAddress) && array_key_exists(1, $explodedAddress)) {
            $second = trim($explodedAddress[1]);
            $secondExploded = explode(' ', $second);
            $this->vendor_city = trim(last($secondExploded));
        }


    }

    private function setProducts()
    {
        $prods = [];

        preg_match('/imgsrc_url_32(.*)Subtotal/iUsm', $this->htmlBody, $matches);
        if (is_array($matches) && array_key_exists(1, $matches)) {
            $productsHtml = trim($matches[1]);
            if ($productsHtml != null) {


                preg_match_all('/font-size: 22px; line-height: 32px; text-decoration: none;.*<b>(.*)<\/b>/iUsm', $productsHtml, $productNamePrices);
                if (is_array($productNamePrices) && array_key_exists(1, $productNamePrices)) {
                    $productNamePrices = $productNamePrices[1];

                    preg_match_all('/QTY:.*(.*)</iUsm', $productsHtml, $quantities);
                    if(is_array($quantities) && array_key_exists(1, $quantities)){
                        $quantities = $quantities[1];
                    }

                    if (is_array($productNamePrices) && count($productNamePrices) >= 2) {

                        $rows = count($productNamePrices);

                        if (($rows % 2) == 0) {

                            $len = $rows / 2;

                            for ($sNo = 0; $sNo < $len; $sNo++) {

                                $product = [];

                                $sNoLKey = $sNo;
                                $sNoLKey = $sNoLKey * 2;

                                $name = $productNamePrices[$sNoLKey];
                                $price = $productNamePrices[($sNoLKey + 1)];

                                if (strpos($name, '% OFF') !== false) {
                                    $price = str_replace('-$', '', $price);
                                    $this->discount = $price;
                                } else {

                                    $price = str_replace('$', '', $price);

                                    $product['name'] = $name;
                                    $product['price'] = $price;

                                    $quantity = null;
                                    if(is_array($quantities) && array_key_exists($sNo, $quantities)){
                                        $quantity = trim($quantities[$sNo]);
                                    }

                                    $product['quantity'] = $quantity;


                                    if (is_array($product) && count($product) > 0) {
                                        $prods[] = $product;
                                    }
                                }

                            }

                        }

                    }
                }


            }
        }


        $this->products = $prods;
    }

    private function setTransaction()
    {
        $amounts = Str::between($this->plainText, 'TAX', 'Sign up for');
        $amounts = str_replace('--tagend--', '', $amounts);

        $this->sub_total = Str::between($amounts, 'Total$', '$');

        $exploded = explode('$', $this->sub_total);
        $this->sub_total = $exploded[0];

        $this->tax_amount = Str::between($amounts, 'Total$' . $this->sub_total . '$', '$');
        $exploded = explode('$', $this->tax_amount);
        $this->tax_amount = $exploded[0];

        $this->total = explode(' ', $amounts);
        $this->total = trim(last($this->total));
        $this->total = str_replace('$', '', $this->total);

        $this->payment_method = Str::between($amounts, '$' . $this->total, ' $');

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

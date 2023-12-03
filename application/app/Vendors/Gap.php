<?php


namespace App\Vendors;


use App\Models\Transaction;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class Gap
{
    private $htmlBody;
    private $plainText;
    private $sender;
    private $textArray;
    private $textCollection;
    private $detail = [];

    private $vendor_name;
    private $vendor_email;
    private $vendor_contact_no = null;
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

        $this->vendor_name = "Gap";
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
        if (Str::contains(Str::lower($this->plainText), Str::lower("Trans"))) { //If it is not order/invoice then skip it.
            return true;
        }
        return false;
    }

    private function setOrderNo()
    {
        //If string contains fails then use the default email address
        if (Str::contains($this->htmlBody, 'Trans')) {
            $this->order_no = strip_tags(Str::between($this->htmlBody, "Trans.:", "Store"));
            $this->order_no = explode('&nbsp;', $this->order_no);
            $this->order_no = $this->order_no[1];
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

    private function removeExtraWords($string)
    {
        $string = mb_convert_encoding($string, 'UTF-8', 'UTF-8');
        $string = strip_tags($string);
        $string = preg_replace('/[?]/', '', $string);
        $string = str_replace('&nbsp;', '', $string);
        $string = trim($string);
        return $string;
    }

    private function setVendor()
    {
        $dateAndNumberText = Str::between($this->plainText, "Tel.", "Trans");
        $dateAndNumberText = explode('Trans', $dateAndNumberText);
        $dateAndNumberText = $dateAndNumberText[0];
        $dateAndNumberText = $this->removeExtraWords($dateAndNumberText);

        preg_match('/\d{2}\/\d{2}\/\d{4}/iUsm', $dateAndNumberText, $dateMatch);
        if (is_array($dateMatch) && array_key_exists(0, $dateMatch)) {
            $dateMatch = $dateMatch[0];
            $dateAndNumberTextExploded = explode($dateMatch, $dateAndNumberText);
            if (is_array($dateAndNumberTextExploded)) {
                if (array_key_exists(0, $dateAndNumberTextExploded)) {
                    $this->vendor_contact_no = $dateAndNumberTextExploded[0];
                }
                if (array_key_exists(1, $dateAndNumberTextExploded)) {
                    $dateMatch = trim($this->removeExtraWords($dateMatch)) . " " . trim($this->removeExtraWords($dateAndNumberTextExploded[1]));
                    $dateMatch = explode(' ', $dateMatch);
                    $secPart = $dateMatch[1];
                    $secPart = $this->removeExtraWords($secPart);
                    $secPart = str_replace(' ', '', $secPart);
                    $secPart = preg_replace('/\s+/', '', $secPart);
                    $secPart = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $secPart);
                    $secPart = preg_replace('/[\x00-\x1F\x7F]/', '', $secPart);
                    $secPart = preg_replace('/[\x00-\x1F\x7F]/u', '', $secPart);


                    $dateMatch = trim($dateMatch[0]) . " " . trim($secPart);
                    $this->emailDate = Carbon::parse(trim($dateMatch));
                }
            }
        }

        $this->vendor_email = Str::between($this->plainText, 'Gap Factory Store <', '>');
        $this->vendor_email = explode('--tagend', $this->vendor_email);
        $this->vendor_email = $this->vendor_email[0];

        $this->vendor_address = Str::between($this->htmlBody, "GAP OUTLET -", "Tel");
        $explodedAddress = explode('<br></td></tr><tr><td align="center"', $this->vendor_address);
        $this->vendor_address = $explodedAddress[1];
        $this->vendor_address = explode('padding :  0;">', $this->vendor_address);
        $this->vendor_address = $this->vendor_address[1];
        $this->vendor_address = str_replace('&nbsp;', ' ', $this->vendor_address);
        $this->vendor_address = strip_tags($this->vendor_address);
        $this->vendor_address = trim($this->vendor_address);
        $this->vendor_address = $this->removeExtraWords($this->vendor_address);
        $addressExploded = explode('                  ', $this->vendor_address);

        $this->vendor_address = trim($addressExploded[0]) . " " . trim($addressExploded[1]);

        $cityStateZip = trim($addressExploded[1]);
        $cityStateZip = explode(' ', $cityStateZip);
        $this->vendor_city = $cityStateZip[0];
        $this->vendor_state = $cityStateZip[1];
        if (array_key_exists(3, $cityStateZip)) {
            $this->vendor_zip_code = $cityStateZip[2] . " " . $cityStateZip[3];
        }
    }

    private function setProducts()
    {
        $prods = [];

        $productsHtml = Str::between($this->htmlBody, 'SALE', 'Total Discount');
        $productsHtml = str_replace('&nbsp;', ' ', $productsHtml);
        $productsHtml = Str::between($productsHtml, 'align="center">', 'Total Discount');

        $productsData = explode('<br> <br> ', $productsHtml);

        $productsDataTemp = $productsData;
        $productsData = [];
        if (is_array($productsDataTemp) && count($productsDataTemp) > 0) {
            foreach ($productsDataTemp as $productDataTemp) {
                $productDataTemp = explode('<br><br>', $productDataTemp);
                if (is_array($productDataTemp) && count($productDataTemp) > 0) {
                    foreach ($productDataTemp as $productDataTe) {
                        $productsData[] = $productDataTe;
                    }
                }
            }
        }

        if (is_array($productsData) && count($productsData) > 0) {
            foreach ($productsData as $productData) {
                $productData = trim($productData);
                if ($productData != null) {
                    $productDataExploded = explode('@', $productData);
                    $productData0 = $productDataExploded[0];

                    $productData0Exploded = explode(' T', $productData0);
                    $namePrice = $productData0Exploded[0];
                    $productDataQtySku = $productData0Exploded[1];
                    $productDataQtySku = explode('   ', $productDataQtySku);
                    $namePrice = explode('     ', $namePrice);

                    $price = explode('<br>', $productDataExploded[1]);
                    $price = trim($price[0]);

                    $sku = $this->removeExtraWords($productDataQtySku[0]);
                    $qty = str_replace($sku, '', $productData0Exploded[1]);

                    $prods[] = [
                        'name' => $this->removeExtraWords($namePrice[0]),
                        'sku' => $sku,
                        'qty' => $this->removeExtraWords($qty),
                        'price' => $this->removeExtraWords($price),
                    ];


                }
            }
        }

        $this->products = $prods;
    }

    private function setTransaction()
    {
        $amounts = Str::between($this->plainText, 'Subtotal', 'GST');
        $amounts = $this->removeExtraWords($amounts);

        $amounts = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $amounts);

        $this->sub_total = Str::before($amounts, 'GST/HST');
        $this->sub_total = $this->removeExtraWords($this->sub_total);

        $this->total = Str::between($amounts, 'Total', 'EntryMethod');
        $this->total = Str::after($this->total, 'Total');
        $this->total = explode(' ', $this->total);
        $this->total = $this->total[0];
        $this->total = $this->removeExtraWords($this->total);

        $this->tax_amount = Str::between($amounts, 'TotalTax', 'Total');
        $this->tax_amount = $this->removeExtraWords($this->tax_amount);
        $this->tax_amount = Str::before($this->tax_amount, 'Total');
        $this->tax_amount = $this->removeExtraWords($this->tax_amount);

        $this->payment_method = Str::between($amounts, $this->total, $this->total);
        $this->payment_method = $this->removeExtraWords($this->payment_method);
        $this->payment_method = trim($this->payment_method);
        $this->payment_method = Str::before($this->payment_method, $this->total);
        $this->payment_method = trim($this->payment_method);

        $this->discount = Str::between($this->plainText, 'Total Discount', 'Subtotal');
        $this->discount = $this->removeExtraWords($this->discount);
        $this->discount = Str::between($this->discount, 'Total Discount', 'Subtotal');
        $this->discount = $this->removeExtraWords($this->discount);
        $this->discount = ltrim('-', $this->discount);
        $this->discount = trim($this->discount);

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

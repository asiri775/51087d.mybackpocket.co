<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Transaction;
use App\Models\Vendor;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Webklex\IMAP\Client;
use PHPHtmlParser\Dom;
use FastSimpleHTMLDom\Document;

class ParseEmailController extends Controller
{

    public function index()
    {

        //        $oClient = new Client([
        //            'host'          => 'mail.codestlab.com',
        //            'port'          => 993,
        //            'encryption'    => 'ssl',
        //            'validate_cert' => false,
        //            'username'      => 'test@codestlab.com',
        //            'password'      => 'Ilvpakistan1@',
        //            'protocol'      => 'imap'
        //        ]);
        $oClient = new Client([
            'host' => env("IMAP_HOST"),
            'port' => env("IMAP_PORT"),
            'encryption' => env("IMAP_ENCRYPTION"),
            'validate_cert' => env("IMAP_VALIDATE_CERT"),
            'username' => env("IMAP_USERNAME"),
            'password' => env("IMAP_PASSWORD"),
            'protocol' => env("IMAP_PROTOCOL")
        ]);

        $vendors_list = [
            ['name' => 'Uber', 'host' => 'uber.com'],
            ['name' => 'SkipTheDishes', 'host' => 'skipthedishes.com'],
            ['name' => 'Starbucks', 'host' => 'starbucks.com'],
            ['name' => 'Apple', 'host' => 'email.apple.com'],
            ['name' => 'PizzaPizza', 'host' => 'pizzapizza.ca'],
            ['name' => 'CascadesRecoveryEtobic', 'host' => 'moneris.com'],
            ['name' => 'Dominos', 'host' => 'dominos.ca'],
            ['name' => 'Kijiji', 'host' => 'kijiji.ca'],
            ['name' => 'Amazon', 'host' => 'Amazon.ca'],
            ['name' => 'HomeDepot', 'host' => 'homedepot.com'],
            ['name' => 'Marks', 'host' => '@marks.com'],
            ['name' => 'CanadianTire', 'host' => 'canadiantire.ca'],
            ['name' => 'NordStrom', 'host' => 'eml.nordstrom.com'],
            ['name' => 'Bell', 'host' => 'bell.ca'],
            ['name' => 'DrugMart', 'host' => 'e.shoppersdrugmart.ca'],
            ['name' => 'Sephora', 'host' => 'beauty.sephora.com'],
            ['name' => 'Ikea', 'host' => 'ikea.com'],
            ['name' => 'McDonald', 'host' => 'ca.mcdonalds.com'],
            ['name' => 'Spotify', 'host' => 'spotify.com'],
            ['name' => 'GreenP', 'host' => 'greenp.com'],
            ['name' => 'AldoShoes', 'host' => 'e.aldoshoes.com'],
            ['name' => 'BestBuy', 'host' => 'bestbuy.ca'],
            ['name' => 'UnderArmour', 'host' => '@underarmour.com'],
            ['name' => 'IwaSpa', 'host' => '@secure-booker.com'],
            ['name' => 'PartyCity', 'host' => 'e.partycity.ca'],


            ['name' => 'Earls', 'host' => 'xpotech.net'],
            ['name' => 'TJX', 'host' => 'info.tjx.com'],
            ['name' => 'Levis', 'host' => 'e.levi.com'],
            ['name' => 'Gap', 'host' => 'email.gapcanada.ca'],
            ['name' => 'OldNavy', 'host' => 'email.oldnavy.ca'],
            ['name' => 'UnderArmour', 'host' => 'underarmour.com'],
            ['name' => 'BedBath', 'host' => 'emailbedbathandbeyond.ca'],
            ['name' => 'Champs', 'host' => 'champssports.ca'],
            ['name' => 'Indigo', 'host' => 'online.indigo.ca'],
            ['name' => 'Sobeys', 'host' => 'support@caperlab.com'],
        ];

        //Connect to the IMAP Server
        $oClient->connect();

        //Get all Mailboxes
        /** @var \Webklex\IMAP\Support\FolderCollection $aFolder */

        $aFolder = $oClient->getFolder('INBOX');
        // $aFolder = $oClient->getFolder('INBOX/rehantemplates');

        $detail = [];

        //TODO: Get only INBOX folder emails instead of looping through all folders.
        //TODO: Create Transactions logs to know which email is parsed
        //Loop through every Mailbox
        /** @var \Webklex\IMAP\Folder $oFolder */


        //Get all Messages of the current Mailbox $oFolder
        /** @var \Webklex\IMAP\Support\MessageCollection $aMessage */
        $aMessage = $aFolder->messages()->all()->get();

        /** @var \Webklex\IMAP\Message $oMessage */
        foreach ($aMessage as $oMessage) {

            $emailDate = $oMessage->getDate();
            //Get email Body in HTML
            $body = $oMessage->getHTMLBody(true);

            if ($body) { //If body is not empty
                $sender_array = collect($oMessage->getSender());
                $sender = $sender_array->first(); //get host $sender->host

                //                $body = file_get_contents('https://test.codestlab.com/directemail.html');
                //Remiving tabs spaces and new lines
                $body = preg_replace("/\r|\n|\t/", "", $body);

                $htmlBody = $body; //because plain text is not removing vendor email in the case of forwarded message

                /**
                 * Inserting delimeter --tagend-- before converting the html body into plaintext.
                 * We will use that delimeter in explode so that we can get each tag text into
                 * array element
                 */
                $body = str_replace('</', '--tagend--</', $body);

                //Parse HTML into String
                $html = new Document($body);

                $plainText = $html->plaintext;
                $plainText = trim(preg_replace('/\s+/', ' ', $plainText));

                //Getting vendor from email
                $vendor_name = '';
                foreach ($vendors_list as $vendor) {
                    if (Str::contains(Str::lower($plainText), Str::lower($vendor['host']))) {
                        $vendor_name = $vendor['name'];
                        break;
                    }
                }

                // var_dump($vendor_name);die;

                if ($vendor_name) { //If the email is from the vendor. Skip the emails sent by the person which is not in our vendor list
                    //                        $content = $this->{$vendor_name}($plainText, $htmlBody, $emailDate, $sender);
                    //                        $ct = "/app/Vendors/" . $vendor_name;
                    //                        $class = 'App\Vendors\Uber';

                    $v_class = "App\Vendors\\$vendor_name";
                    $v_content = new $v_class($htmlBody, $plainText, $emailDate, $sender, $oMessage->getMessageId());
                    $content = $v_content->parseEmail();
                    $content = Helper::cleanIllegalThings($content);

                    /*echo '</br></br><h2>' . $vendor_name . '</h2></br></br><pre>';
                    print_r($content);
                    echo '</pre></br></br>';

                    die;*/

                    if ($content) {
                        // $oMessage->move('INBOX/parsedemails');
                    }

                    $detail[] = $content;

                    /**
                     * If there is any exception or order already exists then dont need to create
                     * record.
                     */
                    if ($vendor_name == 'Bell') {
                        if ($content && is_array($content)) {
                            try {

                                foreach ($content as $value) {
                                    $vendor = Vendor::firstUpdateOrCreate(
                                        ['name' => $value['vendor']['name']],
                                        $value['vendor']
                                    );

                                    $products = data_fill($value['products'], '*.vendor_id', $vendor->id);

                                    //TODO: Make sure do we need the following check or not
                                    $transaction_exists = Transaction::where('order_no', $value['transaction']['order_no'])->exists();
                                    if (!$transaction_exists) {
                                        $transaction_data = data_fill($value['transaction'], 'vendor_id', $vendor->id);
                                        if (!isset($value['transaction']['transaction_no'])) {
                                            $transaction_no = $vendor->id . str_replace(['-', ':', ' '], '', $transaction_data['transaction_date']);
                                            $transaction_data = data_fill($value['transaction'], 'transaction_no', $transaction_no);
                                        }


                                        $transaction_data['user_id'] = 1;
                                        $transaction_data['transaction_time'] = date('H:i:s', strtotime($transaction_data['transaction_date']));
                                        $transaction = Transaction::create($transaction_data);

                                    }

                                    //                echo "<pre>"; print_r($products);
                                    foreach ($products as $p) {

                                        $purchase_data = [
                                            'transaction_id' => $transaction->id,
                                            'price' => $p['price'],
                                            'quantity' => $p['quantity'] ?? 1,
                                        ];

                                        unset($p['quantity']);

                                        $product = Product::firstOrCreate(
                                            ['name' => $p['name']],
                                            $p
                                        );

                                        $purchase_data['product_id'] = $product->id;

                                        if (!$transaction_exists) {

                                            $purchase = new Purchase();
                                            $purchase->transaction_id = $purchase_data['transaction_id'];
                                            $purchase->product_id = $purchase_data['product_id'];
                                            $purchase->price = round($purchase_data['price'], 2);
                                            $purchase->quantity = $purchase_data['quantity'];
                                            $purchase->save();
                                        }
                                    }
                                }
                            } catch (Exception $exception) {
                                Log::error("Email Parsing Error DB: " . $exception->getMessage());
                            }
                        }
                    } else {

                        if ($content && is_array($content)) {
                            try {
                                $vendor = Vendor::firstUpdateOrCreate(
                                    ['name' => $content['vendor']['name']],
                                    $content['vendor']
                                );

                                $products = data_fill($content['products'], '*.vendor_id', $vendor->id);

                                //TODO: Make sure do we need the following check or not
                                $transaction_exists = Transaction::where('order_no', $content['transaction']['order_no'])->exists();
                                if (!$transaction_exists) {
                                    $transaction_data = data_fill($content['transaction'], 'vendor_id', $vendor->id);
                                    if (!isset($content['transaction']['transaction_no'])) {
                                        $transaction_no = $vendor->id . str_replace(['-', ':', ' '], '', $transaction_data['transaction_date']);
                                        $transaction_data = data_fill($content['transaction'], 'transaction_no', $transaction_no);
                                    }

                                    $transaction_data['user_id'] = 1;
                                    $transaction_data['transaction_time'] = date('H:i:s', strtotime($transaction_data['transaction_date']));
                                    $transaction = Transaction::create($transaction_data);

                                }
                                foreach ($products as $p) {

                                    $purchase_data = [
                                        'transaction_id' => $transaction->id,
                                        'price' => $p['price'],
                                        'quantity' => $p['quantity'] ?? 1,
                                    ];

                                    unset($p['quantity']);

                                    $product = Product::firstOrCreate(
                                        ['name' => $p['name']],
                                        $p
                                    );

                                    $purchase_data['product_id'] = $product->id;

                                    if (!$transaction_exists) {

                                        $purchase = new Purchase();
                                        $purchase->transaction_id = $purchase_data['transaction_id'];
                                        $purchase->product_id = $purchase_data['product_id'];
                                        $purchase->price = round($purchase_data['price'], 2);
                                        $purchase->quantity = $purchase_data['quantity'];
                                        $purchase->save();
                                    }
                                }
                            } catch (Exception $exception) {
                                echo $exception->getMessage();
                                echo $exception->getLine();
                                die;
                                Log::error("Email Parsing Error DB: " . $exception->getMessage());
                            }
                        }
                    }
                }
            }
        }


        //dd($detail);
        return "IMAP";
    }
}

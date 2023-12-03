<?php

namespace App\Models;

use App\Helpers\Helper;
use App\Constants\StatementConstants;
use App\Models\Envelope;
use App\Models\Budget;
use Codedge\Fpdf\Fpdf\Fpdf;
use Dubocr\PdfUtils\Facades\PdfUtils;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Spatie\PdfToText\Pdf;


class BankAccountStatement extends Model
{


    protected $table = "bank_account_statements";
    protected $guarded = [];

    protected $fillable = ['bank_account_id', 'name', 'statement_file', 'status', 'content', 'is_processed'];

    public function delete()
    {
        //delete images
        $statementFile = storage_path() . '/app/bank_statements/' . $this->statement_file;
        $statementDir = storage_path("app/bank_statements/" . $this->id . "/");
        if (file_exists($statementFile)) {
            unlink($statementFile);
        }
        if (File::isDirectory($statementDir)) {
            File::deleteDirectory($statementDir);
        }

        Transaction::where('bank_account_statement_id', $this->id)->delete();
        return parent::delete();
    }

    private static function verifyDateThing(array $values)
    {
        $data = [];
        $month = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
        foreach ($values as $value) {
            $appended = false;

            $value = trim($value);
            if (strlen($value) > 6) {
                $firstThree = strtolower(substr($value, 0, 3));
                if (in_array($firstThree, $month)) {
                    $date = substr($value, 0, 6);
                    $value = str_replace($date, '', $value);
                    $data[] = trim($date);
                    $data[] = trim($value);
                    $appended = true;
                }
            }

            if (!$appended) {
                $data[] = $value;
            }
        }
        return $data;
    }


    public function parseStatement()
    {
        if ($this->is_processed != 1) {

            $statementFile = storage_path() . '/app/bank_statements/' . $this->statement_file;
            $statementDir = storage_path("app/bank_statements/" . $this->id . "/");
            File::isDirectory($statementDir) or File::makeDirectory($statementDir, 0777, true, true);

            $fileExtension = explode('.', $statementFile);
            $fileExtension = last($fileExtension);
            $fileExtension = Helper::slugifyText($fileExtension);

            if ($fileExtension == 'csv') {

                if ($this->is_processed != -1) {

                    $arrResult = array();
                    $handle = fopen($statementFile, "r");
                    if (empty($handle) === false) {
                        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                            $arrResult[] = $data;
                        }
                        fclose($handle);
                    }

                    $this->parseCSVTransactions($arrResult);
                }

            } else {
                $content = trim($this->content);
                if ($content == null) {

                    try {
                        $finalPdf = $statementDir . "final.pdf";
                        $crop = Helper::cropPDF($statementFile, $finalPdf, 0, 250, 0, 0);
                        //var_dump($crop);die;
                        if ($crop) {
                            $content = Pdf::getText($finalPdf);
                            $content = trim($content);
                            $this->content = $content;
                            $this->update();
                        }
                    } catch (\Exception $ex) {
                        $this->is_processed = -1;
                        $this->update();
                    }

                }

                if ($this->is_processed != -1) {
                    $this->parsePDFTransactions();
                }
            }

        }
    }

    private function parseCSVTransactions($results)
    {
        $modelTransactions = [];
        if (is_array($results) && count($results) > 1) {

            $unCategorizedCat = Category::where('slug', 'uncategorized')->first();

            foreach ($results as $k => $result) {
                if ($k > 0) {
                    if (is_array($result) && count($result) >= 8) {
                        // $accountType = trim($result[0]);
                        $accountNo = trim($result[1]);
                        $transactionDate = trim($result[2]);
                        $chequeNumber = trim($result[3]);
                        $descriptionOne = trim($result[4]);
                        $descriptionTwo = trim($result[5]);
                        $cad = trim($result[6]);
                        // $usd = trim($result[7]);

                        $transactionDate = strtotime($transactionDate);

                        $transactionNo = $accountNo;
                        if ($chequeNumber != null) {
                            $transactionNo .= ' - ' . $chequeNumber;
                        }

                        $amount = $cad;

                        if ($amount != '') {

                            $debitType = StatementConstants::DEBIT;
                            $firstChar = substr($amount, 0, 1);
                            if ($firstChar == '-') {
                                $debitType = StatementConstants::CREDIT;
                            }


                            $model = new Transaction();

                            $model->bank_account_id = $this->bank_account_id;
                            $model->bank_account_statement_id = $this->id;

                            $model->transaction_date = date('Y-m-d H:i:s', $transactionDate);
                            $model->transaction_time = date('H:i:s', $transactionDate);

                            $model->particular = $descriptionOne;
                            $model->description_2 = $descriptionTwo;

                            $model->transaction_no = $transactionNo;
                            $model->order_no = $transactionNo;

                            $model->is_debit = $debitType;
                            $model->total = $amount;

                            $model->user_id = $this->user_id;

                            $model->type = StatementConstants::TRANSACTION_TYPE_BANK_STATEMENTS;
                            $model->status = StatementConstants::TRANSACTION_PENDING;

                            $model->cheque_no = $chequeNumber;

                            if ($unCategorizedCat != null) {
                                $model->category_id = $unCategorizedCat->id;
                            }

                            $modelTransactions[] = $model->attributesToArray();

                        }
                    }
                }
            }
        }

        if (is_array($modelTransactions) && count($modelTransactions) > 0) {

            $chunks = array_chunk($modelTransactions, 500);
            foreach ($chunks as $chunk) {
                if (is_array($chunk) && count($chunk) > 0) {
                    Transaction::insert($chunk);
                }
            }

            $this->is_processed = 1;
            $this->update();
        } else {
            $this->is_processed = -1;
            $this->update();
        }
    }

    private function parsePDFTransactions()
    {
        try {
            $allTransactions = [];

            $content = trim($this->content);

            $previousStatementBal = Str::between($content, 'PREVIOUS STATEMENT BALANCE', 'TRANSACTION POSTING');
            $previousStatementBal = trim($previousStatementBal);
            $previousStatementBal = preg_replace("/\r|\n|\t/", " ", $previousStatementBal);
            $previousStatementBal = trim($previousStatementBal);
            if ($previousStatementBal != null) {
                $previousStatementBaLoop = explode(' ', $previousStatementBal);
                $previousStatementBal = 0;
                foreach ($previousStatementBaLoop as $str) {
                    if (strpos($str, '$') !== false) {
                        $previousStatementBal = $str;
                        break;
                    }
                }
                $previousStatementBal = str_replace('$', '', $previousStatementBal);
                $previousStatementBal = str_replace(',', '', $previousStatementBal);
                $this->previous_statement_balance = $previousStatementBal;
                $this->save();
            }

            $statementDateData = Str::between($content, 'STATEMENT FROM', 'OF');
            $statementDateData = trim($statementDateData);
            $statementDateData = explode(PHP_EOL, $statementDateData);
            $statementDateData = $statementDateData[0];
            $statementDateData = explode('TO', $statementDateData);

            $statementDateDataOne = trim($statementDateData[0]);
            $statementDateDataTwo = strtotime(trim($statementDateData[1]));
            $statementDateDataTwoYear = date('Y', $statementDateDataTwo);

            if (strlen($statementDateDataOne) <= 6) {
                $statementDateDataOne = $statementDateDataOne . ", " . $statementDateDataTwoYear;
                //echo $statementDateDataOne;die;
            }

            $statementDateDataOne = strtotime($statementDateDataOne);

            $this->statement_from = date('Y-m-d', $statementDateDataOne) . " 00:00:00";
            $this->statement_to = date('Y-m-d', $statementDateDataTwo) . " 00:00:00";
            $this->save();

            // echo date('d M Y', $statementDateDataOne)." - ".date('d M Y', $statementDateDataTwo);die;

            $year = date('Y', $statementDateDataOne);

            if ($content != null) {
                $data = preg_split('/TRANSACTION POSTING/iUsm', $content);
                if ($data != null) {
                    // print_r($data);die;
                    foreach ($data as $k => $pageContent) {

                        if (strpos($pageContent, 'DATE') !== false) {
                            $pageContent = str_replace('AMOUNT ($)', '', $pageContent);
                            $pageContent = str_replace('ACTIVITY DESCRIPTION', '', $pageContent);
                            $pageContent = str_replace('DATE', '', $pageContent);
                            $pageContent = explode(PHP_EOL, $pageContent);
                            $pageContent = Helper::cleanArray($pageContent);
                            $pageContent = self::verifyDateThing($pageContent);

                            /*if ($k == 6) {
                                print_r($pageContent);
                                die;
                            }*/

                            if (is_array($pageContent) && count($pageContent) > 0) {

                                $lastColumn = null;

                                $transactions = [];
                                $lastIndex = 0;
                                $index = 0;

                                foreach ($pageContent as $data) {

                                    $dataToValidate = trim(strtolower($data));
                                    if ($dataToValidate == 'new balance') {
                                        break;
                                    }

                                    $processed = false;
                                    $data = trim($data);
                                    $rawData = $data;
                                    $firstChar = substr($data, 0, 1);
                                    if (!array_key_exists($index, $transactions)) {
                                        $transactions[$index] = [
                                            'foreign_currency' => '',
                                            'exchange_rate' => '',
                                        ];
                                    }

                                    if (strlen($data) == 6 && strpos(strtolower($data), 'of') === false && $firstChar != '$' && $firstChar != '-') {
                                        $nextDate = false;

                                        $defaultData = $data;
                                        $data = Helper::convertToDate(trim($data), $year);
                                        $dayDiffer = ($statementDateDataOne - $data);
                                        $allowedDiff = 15 * (24 * 60 * 60);
                                        if ($dayDiffer >= $allowedDiff) {
                                            $data = Helper::convertToDate(trim($defaultData), ($year + 1));
                                        }

                                        if (date('Y', $data) < 2010) {
                                            if (array_key_exists($lastIndex, $transactions)) {
                                                if (array_key_exists('transaction_date', $transactions[$lastIndex])) {
                                                    $data = $transactions[$lastIndex]['transaction_date'];
                                                }
                                            }
                                        }

                                        if (array_key_exists('transaction_date', $transactions[$index])) {
                                            if (array_key_exists('posting_date', $transactions[$index])) {
                                                $nextDate = true;
                                            } else {
                                                $processed = true;
                                                $transactions[$index]['posting_date'] = $data;
                                                $transactions[$index]['posting_date_formatted'] = date('d M Y', $data);
                                                $lastColumn = 'posting_date';
                                            }
                                        } else {
                                            $processed = true;
                                            $transactions[$index]['transaction_date_p'] = true;
                                            $transactions[$index]['transaction_date'] = $data;
                                            $transactions[$index]['transaction_date_formatted'] = date('d M Y', $data);
                                            $lastColumn = 'transaction_date';
                                        }

                                        if ($nextDate) {
                                            $processed = true;
                                            $lastIndex = $index;
                                            $index++;
                                            $lastColumn = 'transaction_date';
                                            $transactions[$index] = [
                                                'raw' => $rawData,
                                                'foreign_currency' => '',
                                                'exchange_rate' => '',
                                                'transaction_date' => $data,
                                                'transaction_date_formatted' => date('d M Y', $data),
                                                'next_date' => true,
                                            ];
                                        }
                                    } else {
                                        if ($firstChar == '$' || $firstChar == '-') {
                                            //amount
                                            $debitType = StatementConstants::CREDIT;
                                            if ($firstChar == '-') {
                                                $debitType = StatementConstants::DEBIT;
                                                $data = str_replace('-', '', $data);
                                            }
                                            $firstChar = substr($data, 0, 1);
                                            if ($firstChar == '$') {
                                                $data = str_replace('-', '', $data);
                                                $data = str_replace('$', '', $data);
                                                $data = str_replace(',', '', $data);
                                                //check if exist in last index
                                                if (array_key_exists('amount', $transactions[$lastIndex])) {
                                                    if (!array_key_exists('amount', $transactions[$index])) {
                                                        $processed = true;
                                                        $transactions[$index]['is_debit'] = $debitType;
                                                        $transactions[$index]['amount'] = $data;
                                                        $lastColumn = 'amount';
                                                    }
                                                } else {
                                                    if (!array_key_exists('amount', $transactions[$lastIndex])) {
                                                        $processed = true;
                                                        $transactions[$lastIndex]['is_debit'] = $debitType;
                                                        $transactions[$lastIndex]['amount'] = $data;
                                                        $lastColumn = 'amount';
                                                    }
                                                }
                                            }
                                        } else {
                                            if (strpos($data, ' ') === false) {
                                                //no space means narration
                                                //check if exist in last index
                                                if (array_key_exists('narration', $transactions[$lastIndex])) {
                                                    if (!array_key_exists('narration', $transactions[$index])) {
                                                        $processed = true;
                                                        $transactions[$index]['narration'] = $data;
                                                        $lastColumn = 'narration';
                                                        if (
                                                            !array_key_exists('posting_date', $transactions[$index]) &&
                                                            array_key_exists('transaction_date', $transactions[$index])
                                                        ) {
                                                            $transactions[$index]['posting_date'] = $transactions[$index]['transaction_date'];
                                                            $transactions[$index]['posting_date_formatted'] = $transactions[$index]['transaction_date_formatted'];
                                                        }
                                                    }
                                                } else {
                                                    if (!array_key_exists('narration', $transactions[$lastIndex])) {
                                                        $processed = true;
                                                        $transactions[$lastIndex]['narration'] = $data;
                                                        $lastColumn = 'narration';
                                                    }
                                                }

                                            } else {
                                                $dataToCheck = strtolower($data);
                                                // || strpos($dataToCheck, 'exchange rate') !== false
                                                if (strpos($dataToCheck, 'foreign currency') !== false) {
                                                    $transaction = $transactions[$index];
                                                    if (array_key_exists('foreign_currency', $transaction) && trim($transaction['foreign_currency'] == null)) {
                                                        $processed = true;
                                                        $transactions[$index]['foreign_currency'] = $data;
                                                        $lastColumn = 'foreign_currency';
                                                    }
                                                } elseif (strpos($dataToCheck, 'exchange rate') !== false) {
                                                    if (array_key_exists($lastIndex, $transactions)) {
                                                        $transaction = $transactions[$lastIndex];
                                                        if (is_array($transaction)) {
                                                            if (array_key_exists('exchange_rate', $transaction) && trim($transaction['exchange_rate'] == null)) {
                                                                $processed = true;
                                                                $transactions[$lastIndex]['exchange_rate'] = $data;
                                                                $lastColumn = 'exchange_rate';
                                                            } else {
                                                                $transaction = $transactions[$index];
                                                                if (array_key_exists('exchange_rate', $transaction) && trim($transaction['exchange_rate'] == null)) {
                                                                    $processed = true;
                                                                    $transactions[$index]['exchange_rate'] = $data;
                                                                    $lastColumn = 'exchange_rate';
                                                                }
                                                            }
                                                        }
                                                    } else {
                                                        $transaction = $transactions[$index];
                                                        if (array_key_exists('exchange_rate', $transaction) && trim($transaction['exchange_rate'] == null)) {
                                                            $processed = true;
                                                            $transactions[$index]['exchange_rate'] = $data;
                                                            $lastColumn = 'exchange_rate';
                                                        }
                                                    }
                                                } else {
                                                    if (!array_key_exists('particular', $transactions[$lastIndex])) {
                                                        $processed = true;
                                                        $transactions[$lastIndex]['particular'] = $data;
                                                        $lastColumn = 'particular';
                                                    } elseif (!array_key_exists('particular', $transactions[$index])) {
                                                        $processed = true;
                                                        $transactions[$index]['particular'] = $data;
                                                        $lastColumn = 'particular';
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    if (!$processed) {
                                        // echo $index."  ->  ".$data."   ---> ".$lastIndex.'</br>';
                                    }

                                }

                                foreach ($transactions as $transactionSingle) {
                                    $allTransactions[] = $transactionSingle;
                                }

                            }


                        }
                    }
                }
            }

            /*print_r($allTransactions);
            die;*/

            $modelTransactions = [];

            $unCategorizedCat = Category::where('slug', 'uncategorized')->first();

            if (is_array($allTransactions) && count($allTransactions) > 0) {
                foreach ($allTransactions as $transaction) {
                    if (array_key_exists('narration', $transaction) && array_key_exists('particular', $transaction)
                        && array_key_exists('amount', $transaction)
                        && array_key_exists('particular', $transaction) && trim($transaction['narration'] != null)) {
                        $model = new Transaction();

                        $extraInfo = [];

                        if (array_key_exists('foreign_currency', $transaction) && trim($transaction['foreign_currency'] != null)) {
                            $extraInfo['foreign_currency'] = $transaction['foreign_currency'];
                        }

                        if (array_key_exists('exchange_rate', $transaction) && trim($transaction['exchange_rate'] != null)) {
                            $extraInfo['exchange_rate'] = $transaction['exchange_rate'];
                        }

                        if ($extraInfo != null) {
                            $model->extra_info = json_encode($extraInfo);
                        } else {
                            $model->extra_info = null;
                        }

                        $model->bank_account_id = $this->bank_account_id;
                        $model->bank_account_statement_id = $this->id;
                        $model->transaction_date = date('Y-m-d', $transaction['transaction_date']) . " 00:00:00";
                        $model->transaction_time = date('H:i:s', $transaction['transaction_date']);
                        $model->posting_date = date('Y-m-d', $transaction['posting_date']) . " 00:00:00";
                        $model->particular = $transaction['particular'];
                        $model->transaction_no = $transaction['narration'];
                        $model->order_no = $transaction['narration'];

                        $model->user_id = $this->user_id;

                        if ($transaction['is_debit'] == StatementConstants::CREDIT) {
                            $model->is_debit = StatementConstants::DEBIT;
                        } else {
                            $model->is_debit = StatementConstants::CREDIT;
                        }

                        $model->total = $transaction['amount'];
                        $model->type = StatementConstants::TRANSACTION_TYPE_BANK_STATEMENTS;
                        $model->status = StatementConstants::TRANSACTION_PENDING;

                        if ($unCategorizedCat != null) {
                            $model->category_id = $unCategorizedCat->id;
                        }

                        $modelTransactions[] = $model->attributesToArray();
                    }
                }
            }

            // print_r($modelTransactions);die;

            if (is_array($modelTransactions) && count($modelTransactions) > 0) {
                Transaction::insert($modelTransactions);
            }

            $this->is_processed = 1;
            $this->update();

        } catch (\Exception $ex) {
            /* echo $ex->getMessage()."</br>";
             echo $ex->getLine();
             die;*/
            $this->is_processed = -1;
            $this->update();
        }
    }

    public function bank()
    {
        return $this->hasOne(Bank::class, 'id', 'bank_id');
    }

    public function bankAccount()
    {
        return $this->hasOne(BankAccount::class, 'id', 'bank_account_id');
    }

}

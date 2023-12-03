<?php

namespace App\Http\Controllers;


use App\Constants\StatementConstants;
use App\Helpers\Helper;
use App\Models\BankAccount;
use App\Models\BankAccountStatement;
use App\Models\EnvelopeTransaction;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AllTransactionsExport;
use App\Exports\AllTransactionsExportByYear;
use App\Models\TransactionByCategory;

class BankStatementController extends Controller
{

    public function list()
    {
        $title = "Bank Statements";
        return view('bank-statements.list', compact('title'));
    }

    public function addNewStatement(Request $request)
    {
        $rules = [
            'bank_account_id' => 'required',
            'name' => 'required',
            'statement' => 'required:mimes:pdf,csv',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->with('popup', 'newRecordModal')->withInput();
        }

        $bankAccount = BankAccount::where('id', $request->get('bank_account_id'))->first();
        if ($bankAccount != null) {

            $statementFile = $request->statement;
            /**
             * @var UploadedFile $statementFile
             */
            $originalName = $statementFile->getClientOriginalName();
            $originalName = Helper::slugifyText($originalName);

            $fileName = time() . '_' . rand(11111, 99999) . '.' . $originalName;
            $request->statement->move(storage_path() . '/app/bank_statements/', $fileName);

            $file = new BankAccountStatement();
            $file->user_id = auth()->user()->id;
            $file->bank_account_id = $bankAccount->id;
            $file->bank_id = $bankAccount->bank_id;
            $file->name = $request->get('name');
            $file->statement_file = $fileName;
            $file->status = StatementConstants::PENDING;
            $file->save();

            return back()->with('success', 'Statement has been uploaded.');
        } else {
            return back()->with('error', 'Bank account is invalid.')->with('popup', 'newRecordModal')->withInput();
        }
    }

    public function editStatement(Request $request)
    {
        if (isset($_GET['id']) && trim($_GET['id']) != null) {
            $bankStatement = BankAccountStatement::where('id', trim($_GET['id']))->firstOrFail();

            $rules = [
                'bank_account_id' => 'required',
                'name' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->with('popup', 'oldRecordModal')->withInput();
            }

            $bankStatement->bank_account_id = $request->get('bank_account_id');
            $bankStatement->name = $request->get('name');
            $bankStatement->save();

            return back()->with('success', 'Statement has been updated.');
        } else {
            return back()->with('error', 'Invalid Request');
        }
    }

    public function deleteStatement($id)
    {
        $bankStatement = BankAccountStatement::where('id', $id)->firstOrFail();
        $bankStatement->delete();
        return back()->with('success', 'Bank statement has been deleted.');
    }

    public function deleteStatements($id)
    {
        if ($id != null) {
            BankAccountStatement::whereIn('id', explode(',', $id))->delete();
        }
        return back()->with('success', 'Bank statement has been deleted.');
    }

    public function allTransactions()
    {
        $title = "All Transactions";
        $id = 'all';

        $totalTransactions = $totalCredits = $totalDebits = $paymentsAndCredits = $purchaseAndDebits = 0;
        $cashAdvances = $interest = $fee = 0;
        $totalCreditsVal = $totalDebitsVal = 0;

        $particular = isset($_GET['particular']) ? trim($_GET['particular']) : '';
        $particular = html_entity_decode($particular, ENT_QUOTES);

        $allTransactions = Transaction::whereNotNull('bank_account_statement_id');

        if ($particular != '') {
            $allTransactions->where('particular', 'like', '%' . $particular . '%');
        }

        $allTransactions = $allTransactions->get();

        if ($allTransactions != null) {
            foreach ($allTransactions as $transaction) {
                if ($transaction->is_debit == StatementConstants::DEBIT) {
                    $totalDebits = $totalDebits + 1;
                    $totalDebitsVal = $totalDebitsVal + $transaction->total;
                } else {
                    $totalCredits = $totalCredits + 1;
                    $totalCreditsVal = $totalCreditsVal + $transaction->total;
                }

                if (strpos(strtolower($transaction->particular), 'cash advance') !== false) {
                    $cashAdvances = $cashAdvances + $transaction->total;
                } elseif (strpos(strtolower($transaction->particular), 'cash adv') !== false) {
                    $cashAdvances = $cashAdvances + $transaction->total;
                } elseif (strpos(strtolower($transaction->particular), 'interest') !== false) {
                    $interest = $interest + $transaction->total;
                } elseif (strpos(strtolower($transaction->particular), ' fee') !== false && strpos(strtolower($transaction->particular), 'coffee') === false) {
                    $fee = $fee + $transaction->total;
                } elseif (strpos(strtolower($transaction->particular), 'fee ') !== false && strpos(strtolower($transaction->particular), 'coffee') === false) {
                    $fee = $fee + $transaction->total;
                } elseif (strpos(strtolower($transaction->particular), 'service charge') !== false) {
                    $fee = $fee + $transaction->total;
                }
            }
        }

        $cashIntFee = $cashAdvances + $interest + $fee;
        $totalDebitsVal = $totalDebitsVal - $cashIntFee;

        $statementDate = '';

        $firstTransaction = Transaction::whereNotNull('bank_account_statement_id');
        $lastTransaction = Transaction::whereNotNull('bank_account_statement_id');

        if ($particular != '') {
            $firstTransaction->where('particular', 'like', '%' . $particular . '%');
            $lastTransaction->where('particular', 'like', '%' . $particular . '%');
        }

        $firstTransaction = $firstTransaction->orderBy('transaction_date', 'ASC')->first();
        $lastTransaction = $lastTransaction->orderBy('transaction_date', 'DESC')->first();

        if ($firstTransaction != null && $lastTransaction != null) {
            $statementDate = 'FROM ' . date('M d, Y', strtotime($firstTransaction->transaction_date)) . " to " . date('M d, Y', strtotime($lastTransaction->transaction_date));
            $statementDate = strtoupper($statementDate);
        }

        $paymentsAndCredits = $totalCreditsVal;
        $purchaseAndDebits = $totalDebitsVal;

        $stats = [
            'totalTransactions' => $totalTransactions,
            'totalCredits' => $totalCredits,
            'totalDebits' => $totalDebits,
            'paymentsAndCredits' => $paymentsAndCredits,
            'purchaseAndDebits' => $purchaseAndDebits,
            'cashAdvances' => $cashAdvances,
            'interest' => $interest,
            'fee' => $fee,
        ];

        return view('bank-statements.show-transactions', compact('title', 'id', 'statementDate', 'stats'));
    }

    public function AllTransactionsExport()
    {
        $from = isset($_GET['from']) ? trim($_GET['from']) : '';
        $to = isset($_GET['to']) ? trim($_GET['to']) : '';
        $year = isset($_GET['year']) ? trim($_GET['year']) : '';
        if ($year != '') {

            $from = '01/01/' . $year;
            $to = '12/31/' . $year;
        }

        $import = new AllTransactionsExport();
        $import->setFrom($from);
        $import->setTo($to);
        return Excel::download($import, 'all-transactions-by-category.xlsx');

    }

  public function AllTransactionsExportByYear()
    {
        $from = isset($_GET['from']) ? trim($_GET['from']) : '';
        $to = isset($_GET['to']) ? trim($_GET['to']) : '';
        $year = isset($_GET['year']) ? trim($_GET['year']) : '';
        if ($year != '') {
            $from = '01/01/' . $year;
            $to = '12/31/' . $year;
        }
        $fromDate=explode('/', $from);
        $first= TransactionByCategory::orderBy('id', 'ASC')->first();
        if(isset($fromDate[2]))
        {
            $from=$fromDate[2].'-'.$fromDate[0].'-'.$fromDate[1];
        }
        else {
            $from=$first->transaction_date;
        }

        $fromTo=explode('/', $to);
        if(isset($fromTo[2]))
        {
            $to=$fromTo[2].'-'.$fromTo[0].'-'.$fromTo[1];
        } else {
            $to=date('Y-m-d');
        }
      
        $import = new AllTransactionsExportByYear();
        $import->setFrom($from);
        $import->setTo($to);
        return Excel::download($import, 'BulkSheetsExport.xlsx');

    }
	
    public function listTransactions($id)
    {
        $title = "Transactions";
        return view('bank-statements.show-transactions', compact('title', 'id'));
    }

    public function showTransactions($id)
    {
        $bankStatement = BankAccountStatement::where('id', $id)->firstOrFail();
        $bankStatement->parseStatement();
        $title = $bankStatement->name . " Transactions";

        $totalTransactions = $totalCredits = $totalDebits = $paymentsAndCredits = $purchaseAndDebits = 0;
        $cashAdvances = $interest = $fee = 0;
        $totalCreditsVal = $totalDebitsVal = 0;

        $previousBalance = $bankStatement->previous_statement_balance;

        $particular = isset($_GET['particular']) ? trim($_GET['particular']) : '';
        $particular = html_entity_decode($particular, ENT_QUOTES);

        $allTransactions = Transaction::where('bank_account_statement_id', $id);

        if ($particular != '') {
            $allTransactions->where('particular', 'like', '%' . $particular . '%');
        }

        $allTransactions = $allTransactions->get();

        if ($allTransactions != null) {
            foreach ($allTransactions as $transaction) {
                if ($transaction->is_debit == StatementConstants::DEBIT) {
                    $totalDebits = $totalDebits + 1;
                    $totalDebitsVal = $totalDebitsVal + $transaction->total;
                } else {
                    $totalCredits = $totalCredits + 1;
                    $totalCreditsVal = $totalCreditsVal + $transaction->total;
                }

                if (strpos(strtolower($transaction->particular), 'cash advance') !== false) {
                    $cashAdvances = $cashAdvances + $transaction->total;
                } elseif (strpos(strtolower($transaction->particular), 'cash adv') !== false) {
                    $cashAdvances = $cashAdvances + $transaction->total;
                } elseif (strpos(strtolower($transaction->particular), 'interest') !== false) {
                    $interest = $interest + $transaction->total;
                } elseif (strpos(strtolower($transaction->particular), ' fee') !== false && strpos(strtolower($transaction->particular), 'coffee') === false) {
                    $fee = $fee + $transaction->total;
                } elseif (strpos(strtolower($transaction->particular), 'fee ') !== false && strpos(strtolower($transaction->particular), 'coffee') === false) {
                    $fee = $fee + $transaction->total;
                } elseif (strpos(strtolower($transaction->particular), 'service charge') !== false) {
                    $fee = $fee + $transaction->total;
                }
            }
        }

        $cashIntFee = $cashAdvances + $interest + $fee;
        $totalDebitsVal = $totalDebitsVal - $cashIntFee;

        $paymentsAndCredits = $totalCreditsVal;
        $purchaseAndDebits = $totalDebitsVal;

        $newBalance = ($previousBalance - $paymentsAndCredits) + $purchaseAndDebits + $cashIntFee;

        $statementDate = '';
        if ($bankStatement->statement_from != null && $bankStatement->statement_to != null) {
            $statementDate = 'FROM ' . date('M d, Y', strtotime($bankStatement->statement_from)) . " to " . date('M d, Y', strtotime($bankStatement->statement_to));
            $statementDate = strtoupper($statementDate);
        }

        if ($particular != '') {
            $statementDate = '';
        }

        if ($statementDate == '') {
            $firstTransaction = Transaction::where('bank_account_statement_id', $bankStatement->id);
            $lastTransaction = Transaction::where('bank_account_statement_id', $bankStatement->id);

            if ($particular != '') {
                $firstTransaction->where('particular', 'like', '%' . $particular . '%');
                $lastTransaction->where('particular', 'like', '%' . $particular . '%');
            }

            $firstTransaction = $firstTransaction->orderBy('transaction_date', 'ASC')->first();
            $lastTransaction = $lastTransaction->orderBy('transaction_date', 'DESC')->first();

            if ($firstTransaction != null && $lastTransaction != null) {
                $statementDate = 'FROM ' . date('M d, Y', strtotime($firstTransaction->transaction_date)) . " to " . date('M d, Y', strtotime($lastTransaction->transaction_date));
                $statementDate = strtoupper($statementDate);
            }
        }

        $stats = [
            'totalTransactions' => $totalTransactions,
            'totalCredits' => $totalCredits,
            'totalDebits' => $totalDebits,
            'paymentsAndCredits' => $paymentsAndCredits,
            'purchaseAndDebits' => $purchaseAndDebits,
            'cashAdvances' => $cashAdvances,
            'interest' => $interest,
            'fee' => $fee,
            'previousBalance' => $previousBalance,
            'newBalance' => $newBalance,
        ];

        return view('bank-statements.show-transactions', compact('bankStatement', 'title', 'stats', 'statementDate', 'id'));
    }

    public function updateInvoiceCategory(Request $request)
    {
        $invoice_id = $request->get('invoice_id');
        $category_id = $request->get('category_id');
        if ($invoice_id) {
            $bankTransaction = Transaction::where('id', $invoice_id)->first();
            if ($category_id != null) {
                $bankTransaction->category_id = $category_id;
            }
            $bankTransaction->save();
            return back()->with('success', ' Transaction category updated successfully');
        } else {
            return back()->with('error', 'No category selected');
        }
    }

    public function bulkUpdateStatements(Request $request)
    {
        $status = $request->get('status');
        $keys = $request->get('keys');
        $keys = json_decode($keys, true);
        if (is_array($keys) && count($keys) > 0) {
            $rowsUpdated = 0;
            foreach ($keys as $id) {
                $id = trim($id);
                $bankTransaction = BankAccountStatement::where('id', $id)->first();
                if ($bankTransaction != null) {
                    $rowsUpdated++;
                    if ($status != null) {
                        $bankTransaction->status = $status;
                    }
                    $bankTransaction->save();
                }
            }
            return back()->with('success', $rowsUpdated . ' Transactions updated successfully');
        } else {
            return back()->with('error', 'No transactions selected');
        }
    }


    public function bulkUpdateStatement(Request $request)
    {
        $transaction_date = $request->get('transaction_date');
        $category_id = $request->get('category_id');

        $bank_account_id = $request->get('bank_account_id');
        $bank_account_statement_id = $request->get('bank_account_statement_id');

        $status = $request->get('status');
        $vendor_id = $request->get('vendor_id');
        $envelope_id = $request->get('envelope_id');
        $budget_id = $request->get('budget_id');
        $keys = $request->get('keys');
        $keys = json_decode($keys, true);
        if (is_array($keys) && count($keys) > 0) {
            $rowsUpdated = 0;
            foreach ($keys as $id) {
                $id = trim($id);
                $bankTransaction = Transaction::where('id', $id)->first();
                if ($bankTransaction != null) {
                    $rowsUpdated++;
                    if ($transaction_date != null) {
                        $bankTransaction->transaction_date = date('Y-m-d', strtotime($transaction_date)) . " 00:00:00";
                    }
                    if ($category_id != null) {
                        $bankTransaction->category_id = $category_id;
                    }
                    if ($status != null) {
                        $bankTransaction->status = $status;
                    }
                    if ($vendor_id != null) {
                        $bankTransaction->vendor_id = $vendor_id;
                    }

                    if ($bank_account_id != null && $bank_account_statement_id != null) {
                        $bankTransaction->bank_account_id = $bank_account_id;
                        $bankTransaction->bank_account_statement_id = $bank_account_statement_id;
                    }

                    if (is_array($envelope_id) && count($envelope_id) > 0) {
                        foreach ($envelope_id as $envelopeId) {
                            $findEvTran = EnvelopeTransaction::where('envelope_id', $envelopeId)->where('transaction_id', $bankTransaction->id)->first();
                            if (empty($findEvTran)) {
                                EnvelopeTransaction::create([
                                    'envelope_id' => $envelopeId,
                                    'transaction_id' => $bankTransaction->id,
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s')
                                ]);
                            }
                        }
                    }
                    if ($budget_id != null) {
                        $bankTransaction->budget_id = $budget_id;
                    }
                    $bankTransaction->save();
                }
            }

            $lastUrl = url()->previous();
            $lastUrl = explode('?', $lastUrl);
            return redirect()->to($lastUrl[0])->with('success', $rowsUpdated . ' Transactions updated successfully');
        } else {
            return back()->with('error', 'No transactions selected');
        }
    }

    public function statementsDataTable(Request $request)
    {
        $statements = BankAccountStatement::where('id', '>', 0);

        if ($request->bank_id != '') {
            $statements->where('bank_id', "{$request->bank_id}");
        }

        if ($request->bank_account_id != '') {
            $statements->where('bank_account_id', "{$request->bank_account_id}");
        }

        $statements = $statements->get();

        $dataTable = DataTables::of($statements)
            ->addColumn('checkboxes', function ($statement) {
                $action = '<input type="checkbox" name="pdr_checkbox[]" class="pdr_checkbox" value="' . $statement->id . '" />';
                return $action;
            })
            ->addColumn('bank', function ($statement) {
                return $statement->bank->name;
            })
            ->addColumn('bankAccount', function ($statement) {
                return $statement->bankAccount->displayName();
            })
            ->addColumn('created_on', function ($statement) {
                return $statement->created_at;
            })
            ->addColumn('updated_on', function ($statement) {
                return $statement->updated_at;
            })
            ->addColumn('actions', function ($statement) {
                $action = Helper::getActionButtons([
                    'edit' => ['url' => 'javascript:;', 'class' => 'editRecord', 'dataAttributes' => [
                        'href' => route('bankStatements.editStatement', ['id' => $statement->id]),
                        'bank_account_id' => $statement->bank_account_id,
                        'name' => $statement->name,
                    ]],
                    'view' => ['url' => route('bankStatements.showTransactions', $statement->id)],
                    'delete' => ['url' => route('bankStatements.deleteStatement', $statement->id)],
                ]);
                return $action;
            })
            ->rawColumns(['checkboxes', 'actions'])
            ->make(true);

        $dataTable = $dataTable->getData(true);
        return response()->json($dataTable);
    }

    public function transactionDataTable(Request $request, $id)
    {
        // add sorting prameters
        $columnIndex =$request['order'][0]['column'];
        $columnName = $request['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $request['order'][0]['dir']; // asc or desc
        ini_set('memory_limit', '-1');
        $transactions = Transaction::where('bank_account_statement_id', '=', $id);

        if ($id == 'all') {
            $transactions = Transaction::whereNotNull('bank_account_statement_id');
        }

        if (strpos($id, ',') !== false) {
            $transactions = Transaction::whereIn('bank_account_statement_id', explode(',', $id));
        }

        if (request()->from) {
            $transactions->whereDate('transaction_date', '>=', Carbon::createFromDate(request()->from));
        }

        if (request()->to) {
            $transactions->whereDate('transaction_date', '<=', Carbon::createFromDate(request()->to));
        }

        if ($request->year_options != null) {
            $yearOption = trim($request->year_options);
            if (is_numeric($yearOption)) {
                $yearStart = $yearOption . '-01-01 00:00:00';
                $yearEnd = $yearOption . '-12-31 23:59:59';
                $transactions->whereDate('transaction_date', '>=', $yearStart);
                $transactions->whereDate('transaction_date', '<=', $yearEnd);
            }
        }

        if ($request->vendor_id != '') {
            $transactions->where('vendor_id', "{$request->vendor_id}");
        }

        // if ($request->category_id != '') {
        //     $categoryToQuery = null;
        //     if (is_array($request->category_id) && count($request->category_id) > 0) {
        //         $transactions->whereIn('category_id', $request->category_id);
        //     }
        // }

        if ($request->category_id != '') {
            $transactions->where('category_id', $request->category_id);
        }

        if ($request->is_debit != '') {
            $transactions->where('is_debit', "{$request->is_debit}");
        }

        if ($request->status != '') {
            $transactions->where('status', "{$request->status}");
        }

        if ($request->particular != '') {
            $transactions->where('particular', 'like', "%{$request->particular}%");
        }

        if ($request->order_no != '') {
            $transactions->where('order_no', 'like', "%{$request->order_no}%");
        }

        if ($request->id != '') {
            $transactions->where('id', 'like', "%{$request->id}%");
        }

        if ($request->bank_account_id != '') {
            $transactions->where('bank_account_id', 'like', "{$request->bank_account_id}");
        }

        if ($request->pt != '') {
            $particularToSearch = $request->pt;
            $particularToSearch = html_entity_decode($particularToSearch, ENT_QUOTES);
            $transactions->where('particular', 'like', '%' . $particularToSearch . '%');
            //dd($transactions->getBindings());
        }

        // Sorting parameter maping
        if($columnName!='' AND $columnName!='checkboxes'){
            if($columnName=='bank_account'){
                $columnName='bank_account_statement_id';
                $transactions->orderBy($columnName,  $columnSortOrder);
            }
            else if($columnName=='particularWithLink'){
                $columnName='particular';
                $transactions->orderBy($columnName,  $columnSortOrder);
            }
            else if($columnName=='category'){
                $columnName='category_id';
                $transactions->orderBy($columnName,  $columnSortOrder);
            }
            else if($columnName=='vendor'){
                $columnName='vendor_id';
                $transactions->orderBy($columnName,  $columnSortOrder);
            }
            else if($columnName=='total_formatted'){
                $columnName='total';
                $transactions->orderBy($columnName,  $columnSortOrder);
            }
            else {
                $transactions->orderBy($columnName,  $columnSortOrder);
            }
        }

        $currentPageUrl = $request->current_page;

        $transactionsQuery = clone $transactions;

        $dataTable = DataTables::of($transactions)
            ->addColumn('checkboxes', function ($transaction) {
                $action = '<input type="checkbox" name="pdr_checkbox[]" class="pdr_checkbox" value="' . $transaction->id . '" />';
                return $action;
            })
            ->addColumn('transaction_date', function ($transaction) {
                return date('Y-m-d', strtotime($transaction->transaction_date));
            })
            ->addColumn('transaction_date_mdy', function ($transaction) {
                return date('m-d-Y', strtotime($transaction->transaction_date));
            })
            ->addColumn('bank_account', function ($transaction) {
                $bankAccountName = '-';
                if ($transaction->bank_account_id != null) {
                    $bankAccount = BankAccount::where('id', $transaction->bank_account_id)->first();
                    if ($bankAccount != null) {
                        $bankAccountName = $bankAccount->displayName();
                    }
                }
                return $bankAccountName;
            })
            ->addColumn('particular', function ($transaction) {
                return $transaction->particular;
            })
            ->addColumn('particularWithLink', function ($transaction) use ($currentPageUrl) {
                return '<a href="' . $currentPageUrl . '?particular=' . urlencode($transaction->particular) . '">' . $transaction->particular . '</a>';
            })
            ->addColumn('order_no', function ($transaction) {
                return $transaction->order_no;
            })
            ->addColumn('status', function ($transaction) {
                return $transaction->status;
            })
            ->addColumn('category', function ($transaction) {
                if ($transaction->category != null) {
                    $category_name = $transaction->category->name;
                    if ($category_name == 'Incoming Uncategorized') {
                        $category_name = 'Uncategorized';
                    }
                } else {
                    $category_name = '-';
                }

                return $category_name;
            })
            ->addColumn('vendor', function ($transaction) {
                return $transaction->vendor != null ? $transaction->vendor->name : '-';
            })
            ->addColumn('type', function ($transaction) {
                return $transaction->is_debit;
            })
            ->addColumn('total_formatted', function ($transaction) {
                $val = Helper::printAmount($transaction->total);
                if ($transaction->is_debit == StatementConstants::CREDIT) {
                    $val = "-" . $val;
                }
                return $val;
            })
            ->rawColumns(['checkboxes', 'actions', 'particularWithLink'])
            ->make(true);
        /**
         * @var JsonResponse $dataTable
         */

        $dataTable = $dataTable->getData(true);

        $stats = [
            'this_page' => [
                'debits' => 0,
                'debits_total' => 0,
                'credits' => 0,
                'credits_total' => 0,
            ],
            'total' => [
                'debits' => 0,
                'debits_total' => 0,
                'credits' => 0,
                'credits_total' => 0,
            ]
        ];

        $transactions = $transactionsQuery->get();
        if ($transactions != null) {
            foreach ($transactions as $transaction) {
                if ($transaction->is_debit == StatementConstants::DEBIT) {
                    $stats['total']['debits'] = $stats['total']['debits'] + 1;
                    $stats['total']['debits_total'] = $stats['total']['debits_total'] + $transaction->total;
                } else {
                    $stats['total']['credits'] = $stats['total']['credits'] + 1;
                    $stats['total']['credits_total'] = $stats['total']['credits_total'] + $transaction->total;
                }
            }
        }

        $pageTransactions = $dataTable['data'];
        if (is_array($pageTransactions) && count($pageTransactions) > 0) {
            foreach ($pageTransactions as $transaction) {
                if ($transaction['is_debit'] == StatementConstants::DEBIT) {
                    $stats['this_page']['debits'] = $stats['this_page']['debits'] + 1;
                    $stats['this_page']['debits_total'] = $stats['this_page']['debits_total'] + $transaction['total'];
                } else {
                    $stats['this_page']['credits'] = $stats['this_page']['credits'] + 1;
                    $stats['this_page']['credits_total'] = $stats['this_page']['credits_total'] + $transaction['total'];
                }
            }
        }

        $stats['this_page']['debits_total'] = Helper::printAmount($stats['this_page']['debits_total']);
        $stats['this_page']['credits_total'] = Helper::printAmount($stats['this_page']['credits_total']);

        $stats['total']['debits_total'] = Helper::printAmount($stats['total']['debits_total']);
        $stats['total']['credits_total'] = Helper::printAmount($stats['total']['credits_total']);

        $dataTable['stats'] = $stats;

        return response()->json($dataTable);
    }
}

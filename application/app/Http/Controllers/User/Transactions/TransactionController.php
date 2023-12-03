<?php

namespace App\Http\Controllers\User\Transactions;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use PDF;

use App\Models\Transaction;
use App\Models\Vendor;
use App\Models\Envelope;
use App\Models\Budget;
use App\Models\EnvelopeTransaction;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        $transactions = Transaction::where('user_id', Auth::user()->id)->get();
        $vendors = Vendor::all();
        return view('user.transactions.transaction', compact('transactions', 'vendors'));
    }

    public function show(Transaction $transaction) {
        $extra_info = collect(json_decode($transaction->extra_info, true));
        $envelopes = Envelope::where('enveloped_by',Auth::user()->id)->get();
        $budgets = Budget::where('created_by',Auth::user()->id)->get();
        $token = [$transaction->id, now()];
        $link = serialize($token);
        $encrypted = Crypt::encryptString($link);

        return view('user.transactions.show', compact('transaction', 'extra_info', 'envelopes', 'budgets', 'encrypted'));
    }

    public function printAll(Request $request)
    {
        $count = 0;
        $grandTotal = 0;
        $ids = $request->transaction_ids;
        if (is_array($ids)) {
            $transactions = Transaction::whereIn('id', $ids)->get();
            $count = Transaction::whereIn('id', $ids)->count();
            foreach ($transactions as $transaction) {
                $p = $transaction->total;
                $grandTotal += $p;
            }
        }
        return view('admin.transactions.transactions_pdf_print', compact('transactions', 'grandTotal', 'count'));
    }
    public function savePDF(Request $request)
    {

        $count = 0;
        $grandTotal = 0;
        $ids = $request->transaction_ids;
        if (is_array($ids)) {
            $transactions = Transaction::whereIn('id', $ids)->get();
            $count = Transaction::whereIn('id', $ids)->count();
            foreach ($transactions as $transaction) {
                $p = $transaction->total;
                $grandTotal += $p;
            }
        }
        $f_name = str_replace(' ', '_', now()) . '.pdf';
        $pdf = PDF::loadView('admin.transactions.transactions_pdf', compact('transactions', 'grandTotal', 'count'));
        return $pdf->stream($f_name);
    }

    public function exportAll(Request $request)
    {
        $ids = $request->transaction_ids;
        if (is_array($ids)) {
            $transactions = Transaction::whereIn('id', $ids)->get();
        }
        $fileName = str_replace(' ', '_', now());

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Transaction No', 'Vendor', 'Transaction Date', 'Total', 'Bar Code');
        $callback = function () use ($transactions, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($transactions as $tans) {
                $row['transaction_no']  = $tans->transaction_no;
                $row['vendor_id']  = $tans->vendor->name;
                $row['transaction_date']    = $tans->transaction_date;
                $row['total']    = $tans->total;
                $row['bar_qr_code']  = $tans->bar_qr_code;
                fputcsv($file, array($row['transaction_no'], $row['vendor_id'], $row['transaction_date'], $row['total'], $row['bar_qr_code']));
            }
            fclose($file);
        };

        return response()->streamDownload($callback, null, $headers);
    }


    public function AddToEnvelope(Request $request, $transaction)
    {
        if ($request->envelope_id == 0) {
            return redirect()->back();
        } else {
            $envelope = Envelope::where('id', $request->envelope_id)->first();
            $envId = $envelope->id;
            $envName = $envelope->name;
            $envTrans = EnvelopeTransaction::where('envelope_id', $request->envelope_id)->where('transaction_id', $transaction)->first();
            if($envTrans)
            {
                $envTrans->update();
            }
            else
            {
                $envTrans = new EnvelopeTransaction();
                $envTrans->envelope_id = $envelope->id;
                $envTrans->transaction_id = $transaction;
                $envTrans->save();
            }
           
            Session::flash('success', 'You have successfully add transaction # ' . $transaction . ' to Envelope - ' . $envName);
            return redirect()->back();
        }
    }


    public function AddToBudgetTransacation(Request $request, $transaction)
    {

        if ($request->budget_id == 0) {
            return redirect()->back();
        } else {
            $budgets = Budget::where('id', $request->budget_id)->first();
            $BudgetId = $budgets->id;
            $budName = $budgets->name;
            $array = explode(',', $transaction, 1);

            Transaction::whereIn('id', $array)->update(['budget_id' => $BudgetId]);
            Session::flash('success', 'You have successfully add transaction # ' . $transaction . ' to Budget - ' . $budName);
            return redirect()->back();
        }
    }
}


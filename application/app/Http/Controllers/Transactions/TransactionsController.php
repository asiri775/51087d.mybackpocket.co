<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use App\Mail\InvoiceEmail;
use App\Models\Budget;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\EmailSubject;
use App\Models\EmailTemplate;
use App\Models\Vendor;
use App\Models\Envelope;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use PDF;
use MPDF;
use Session;

class TransactionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $transactions = Transaction::all();
        $vendors = Vendor::all();
        return view('admin.transactions.list', compact('transactions', 'vendors'));
    }

    public function archiveList()
    {
        $transactions = Transaction::all();
        return view('admin.transactions.archive_list', compact('transactions'));
    }

    public function show(Transaction $transaction)
    {

        $extra_info = collect(json_decode($transaction->extra_info, true));
        $envelopes = Envelope::all();
        $budgets = Budget::all();
        $token = [$transaction->id, now()];
        $link = serialize($token);
        $encrypted = Crypt::encryptString($link);
        
        return view('admin.transactions.show', compact('transaction', 'extra_info', 'envelopes', 'budgets', 'encrypted'));
    }

    public function pdf(Transaction $transaction)
    {
        $pdf = PDF::loadView('admin.transactions.invoice', compact('transaction'));
        return $pdf->download('invoice.pdf');
    }

    public function mpdf(Transaction $transaction)
    {
        $config = [
            'title' => $transaction->vendor->name . " Invoice"
        ];
        $extra_info = collect(json_decode($transaction->extra_info, true));
        $pdf = MPDF::loadView('admin.transactions.minvoice', compact('transaction', 'extra_info'), [], $config);
        return $pdf->download('BackpocketReceipt_' . strtolower($transaction->vendor->name) . '_' . $transaction->transaction_no.'.pdf');
    }

   public function notify(Request $request)
    {
        //find booking
        if ($request->trans_id) {
            $transaction=Transaction::where('id',$request->trans_id)->first();
            $product_listing = Transaction::emailProductLIst($request->trans_id);
            $token = [$request->trans_id, now()];
            $link = serialize($token);
            $encrypted = Crypt::encryptString($link);
            $link= url('/email/share-email/'.$encrypted);
            //send email to customer - refund true
            try {
                // Send Booking Cancelled email
                $EmailSubject = EmailSubject::where('token', 's4ad52j8')->first();
                $EmailTemplate = EmailTemplate::where('domain', 6)->where('subject_id', $EmailSubject['id'])->first();
                Mail::to($request->send_email)->queue(new InvoiceEmail($EmailSubject['subject'], $transaction, $product_listing, $transaction->vendor, $EmailTemplate,$link));
            } catch (\Exception $ex) {
                //do nothing
            }
            //set success message and redirect to bookings.show
            Session::flash('booking_updated', __('Vendor invoice email successfully sent.'));
            return redirect(url('/admin/transactions/'.$request->trans_id));
        }
    }

    public function notifyList(Request $request)
    {
        //find booking
        if ($request->trans_id) {
            $transaction=Transaction::where('id',$request->trans_id)->first();
            $product_listing = Transaction::emailProductLIst($request->trans_id);
            $token = [$request->trans_id, now()];
            $link = serialize($token);
            $encrypted = Crypt::encryptString($link);
            $link= url('/email/share-email/'.$encrypted);
            //send email to customer - refund true
            try {
                // Send Booking Cancelled email
                $EmailSubject = EmailSubject::where('token', 's4ad52j8')->first();
                $EmailTemplate = EmailTemplate::where('domain', 6)->where('subject_id', $EmailSubject['id'])->first();
                Mail::to($request->send_email)->queue(new InvoiceEmail($EmailSubject['subject'], $transaction, $product_listing, $transaction->vendor, $EmailTemplate,$link));
            } catch (\Exception $ex) {
                //do nothing
            }
            //set success message and redirect to bookings.show
            Session::flash('booking_updated', __('Vendor invoice email successfully sent.'));
            return redirect(url('/admin/transactions'));
        }
    }

    public function AddToEnvelope(Request $request, $transaction)
    {
        if ($request->envelope_id == 0) {
            return redirect()->back();
        } else {
            $envelopes = Envelope::where('id', $request->envelope_id)->first();
            $envId = $envelopes->id;
            $envName = $envelopes->name;
            $array = explode(',', $transaction, 1);
            Transaction::whereIn('id', $array)->update(['envelope_id' => $envId]);
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

            Transaction::whereIn('id', $array)->update(['envelope_id' => $BudgetId]);
            Session::flash('success', 'You have successfully add transaction # ' . $transaction . ' to Budget - ' . $budName);
            return redirect()->back();
        }
    }

    public function archive(Request $request)
    {
        Transaction::where('id', $request->trans_id)->update(['is_archived' => 1]);
        Session::flash('success', 'You have successfully archive transaction # ' . $request->trans_id);
        return redirect()->back();
    }

    public function hide(Request $request)
    {
        Transaction::where('id', $request->trans_id)->update(['is_hidden' => 1]);
        Session::flash('success', 'You have successfully hide transaction # ' . $request->trans_id);
        return redirect()->back();
    }

    function envelopeAutoComplete(Request $request)
    {
        if ($request->get('query')) {
            $query = $request->get('query');
            $envName = Envelope::where('name', 'LIKE', "%{$query}%")
                ->get();
            $output = '<ul class="dropdown-menu" style="display:block; ">';
            foreach ($envName as $env) {
                $output .= '
       <li><a href="#">' . $env->name . '</a></li>
       ';
            }
            $output .= '</ul>';
            echo $output;
        }
    }


    function budgetAutoComplete(Request $request)
    {
        if ($request->get('budget')) {
            $budget = $request->get('budget');
            $budgetName = Budget::where('name', 'LIKE', "%{$budget}%")
                ->get();
            $display = '<ul class="dropdown-menu" style="display:block; position:relative">';
            foreach ($budgetName as $budget) {
                $display .= '
       <li><a href="#">' . $budget->name . '</a></li>
       ';
            }
            $display .= '</ul>';
            echo $display;
        }
    }

    public function hideAll(Request $request)
    {

        $ids = $request->transaction_ids;
        if (is_array($ids)) {
            //for multiple Transactions
            foreach ($ids as $id) {
                Transaction::where('id', $id)->update(['is_hidden' => 1]);
            }
        }

        Session::flash('success', 'You have successfully hide transactions.');
        return redirect()->back();
    }


    public function visible()
    {
        DB::table('transactions')->where(['is_hidden' => 1])->where(['is_archived' => 0])->update(['is_hidden' => 0]);
        Session::flash('success', 'Translations made visible successfully.');
        return redirect('/admin/transactions');
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
}

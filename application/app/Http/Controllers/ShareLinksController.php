<?php

namespace App\Http\Controllers;

use App\Models\Envelope;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ShareLinksController extends Controller
{
    public $section = "Accounting";
    public $page = "Banking & Financial";

    public function shereReport($id)
    {
        $report_id = Crypt::decryptString($id);
        $getid = unserialize($report_id);
        $reporId = $getid[0];

        $to_time = strtotime(date('Y-m-d H:i:s'));
        $from_time = strtotime($getid[1]);
        $time = round(abs($to_time - $from_time) / 60, 2);


        if ($time > 1440) {
            return view('admin.reports.expired');
        } else {
            $envelopes = Envelope::find($reporId);
            $allenvelopes = Envelope::OrderBy('id', 'asc')->get();
            $transactions = Transaction::where('envelope_id', $reporId)->get();
            $category = Category::where('id', $envelopes->category_id)->first();

            $categoryName = $category->name;
            $section = $this->section;
            $grandTotal = 0;
            foreach ($transactions as $transaction) {
                $p = $transaction->total;
                $grandTotal += $p;
            }
            return view('admin.reports.share-link', compact('envelopes', 'allenvelopes', 'section', 'id', 'transactions', 'grandTotal', 'categoryName'));
        }
    }

    public function shareEmail($id)
    {

        $trans_id = Crypt::decryptString($id);
        $getid = unserialize($trans_id);
        $transId = $getid[0];
        $transaction = Transaction::where('id', $transId)->first();
        $extra_info = collect(json_decode($transaction->extra_info, true));
        return view('admin.transactions.transaction_email', compact('transaction', 'extra_info'));
        
    }
}

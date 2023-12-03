<?php

namespace App\Http\Controllers\Envelope;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Envelope;
use Illuminate\Http\Request;
use App\Models\Vendor;
use PDF;
use MPDF;
use Session;

class AddReceiptController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($id){
        Session::forget('transaction_id');
        Session::forget('envelope_id');
        $transactions = Transaction::where('envelope_id', '=' , null)->get();
        Session::push('envelope_id',$id);
        $vendors = Vendor::all();
        return view('admin.envelopes.AddReceipts.list', compact('transactions', 'vendors'));
    }

    public function show(Transaction $transaction){
        $extra_info = collect(json_decode($transaction->extra_info, true));
    	return view('admin.envelopes.AddReceipts.show', compact('transaction', 'extra_info'));
    }

    public function pdf(Transaction $transaction){
        $pdf = PDF::loadView('admin.envelopes.AddReceipts.invoice', compact('transaction'));
        return $pdf->download('invoice.pdf');
    }

    public function mpdf(Transaction $transaction){
        $config = [
          'title' => $transaction->vendor->name .  " Invoice"
        ];
        $extra_info = collect(json_decode($transaction->extra_info, true));
//        return view('admin.transactions.minvoice', compact('transaction', 'extra_info'));
        $pdf = MPDF::loadView('admin.envelopes.AddReceipts.minvoice', compact('transaction', 'extra_info'), [], $config);
        return $pdf->download('BackpocketReceipt_'.strtolower($transaction->vendor->name).'_'.$transaction->transaction_no);
    }

    public function bulkSession(Request $request){

        if(!Session::has('transaction_id')){
            Session::put('transaction_id',$request->fieldOne);
        }
        else{
            foreach($request->fieldOne as $field)
                Session::push('transaction_id',$field);
        }
    }
    public function clearSession(){
        Session::forget('transaction_id');
    }

    public function addSession(Request $request){
        Session::push('transaction_id',$request->id);
    }

    public function removeSession(Request $request){
        $transactions = Session::get('transaction_id');
        $found = null;
        foreach($transactions as $key=>$transaction){
            if($transactions == $request->id){
                $found = $key;
            }
        }
        Session::pull('transaction_id');
        unset($transactions[$found]);
        Session::put('transaction_id',$transactions);
    }
}



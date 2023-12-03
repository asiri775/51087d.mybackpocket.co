<?php


namespace App\Http\Controllers\User\Envelopes;



use App\Models\Category;
use App\Models\Vendor;
use App\Models\Envelope;
use App\Models\Transaction;


use App\Http\Controllers\Controller;
use App\Models\EnvelopeTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class EnvelopeReceiptController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index($id)
    {
        $envelope = Envelope::where('id', $id)->first();
        Session::forget('transaction_id');
        Session::forget('envelope_id');
        if ($envelope->vendor_id != null) {
            $transactions = Transaction::where('user_id', Auth::user()->id)->where('envelope_id', '=', null)->get();
        } else {
            $transactions = Transaction::where('user_id', Auth::user()->id)->where('envelope_id', '=', null)->where('vendor_id', $envelope->vendor_id)->get();
        }
        Session::push('envelope_id', $envelope->id);
        $vendors = Vendor::all();
        return view('user.envelopes.receipts.index', compact('transactions', 'vendors', 'envelope'));
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

    public function addReceipt(Request $request) {

        $transaction_id = Session::get('transaction_id');
        $id = Session::get('envelope_id');
      
        $envelope = Envelope::whereIn('id', $id)->get();
       
        $envId = '';
        $envDate = '';
        $envName  ='' ;
        if ($transaction_id) {
            foreach ($envelope as $env) {
                $envId = $env->id;
                $envDate = $env->envelope_date;
                $envName  = $env->name;
                $category = Category::where('id', $env->category_id)->first();
                $categoryName = $category->name;
            }
            $transactions = Transaction::whereIn('id', $transaction_id)->get();
            $grandTotal = 0;
            foreach ($transactions as $transaction) {
                $p = $transaction->total;
                $grandTotal += $p;
            }
            return view('user.envelopes.receipts.add_receipts', compact('envelope', 'transactions', 'transaction_id', 'id', 'envId', 'envDate', 'envName', 'grandTotal', 'categoryName'));
        }else{
            return redirect('/user/envelopes');
        }
    }

    public function addToEnvelope(Request $request)
    {
        $id = Session::get('envelope_id');
        $arrays = Session::get('transaction_id');
        $transId = $request->transId;
        if ($request->has('save')) {
            $envelope_id = Session::get('envelope_id');
            $name = Envelope::where('id', $envelope_id)->first()->name;
            foreach($transId as $t){
                $envTrans = EnvelopeTransaction::where('envelope_id', $envelope_id[0])->where('transaction_id', $t)->first();
                if($envTrans)
                {
                    $envTrans->update();
                }
                else
                {
                    $envTrans = new EnvelopeTransaction();
                    $envTrans->envelope_id = $envelope_id[0];
                    $envTrans->transaction_id = $t;
                    $envTrans->save();
                }
            }
            Session::flash('success', 'Receipt added to '.$name.' Envelope');
            Session::forget('transaction_id');
            Session::forget('envelope_id');
            return redirect('user/envelopes/preview/' .  $envelope_id[0]);
        } elseif ($request->has('cancel')) {
            Session::forget('transaction_id');
            Session::forget('envelope_id');
            Session::flash('success', 'You have canceled your envelope action');
            return redirect('/user/envelopes');
        }
    }

    public function deleteReceipt($id)
    {
        $transactions = Session::get('transaction_id');
        $found = [];
        foreach ($transactions as $key => $transaction) {
            if ($transaction != $id) {
                $found[] = $transaction;
            }
        }
        Session::forget('transaction_id');
        Session::put('transaction_id', $found);
        $transactions = Session::get('transaction_id');
        Session::flash('success', 'You have successfully remove Transaction #' );
        return redirect()->back();
    }

    public function previewUserEnvelope($id)
    {
        $envelope = Envelope::find($id);
        $allenvelopes = Envelope::OrderBy('id', 'asc')->get();
        $envTrans = EnvelopeTransaction::where('envelope_id', $id)->get();
        $transactions = $envelope->getTransactions($envelope->id);
      
        $categories = Category::orderBy('id', 'ASC')->get();
        $vendors = Vendor::orderBy('id', 'ASC')->get();
        $grandTotal = 0;
        foreach ($transactions as $transaction) {
            $p = number_format((float)$transaction->total, 2, '.', '')  ;
            $grandTotal += $p;
           
           
        }
       

        return view('user.envelopes.show', compact('envelope', 'allenvelopes', 'id', 'transactions', 'grandTotal', 'categories', 'vendors'));
    }

}

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

class EnvelopeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $categories = Category::orderBy('id', 'ASC')->get();
        $vendors = Vendor::orderBy('id', 'ASC')->get();
        return view('user.envelopes.index', compact('categories', 'vendors'));
    }

    public function create(Request $request)
    {
        $envelope = new Envelope();
        $envelope->name = $request->name;
        $envelope->category_id = $request->category;
        $envelope->enveloped_by = Auth::user()->id;
        $envelope->envelope_date = date('Y-m-d');
        $envelope->vendor_id = $request->vendor;
        $envelope->save();
        Session::flash('success', 'You have successfully added envelope #' . $envelope->id);
        return redirect()->back();
    }

    public function editUserEnvelope(Request $request, $id)
    {
        $envelope = Envelope::findorFail($id);
        $envelope->name = $request->name;
        $envelope->category_id = $request->category;
        $envelope->vendor_id = $request->vendor;
        $envelope->update();

        return redirect('/user/envelopes');
    }

    public function deleteUserEnvelope($id)
    {
        $envelope = Envelope::find($id);
        
        $envTrans = EnvelopeTransaction::where('envelope_id', $envelope->id)->delete();
       
        $envelope->delete();
        Session::flash('success', 'You have successfully remove envelope #' . $id);
        return redirect()->back();
    }

    public function bulkUserSession(Request $request){

        if(!Session::has('transaction_id')){
            Session::put('transaction_id',$request->fieldOne);
        }
        else{
            foreach($request->fieldOne as $field)
                Session::push('transaction_id',$field);
        }
    }

    public function clearUserSession(){
        Session::forget('transaction_id');
    }

    public function deleteEnvelopeItem($envelope, $id)
    {
       $envTrans = EnvelopeTransaction::where('envelope_id', $envelope)->where('transaction_id', $id)->delete();
        Session::flash('success', 'You have successfully remove transaction #' . $id);
        return redirect()->back();
    }

}

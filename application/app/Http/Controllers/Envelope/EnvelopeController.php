<?php

namespace App\Http\Controllers\Envelope;

use PDF;
use MPDF;
use Mpdf\Tag\Tr;
use App\Models\Vendor;
use App\Models\Envelope;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\EmailSubject;
use App\Models\EmailTemplate;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Model;

class EnvelopeController extends Controller
{
    public $section = "Accounting";
    public $page = "Banking & Financial";

    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {  
        $section = $this->section;
        $page = $this->page . ' envelope';
        $categories = Category::orderBy('id', 'ASC')->get();
        $envelopes = Envelope::all();
        return view('admin.envelopes.manage-index', compact('envelopes', 'section', 'page', 'categories'));
    }
    public function addEnvelope(Request $request)
    {
     
        if ($request->category) {
            $envelope = new Envelope();
            $envelope->name = $request->name;
            $envelope->category_id = $request->category;
            $envelope->enveloped_by = Auth::id();
            $envelope->envelope_date=date('Y-m-d');
            $envelope->save();
        } else {
            $envelope = new Envelope();
            $envelope->name = $request->name;
            $envelope->enveloped_by = Auth::id();
            $envelope->envelope_date=date('Y-m-d');
            $envelope->category_id = null;
            $envelope->save();
        }
        return redirect('admin/envelopes');
    }
    public function previewEnvelope($id)
    {
        $envelopes = Envelope::find($id);
        $allenvelopes = Envelope::OrderBy('id', 'asc')->get();
        $transactions = Transaction::where('envelope_id', $id)->get();
        $categories = Category::orderBy('id', 'ASC')->get();
        $section = $this->section;
        $grandTotal = 0;
        foreach ($transactions as $transaction) {
            $p = $transaction->total;
            $grandTotal += $p;
        }
        return view('admin.envelopes.envelope_preview', compact('envelopes', 'allenvelopes', 'section', 'id', 'transactions', 'grandTotal', 'categories'));
    }

    public function editEnvelope(Request $request, $id)
    {
        $envelope = Envelope::findorFail($id);
        $envelope->name = $request->name;
        $envelope->category_id = $request->category;
        $envelope->save();

        return redirect('admin/envelopes');
    }

    public function deleteEnvelope($id)
    {
        $envelope = Envelope::find($id);
        $transactions = Transaction::where(['envelope_id' => $id])->get();

        foreach ($transactions as $key => $transaction) {
            if ($transaction->envelope_id = $id) {
                $transaction->update(['envelope_id' => NULL]);
            }
        }
        $envelope->delete();
        Session::flash('success', 'You have successfully remove envelope #' . $id);
        return redirect()->back();
    }

    public function deleteEnvelopeItem($id)
    {
        
        $transaction = Transaction::find($id)->update(['envelope_id' => NULL]);
        Session::flash('success', 'You have successfully remove transaction #' . $id);
        return redirect()->back();
    }

    public function printPdf($id)
    {
         $transactions = Transaction::where('envelope_id', $id)->get();
        $envelope = Envelope::where('id', $id)->first();
        $count = Transaction::where('envelope_id', $id)->count();

        $category = Category::where('id', $envelope->category_id)->first();
        $categoryName = $category->name;
        $grandTotal = 0;
        foreach ($transactions as $transaction) {
            $p = $transaction->total;
            $grandTotal += $p;

            $printname = $transaction->printName($transaction->vendor_id);
        }
        $user = Auth::user()->name;
        $f_name = $envelope->name . uniqid() . '.pdf';

        Session::forget('transaction_id');
        return View('admin.envelopes.envelope_pdf', compact('categoryName','envelope', 'transactions', 'count', 'grandTotal', 'user'));
        //return $pdf->download('Envelope_BatchReceipts_' . date('mdY') . '.pdf');
        //return $pdf->stream($f_name);
    }

    public function previewExistingEnvelope(Request $request)
    {
        $transaction_id = Session::get('transaction_id');
        $id = Session::get('envelope_id');
        $envelope = Envelope::where('id', $id)->get();
        $category = Category::where('id', $id)->first();
        $categoryName = $category->name;
        $envId = '';
        $envDate = '';
        $envName  ='' ;
        if ($transaction_id) {
            foreach ($envelope as $env) {
                $envId = $env->id;
                $envDate = $env->envelope_date;
                $envName  = $env->name;
            }
            $transactions = Transaction::whereIn('id', $transaction_id)->get();
            $grandTotal = 0;
            foreach ($transactions as $transaction) {
                $p = $transaction->total;
                $grandTotal += $p;
            }
            return view('admin.envelopes.preview-existing-envelope', compact('envelope', 'transactions', 'transaction_id', 'id', 'envId', 'envDate', 'envName', 'grandTotal', 'categoryName'));
        }else{
            return redirect('/admin/envelopes');
        }
    }

    public function addToExistingenvelope(Request $request)
    {
        $id = Session::get('envelope_id');
        $arrays = Session::get('transaction_id');
        $transId = $request->transId;
    
        if ($request->has('save')) {
            $envelope_id = Session::get('envelope_id');
            $name = Envelope::where('id', $envelope_id)->first()->name;
            Transaction::whereIn('id',  $transId)->update(['envelope_id' => $envelope_id[0]]);
            Session::flash('success', 'Receipt added to '.$name.' Envelope');
            Session::forget('transaction_id');
            Session::forget('envelope_id');
            return redirect('admin/envelopes/preview/' .  $envelope_id[0]);
        } elseif ($request->has('cancel')) {
            Session::forget('transaction_id');
            Session::forget('envelope_id');
            Session::flash('success', 'You have canceled your envelope action');
            return redirect('/admin/envelopes');
        }
    }

    public function preview()
    {
        $ids = Session::get('transaction_id');
        if ($ids) {
            $section = $this->section;
            $page = $this->page . ' Preview envelope';
            if ($ids) {
                $transactions = Transaction::whereIn('id', $ids)->get();
                $grandTotal = 0;
                foreach ($transactions as $transaction) {
                    $p = $transaction->total;
                    $grandTotal += $p;
                }
            } else {
                $transactions = [];
                $grandTotal = 0.00;
            }

            return view('admin.envelopes.preview-envelope', compact('transactions', 'page', 'section', 'ids', 'grandTotal'));
        } else {
            Session::flash('success', 'You have already saved  !');
            return redirect('admin/transactions');
        }
    }

    public function bulkComplete(Request $request)
    {
        $arrays = implode(',', $request->envelope_id);
        $enve = Envelope::whereIn('id', $request->envelope_id)->update(['envelope_status' => 2]);
        Session::flash('success', 'You have successfully complete envelope #' . $arrays);
        return redirect()->back();
    }

    public function completeEnvelope($id)
    {
        $envelope = Envelope::find($id);
        $envelope->envelope_status = 2;
        $envelope->save();
        Session::flash('success', 'You have successfully complete Envelope #' . $id);
        return redirect()->back();
    }

    public function bulkDownload(Request $request)
    {
        $arrays = implode(',', $request->envelope_id);
        $envelope = Envelope::where('id', $request->envelope_id)->first();
        $transactions = Transaction::whereIn('id', $request->envelope_id)->get();
        $count = Transaction::where('envelope_id', $request->envelope_id)->count();
        $f_name = 'bulkDownload_' . $arrays . uniqid() . '.pdf';
        $pdf = PDF::loadview('admin.envelopes.bulk_pdf', compact('transactions', 'envelope', 'count'));
        return $pdf->download($f_name);
    }

    public function bulkDelete(Request $request, $id)
    {
        $trans_id = Session::get('transaction_id');
        $arrays = implode(',', $trans_id);
        Transaction::whereIn('id', $trans_id)->update(['envelope_id' => NULL]);
        Session::flash('success', 'You have successfully delete envelope receipts id\'s #' . $arrays);
        Session::forget('transaction_id');
        return redirect()->back();
    }

    public function deletePreview(Request $request)
    {
        $ids = $request->invoice_id;
        $invoices = Session::get('invoice_id');
        $found = [];
        foreach ($invoices as $key => $invoice) {
            if (in_array($invoice, $ids)) {
                $found[] = $key;
            }
        }
        Session::pull('invoice_id');
        foreach ($found as $f) {
            unset($invoices[$f]);
        }
        Session::put('invoice_id', $invoices);
        return redirect()->back();
    }

    public function deleteExisting($id)
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

    public function store(Request $request)
    {
        $account = $request->account;
        $date = date('Y-m-d');
        $name = $request->name;
        $arrays = Session::get('transaction_id');

        if ($arrays) {
            $current_user = Auth::user()->id;
            if ($request->has('save')) {
                $envelope = new envelope;
                $envelope->name = $request->name;
                $envelope->envelope_date = $date;
                $envelope->enveloped_to = $account;
                $envelope->enveloped_by = $current_user;
                $envelope->save();
                $transactions = Transaction::whereIn('id', $arrays)->update(['envelope_id' => $envelope->id]);
                Session::flash('success', 'You have successfully done envelope action');
                Session::forget('transaction_id');
                return redirect('admin/envelopes');
            } elseif ($request->has('print')) {
                $envelope = new envelope;
                $envelope->envelope_date = $date;
                $envelope->enveloped_to = $account;
                $envelope->enveloped_by = $current_user;
                $envelope->save();
                $transactions = Transaction::whereIn('id', $arrays)->update(['envelope_id' => $envelope->id]);
                //implode arrays
                $extract = implode(",", $arrays);
                $f_name = $extract . '.pdf';
                $count = Transaction::where('envelope_id', $arrays)->count();
                $pdf = PDF::loadView('admin.envelopes.envelope_pdf', compact('envelope', 'transactions', 'count'));
                Session::forget('transaction_id');
                return $pdf->stream($f_name);
            } elseif ($request->has('cancel')) {
                Session::forget('transaction_id');
                Session::flash('success', 'You have canceled your envelope action');
                return redirect('admin/transactions');
            }
        } else {
            return redirect('admin/transactions');
        }
    }

    public function addExistingIdSession(Request $request)
    {
        Session::push('envelope_id', $request->id);
    }

    public function clearSession()
    {
        Session::forget('transaction_id');
    }
    public function removeExistingIdSession(Request $request)
    {
        $transactions = Session::get('envelope_id');
        $found = null;
        foreach ($transactions as $key => $transaction) {
            if ($transactions == $request->id) {
                $found = $key;
            }
        }
        Session::pull('envelope_id');
        unset($transactions[$found]);
        Session::put('envelope_id', $transactions);
    }

        public function notify(Request $request)
    {

        //find booking
        if ($request->envelope_id) 
        {

            //send email to customer - refund true
            try {

                $envelope = Envelope::where('id', $request->envelope_id)->first();
                $transactions = Transaction::where('envelope_id', $request->envelope_id)->get();
                $count = Transaction::where('envelope_id', $request->envelope_id)->count();
		        $category = Category::where('id', $envelope->category_id)->first();

		        $categoryName = $category->name;
		        $grandTotal = 0;
		        foreach ($transactions as $transaction) {
		            $p = $transaction->total;
		            $grandTotal += $p;
		           
		        }
		        $user = 'Admin';

               $EmailSubject = EmailSubject::where('token', 'fs45djhg')->first();
               $EmailTemplate = EmailTemplate::where('domain', 6)->where('subject_id', $EmailSubject['id'])->first();
                
                $data = array(
                	'envelope_id'=>$request->envelope_id,
                    'email' => $request->send_email,
                    'bodyMessage' => $EmailTemplate['content'],
                    'subject' => $EmailSubject['subject']
                );
                $envelope_name=$envelope->name;

                $template = $EmailTemplate['content'];
                $grandTotal = 0;
                foreach ($transactions as $transaction) {
                    $p = $transaction->total;
                    $grandTotal += $p;
                }

                $pdf = PDF::loadView('admin.envelopes.envelope_pdf_email', compact('categoryName','envelope', 'transactions', 'count', 'grandTotal', 'user'));
                Mail::send('emails.vendor.EvelopePdfEmail',['template'=>$EmailTemplate,'envelope_name'=>$envelope_name],function ($message) use ($data,$pdf,$envelope_name){
                    $message->to($data['email']);
                    $message->subject('Envelope #' . $envelope_name . ': ' . $data['subject']);
                    $message->attachData($pdf->output(), 'envelope' . $envelope_name . '.pdf');
                });
    

            } catch (\Exception $ex) {
                //print_r($ex);die;
            }
            //set success message and redirect to bookings.show
           Session::flash('success', __('Evelope email successfully sent.'));
            return redirect('admin/envelopes');
        }
    }

    
    public function getAllEnvelopes()
    {
       $env='<option value="">Select Envelope</option>'; 
       foreach (Envelope::orderBy('name', 'ASC')->get() as $envelope) {
           $env.='<option value="'.$envelope->id.'">'.$envelope->name.'</option>';
       }

       return $env; 
    }
}

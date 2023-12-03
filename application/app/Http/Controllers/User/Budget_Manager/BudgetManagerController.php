<?php

namespace App\Http\Controllers\User\Budget_Manager;

use App\Models\Category;
use App\Models\Vendor;
use App\Models\Envelope;
use App\Models\Budget;
use App\Models\Transaction;
use App\Models\EmailSubject;
use App\Models\EmailTemplate;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class BudgetManagerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $categories = Category::orderBy('id', 'ASC')->get();
        $vendors = Vendor::orderBy('id', 'ASC')->get();
        $budgets = Budget::where('created_by', Auth::user()->id)->get();
        return view('user.budget_manager.index', compact('categories', 'vendors', 'budgets'));
    }

    public function create(Request $request)
    {
        if ($request->category) {
            $budget = new Budget();
            $budget->name = $request->name;
            $budget->category_id = $request->category;
            $budget->budget_date = date('Y-m-d');
            $budget->target_budget_value = $request->value;
            $budget->created_by = Auth::user()->id;
            $budget->save();
        } else {
            $budget = new Budget();
            $budget->name = $request->name;
            $budget->created_by = Auth::user()->id;
            $budget->budget_date = date('Y-m-d');
            $budget->category_id = null;
            $budget->save();
        }
        return redirect()->back();
    }

    public function editUserBudget(Request $request, $id)
    {
        $budget = Budget::findorFail($id);
        $budget->name = $request->name;
        $budget->target_budget_value = $request->value;
        $budget->category_id = $request->category;
        $budget->save();

        return redirect('/user/budget-manager');
    }

    public function deleteUserBudget($id)
    {
        $budget = Budget::find($id);
        $transactions = Transaction::where(['budget_id' => $id])->get();
        $envelopes = Envelope::where(['budget_id' => $id])->get();
        foreach ($transactions as $key => $transaction) {
            if ($transaction->budget_id = $id) {
                $transaction->update(['budget_id' => NULL]);
            }
        }
        foreach ($envelopes as $key => $envelope) {
            if ($envelope->budget_id = $id) {
                $envelope->update(['budget_id' => NULL]);
            }
        }
        $budget->delete();
        Session::flash('success', 'You have successfully remove budget #' . $id);
        return redirect()->back();
    }

    public function deleteBudgetTransaction($id)
    {
        $transaction = Transaction::find($id)->update(['budget_id' => NULL]);
        Session::flash('success', 'You have successfully remove transaction #' . $id);
        return redirect()->back();
    }
    public function deleteBudgetEnvelope($id)
    {

        $envelope = Envelope::find($id)->update(['budget_id' => NULL]);
        Session::flash('success', 'You have successfully remove envelope #' . $id);
        return redirect()->back();
    }

    public function budgetAddReceipt($id)
    {
        Session::forget('transaction_budget_id');
        Session::forget('budget_id');
        $transactions = Transaction::where('budget_id', '=', null)->get();
        $vendors = Vendor::get();
        Session::push('budget_id', $id);
        return view('user.budget_manager.add_receipt', compact('transactions', 'vendors'));
    }



    public function bulkSession(Request $request)
    {

        if (!Session::has('transaction_budget_id')) {
            Session::put('transaction_budget_id', $request->fieldOne);
        } else {
            foreach ($request->fieldOne as $field)
                Session::push('transaction_budget_id', $field);
        }
    }

    public function clearSession()
    {
        Session::forget('transaction_budget_id');
    }

    public function addSession(Request $request)
    {
        Session::push('transaction_budget_id', $request->id);
    }

   

    public function removeSession(Request $request){
        $transactions = Session::get('transaction_budget_id');
        $found = null;
        Session::forget('transaction_budget_id');
        foreach($transactions as $key=>$transaction){
            if($transaction == $request->id){
                $found = $key;
            }
            else
            {
                Session::push('transaction_budget_id',$transaction);
            }
        }
       
       
    }

    public function previewExistingBudget(Request $request)
    {
        $transaction_b_id = Session::get('transaction_budget_id');
        $id = Session::get('budget_id');
        $budget = Budget::where('id', $id)->get();
        $category = Category::where('id', $id)->first();
        $categoryName = $category->name;
        $envId = '';
        $envDate = '';
        $envName  = '';
        if ($transaction_b_id) {
            foreach ($budget as $env) {
                $envId = $env->id;
                $envDate = $env->budget_date;
                $envName  = $env->name;
            }
            $transactions = Transaction::whereIn('id', $transaction_b_id)->get();
            $grandTotal = 0;
            foreach ($transactions as $transaction) {
                $p = $transaction->total;

                $grandTotal += $p;
            }
            return view('user.budget_manager.add_receipt_preview', compact('budget', 'transactions', 'transaction_b_id', 'id', 'envId', 'envDate', 'envName', 'grandTotal', 'categoryName'));
        } else {
            return redirect('/user/budget-manager');
        }
    }

    public function sendOverBudgetEmail($id)
    {
        $budget=Budget::where('id', $id)->first();
        $sum = Transaction::where('budget_id',  $id)->sum('total');
      
        $EmailSubject = EmailSubject::where('token', 'k35gj41h')->first();
        $EmailTemplate = EmailTemplate::where('domain', 6)->where('subject_id', $EmailSubject['id'])->first();

         //find booking
         if ($id) 
         {
 
             //send email to customer - refund true
             try {
 
                  Mail::send('emails.vendor.OverBudgetEmail',['template'=>$EmailTemplate,'budgetLimit'=>$budget->target_budget_value,''=>$sum],function ($message) {
                     $message->to(Auth::user()->email);
                     $message->subject('Nearing and exceeding the budget amount of Budget' . $budget->name);
                     
                 });
     
 
             } catch (\Exception $ex) {
                 //print_r($ex);die;
             }

         }
    }
    
    public function addToBudget(Request $request)
    {
        // Mail::raw('Hello World!', function($msg) {$msg->to('asiridula@gmail.com')->subject('Test Email'); });
        $id = Session::get('budget_id');
        $arrays = Session::get('transaction_budget_id');
        $transId = $request->transId;
        $budgetLimit=Budget::where('id', $id)->first()->target_budget_value;
        $sum = Transaction::where('budget_id',  $id)->sum('total');
        $newOne=Transaction::where('id', $transId)->first();
        $newTotal=$sum+$newOne->total;
        if($newTotal>$budgetLimit)
        {
           $this->sendOverBudgetEmail($id);
        }

        if ($request->has('save')) {
            $budget_id = Session::get('budget_id');
            Transaction::whereIn('id',  $transId)->update(['budget_id' => $budget_id[0]]);
            $name = Budget::where('id', $budget_id)->first()->name;
            Session::flash('success', 'Receipt added to ' . $name . ' Budget');
            Session::forget('transaction_budget_id');
            Session::forget('budget_id');
            return redirect('/user/budget-manager/preview/' .  $budget_id[0]);
        } elseif ($request->has('cancel')) {
            Session::forget('transaction_budget_id');
            Session::forget('budget_id');
            Session::flash('success', 'You have canceled your budget action');
            return redirect('/user/budget-manager');
        }
    }

    public function deleteReceipt($id)
    {
        $transactions = Session::get('transaction_budget_id');
        $found = [];
        foreach ($transactions as $key => $transaction) {
            if ($transaction != $id) {
                $found[] = $transaction;
            }
        }
        Session::forget('transaction_budget_id');
        Session::put('transaction_budget_id', $found);
        $transactions = Session::get('transaction_budget_id');
        Session::flash('success', 'You have successfully remove Transaction #' );
        return redirect()->back();
    }
    public function previewBudget($id)
    {
        $budgets = Budget::find($id);
        $allbudgets = Budget::OrderBy('id', 'asc')->get();
        $transactions = Transaction::where('budget_id', $id)->get();
        $categories = Category::orderBy('id', 'ASC')->get();
        $envelopes = Envelope::where('budget_id', $id)->get();
        $grandTotal = 0;
        foreach ($transactions as $transaction) {
            $p = $transaction->total;
            $grandTotal += $p;
        }
        foreach($envelopes as $envelope) {
            $env = $envelope->EnvelopAmount($envelope->id);
            $grandTotal += $env;
        }

        return view('user.budget_manager.preview', compact('budgets', 'allbudgets',  'id', 'transactions', 'grandTotal', 'categories', 'envelopes'));
    }

    public function budgetAddEnvelope($id)
    {
        Session::forget('budget_envelope_id');
        Session::forget('budget_id');
        $envelopes = Envelope::where('budget_id', '=', null)->get();
        $categories = Category::orderBy('id', 'ASC')->get();
        $vendors = Vendor::get();
        Session::push('budget_id', $id);
        return view('user.budget_manager.add_envelope', compact('envelopes', 'vendors', 'categories'));
    }


    public function bulkEnvelopeSession(Request $request)
    {

        if (!Session::has('budget_envelope_id')) {
            Session::put('budget_envelope_id', $request->fieldOne);
        } else {
            foreach ($request->fieldOne as $field)
                Session::push('budget_envelope_id', $field);
        }
    }

    public function clearEnvelopeSession()
    {
        Session::forget('budget_envelope_id');
    }

    public function addEnvelopeSession(Request $request)
    {
        Session::push('budget_envelope_id', $request->id);
    }

    public function removeEnvelopeSession(Request $request)
    {
        $envelopes = Session::get('budget_envelope_id');
        $found = null;
        foreach ($envelopes as $key => $envelope) {
            if ($envelopes == $request->id) {
                $found = $key;
            }
        }
        Session::pull('budget_envelope_id');
        unset($envelopes[$found]);
        Session::put('budget_envelope_id', $envelopes);
    }

    public function previewExistingEnvelopeBudget(Request $request)
    {
        $envelope_id = Session::get('budget_envelope_id');
        $id = Session::get('budget_id');
        $budget = Budget::where('id', $id)->first();
        $category = Category::where('id', $budget->category_id)->first();
        $categoryName = $category->name;

        if ($envelope_id) {

            $envelopes = Envelope::whereIn('id', $envelope_id)->get();
            $grandTotal = 0;
            foreach ($envelopes as $envelope) {
                $p = $envelope->EnvelopAmount($envelope->id);
                $grandTotal += $p;
            }
            return view('user.budget_manager.add_envelope_preview', compact('budget', 'envelopes', 'envelope_id', 'id', 'grandTotal', 'categoryName'));
        } else {
            return redirect('/user/budget-manager');
        }
    }


    public function deleteEnvelope($id)
    {
        $envelopes = Session::get('budget_envelope_id');
        $found = [];
        foreach ($envelopes as $key => $envelope) {
            if ($envelope != $id) {
                $found[] = $envelope;
            }
        }
        Session::forget('budget_envelope_id');
        Session::put('budget_envelope_id', $found);
        $envelopes = Session::get('budget_envelope_id');
        Session::flash('success', 'You have successfully remove Envelope #' );
        return redirect()->back();
    }

    public function addEnvelopeToBudget(Request $request)
    {
        $id = Session::get('budget_id');
        $arrays = Session::get('budget_envelope_id');
        $envId = $request->envId;

        if ($request->has('save')) {
            $budget_id = Session::get('budget_id');
            Envelope::whereIn('id',  $envId)->update(['budget_id' => $budget_id[0]]);
            $name = Budget::where('id', $budget_id)->first()->name;
            Session::flash('success', 'Envelope added to ' . $name . ' Budget');
            Session::forget('budget_envelope_id');
            Session::forget('budget_id');
            return redirect('/user/budget-manager/preview/' .  $budget_id[0]);
        } elseif ($request->has('cancel')) {
            Session::forget('budget_envelope_id');
            Session::forget('budget_id');
            Session::flash('success', 'You have canceled your budget action');
            return redirect('/user/budget-manager');
        }
    }
}

<?php

namespace App\Http\Controllers\Budgets;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\Budget;
use Illuminate\Http\Request;
use PDF;
use MPDF;
use Illuminate\Support\Facades\Session;

class AddBudgetReceiptController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($id){
        Session::forget('transaction_budget_id');
        Session::forget('budget_id');
        $transactions = Transaction::where('budget_id', '=' , null)->get();
        Session::push('budget_id',$id);
        return view('admin.budgets.BudgetManager.list', compact('transactions'));
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
        $envName  ='' ;
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
            return view('admin.budgets.BudgetManager.preview-receipts', compact('budget', 'transactions', 'transaction_b_id', 'id', 'envId', 'envDate', 'envName', 'grandTotal', 'categoryName'));
        }else{
            return redirect('/admin/budgets');
        }
    }

    public function bulkSession(Request $request){

        if(!Session::has('transaction_budget_id')){
            Session::put('transaction_budget_id',$request->fieldOne);
        }
        else{
            foreach($request->fieldOne as $field)
                Session::push('transaction_budget_id',$field);
        }
    }

    public function clearSession()
    {
        Session::forget('transaction_budget_id');
    }

    public function addSession(Request $request){
        Session::push('transaction_budget_id',$request->id);
    }


    public function removeSession(Request $request){
        $transactions = Session::get('transaction_budget_id');
        $found = null;
        foreach($transactions as $key=>$transaction){
            if($transactions == $request->id){
                $found = $key;
            }
        }
        Session::pull('transaction_budget_id');
        unset($transactions[$found]);
        Session::put('transaction_budget_id',$transactions);
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

    public function addToBudget(Request $request)
    {
        $id = Session::get('budget_id');
        $arrays = Session::get('transaction_budget_id');
        $transId = $request->transId;
    
        if ($request->has('save')) {
            $budget_id = Session::get('budget_id');
            Transaction::whereIn('id',  $transId)->update(['budget_id' => $budget_id[0]]);
            $name = Budget::where('id', $budget_id)->first()->name;
            Session::flash('success', 'Receipt added to '.$name.' Budget');
            Session::forget('transaction_budget_id');
            Session::forget('budget_id');
            return redirect('admin/budgets/preview/' .  $budget_id[0]);
        } elseif ($request->has('cancel')) {
            Session::forget('transaction_budget_id');
            Session::forget('budget_id');
            Session::flash('success', 'You have canceled your budget action');
            return redirect('/admin/budgets');
        }
    }
}

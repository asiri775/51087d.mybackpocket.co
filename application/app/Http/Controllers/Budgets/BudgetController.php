<?php

namespace App\Http\Controllers\Budgets;

use PDF;
use MPDF;
use Session;
use Mpdf\Tag\Tr;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\Email\EmailSubject;
use Illuminate\Support\Facades\DB;
use App\Models\Email\EmailTemplate;
use App\Http\Controllers\Controller;
use App\Models\Budget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Model;

class BudgetController extends Controller
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
        $page = $this->page . ' budget';
        $categories = Category::orderBy('id', 'ASC')->get();
        $budgets = Budget::all();
        return view('admin.budgets.index', compact('budgets', 'section', 'page', 'categories'));
    }

    public function addBudget(Request $request)
    {
        if ($request->category) {
            $budget = new Budget();
            $budget->name = $request->name;
            $budget->category_id = $request->category;
            $budget->budget_date=date('Y-m-d');
            $budget->target_budget_value = $request->value;
            $budget->created_by = Auth::id();
            $budget->save();
        } else {
            $budget = new Budget();
            $budget->name = $request->name;
            $budget->created_by = Auth::id();
            $budget->budget_date=date('Y-m-d');
            $budget->category_id = null;
            $budget->save();
        }
        return redirect('admin/budgets');
    }

    public function deleteBudget($id)
    {
        $budget = Budget::find($id);
        $transactions = Transaction::where(['budget_id' => $id])->get();

        foreach ($transactions as $key => $transaction) {
            if ($transaction->budget_id = $id) {
                $transaction->update(['budget_id' => NULL]);
            }
        }
        $budget->delete();
        Session::flash('success', 'You have successfully remove budget #' . $id);
        return redirect()->back();
    }

    public function previewBudget($id)
    {
        $budgets = Budget::find($id);
        $allbudgets = Budget::OrderBy('id', 'asc')->get();
        $transactions = Transaction::where('budget_id', $id)->get();
        $categories = Category::orderBy('id', 'ASC')->get();
        $section = $this->section;
        $grandTotal = 0;
        foreach ($transactions as $transaction) {
            $p = $transaction->total;
            $grandTotal += $p;
        }
        return view('admin.budgets.budget_preview', compact('budgets', 'allbudgets', 'section', 'id', 'transactions', 'grandTotal', 'categories'));
    }

    public function editBudget(Request $request, $id)
    {
        $budget = Budget::findorFail($id);
        $budget->name = $request->name;
        $budget->target_budget_value = $request->value;
        $budget->category_id = $request->category;
        $budget->save();

        return redirect('admin/budgets');
    }

    public function deleteBudgetItem($id)
    {
        $transaction = Transaction::find($id)->update(['budget_id' => NULL]);
        Session::flash('success', 'You have successfully remove transaction #' . $id);
        return redirect()->back();
    }

    public function bulkDelete(Request $request, $id)
    {
        $trans_id = Session::get('transaction_id');
       
        $arrays = implode(',', $trans_id);
       
        $trans = Transaction::whereIn('id', $trans_id)->update(['budget_id' => NULL]);
        Session::flash('success', 'You have successfully delete budget #' . $arrays);
         Session::forget('transaction_id');
        return redirect()->back();
    }

    public function getAllBudgets()
    {
       $env='<option value="">Select Budgets</option>'; 
       foreach (Budget::orderBy('name', 'ASC')->get() as $budget) {
           $env.='<option value="'.$budget->id.'">'.$budget->name.'</option>';
       }
       
       return $env; 
    }
}

<?php


namespace App\Http\Controllers\Budgets;

use App\Http\Controllers\Controller;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Session;
use App\Bank_Account;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use App\User;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

use Illuminate\Http\Request;

class BudgetReportController extends Controller
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
      
        $user = Auth::user()->name;
        $userId = Auth::user()->id;
        $categories = Category::orderBy('id', 'ASC')->get();
        $budgets = Budget::all();
       
        return view('admin.budgets.BudgetReport.index', compact('budgets', 'section', 'page', 'categories', 'user','userId'));
    }

    public function usersList($id){
        
        $user = User::where('id', $id)->first();
        
        $section = $this->section;
        $page = $this->page . ' budget';
        $categories = Category::orderBy('id', 'ASC')->get();
        $budgets = Budget::where('created_by', $id)->get();

        $budgetCount = Budget::count('created_by', $id);

       $grandTotal = Budget::getGrandBudgetTotal($id);
    
        return view('admin.budgets.BudgetReport.user-reports', compact('section', 'page', 'categories', 'budgets', 'budgetCount', 'user','grandTotal'));
       
    }


}

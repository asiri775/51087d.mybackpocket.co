<?php

namespace App\Http\Controllers\Budgets;

use App\Http\Controllers\Controller;

use Carbon\Carbon;
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

class BudgetReportTableController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function dateConvertion($date)
    {
        $d=explode('-',$date);
        $date=$d[2].'-'.$d[0].'-'.$d[1];
        return $date;
    }
    public function __invoke(Request $request)
    {
        $budgets = Budget::query();

        switch (request()->date_option) {
            case 'yesterday':
                $budgets = $budgets->whereDate('budget_date', '=', Carbon::now()->subDay());
                break;
            case 'today':
                $budgets = $budgets->whereDate('budget_date', '=', Carbon::now());
                break;
            case 'this_weekdays':
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek()->subDays(2);
                $budgets = $budgets->whereBetween('budget_date', [$start, $end]);
                break;
            case 'this_whole_week':
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();
                $budgets = $budgets->whereBetween('budget_date', [$start, $end]);
                break;
            case 'this_month':
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
                $budgets = $budgets->whereBetween('budget_date', [$start, $end]);
                break;
            case 'this_year':
                $start = Carbon::now()->startOfYear();
                $end = Carbon::now()->endOfYear();
                $budgets = $budgets->whereBetween('budget_date', [$start, $end]);
                break;
            default:
                break;
        }
        if (request()->year_to_date) {
            $start = Carbon::now()->startOfYear();
            $end = Carbon::createFromDate(request()->year_to_date);
            $budgets = $budgets->whereBetween('budget_date', [$start, $end]);
        }
        if (request()->from) {
            $from=$this->dateConvertion(request()->from);
            if(!isset($from))
            {
               $from=Carbon::now()->startOfYear();
            }
            $budgets = $budgets->where('budget_date', '>=', $from);
        }
        if (request()->to) {
            $to=$this->dateConvertion(request()->to);
            if(!isset($to))
            {
                $to=Carbon::now()->startOfYear();
            }
            $budgets = $budgets->where('budget_date', '<=', $to);
        }
        if ($request->user_name) {
            $name = $request->user_name;
            $userList = User::where('name', 'like',  "%{$name}%")->get();
            $budgets->whereIn('created_by', $userList);
        }

        if ($request->category_option) {
            $categoryList = array();
            $category = $request->category_option;
            $budgets->where('category_id', $category);
        }
        
        if ($request->budget_name != '') {
            $budgets = $budgets->where('name', 'like', "%{$request->budget_name}%")->get();
        } else {
            $budgets = $budgets->get();
        }
        return DataTables::of($budgets)
            ->addColumn('budget_date', function ($budget) {
                return date('m-d-Y', strtotime($budget->budget_date));
            })
            ->addColumn('user_name', function ($budget) {

                return $budget->getUserById($budget->created_by)->name;
            })
            ->addColumn('user_id', function ($budget) {

                return $budget->created_by;
            })
            ->addColumn('budget_name', function ($budget) {

                return $budget->name;
            })
            ->addColumn('budget_category', function ($budget) {
                return $budget->getCategoryName($budget->category_id)->name;
            })


            ->make(true);
    }
}

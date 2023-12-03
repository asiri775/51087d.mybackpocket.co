<?php

namespace App\Http\Controllers\User\Budget_Manager;

use App\Models\Category;
use App\Models\Vendor;
use App\Models\Budget;



use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BudgetManagerTableController extends Controller
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
        $budgets = Budget::where('created_by', Auth::user()->id);
        switch (request()->date_option) {
            case 'today':
                $budgets = $budgets->whereDate('budget_date', '=', Carbon::now());
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

        if (request()->budget_name) {
            $budgets = $budgets->where('name', 'like', '%' . request()->budget_name . '%');
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
            $budgets = $budgets->where('budget_date', '<=',$to);
        }

        if ($request->category_id != '') {
            $budgets->where('category_id', "{$request->category_id}");
        } else {
            $budgets = $budgets->get();
        }
        return DataTables::of($budgets)
            ->addColumn('checkboxes', function ($budget) {
                $action = '<input type="checkbox" name="pdr_checkbox[]" class="pdr_checkbox" value="' . $budget->id . '" />';
                return $action;
            })
            ->addColumn('budget_date', function ($budget) {
                return date('m-d-Y', strtotime($budget->budget_date));
            })
            ->addColumn('category_name', function ($budget) {
                if ($budget->category_id != null) {
                    $catname = $budget->category->name;
                } else {
                    $catname = "No Category";
                }
                return $catname;
            })
            ->addColumn('category_id', function ($budget) {
                if ($budget->category_id != null) {
                    $catId = $budget->category->id;
                } else {
                    $catId = "No vendor";
                }
                return $catId;
            })

            ->addColumn('current_value', function ($budget) {
                $amount = $budget->BudgetAmount($budget->id);
                $total = number_format((float)$amount, 2, '.', '');
                return $total;
            })
            ->addColumn('target_budget_value', function ($budget) {
                $total = number_format((float)$budget->target_budget_value, 2, '.', '');
                return $total;
            })

            ->addColumn('variance', function ($budget) {
                $var = $budget->BudgetAmount($budget->id) - $budget->target_budget_value;
                if ($var > 0) {
                    $variance = '<span style="color:  red; text-align:center;">$' . ($var) . '</span>';
                } else {
                    $variance = '<span style="color:  #00238C; text-align:center;">$' . abs($var) . '</span>';
                }
                $v= "$".number_format((float)$variance, 2, '.', '') ;
                return $v;
            })
            ->addColumn('actions', function ($budget) {
                $action = '
                <div class="btn-group">
                <a class="btn btn-primary"
                href="' . route('user.budget.add.reciepts', ['Budget' => $budget]) . '"
                id="sessionSave" data-toggle="tooltip" data-placement="bottom"
                title="Add reciept" name="add"><i class="fa fa-plus"
                    aria-hidden="true"></i></a>
                <a class="btn btn-success"
                    href="' . route('user.budget.add.envelopes', ['Budget' => $budget]) . '"
                    id="sessionSave" data-toggle="tooltip" data-placement="bottom"
                    title="Add Envelope" name="add"><i class="fa fa-envelope"
                        aria-hidden="true"></i></a>
                 <a href="' . route('user.budget.preview', ['id' => $budget]) . '"
                    class="btn btn-complete" data-toggle="tooltip"
                    data-placement="bottom" title="Edit"><i class="fa fa-edit"></i>
                </a>
                <a  href="' . route('user.budget.manager.delete', ['id' => $budget]) . '"  class="btn btn-danger" data-toggle="tooltip"
                data-placement="bottom" title="Delete"><i class="fa fa-trash-o"></i></a>
                </div>
            ';
                return $action;

            })
            ->rawColumns(['checkboxes', 'actions', 'variance'])
            ->make(true);
    }
}

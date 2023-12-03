<?php


namespace App\Http\Controllers\Budgets;

use Carbon\Carbon;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class AddBudgetReceiptTableController extends Controller

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
        $transactions = Transaction::where('is_archived', '=', 0);
        $transactions->where('budget_id', '=', null);
        switch (request()->date_option) {
            case 'yesterday':
                $transactions = $transactions->whereDate('transaction_date', '=', Carbon::now()->subDay());
                break;
            case 'today':
                $transactions = $transactions->whereDate('transaction_date', '=', Carbon::now());
                break;
            case 'this_weekdays':
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek()->subDays(2);
                $transactions = $transactions->whereBetween('transaction_date', [$start, $end]);
                break;
            case 'this_whole_week':
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();
                $transactions = $transactions->whereBetween('transaction_date', [$start, $end]);
                break;
            case 'this_month':
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
                $transactions = $transactions->whereBetween('transaction_date', [$start, $end]);
                break;
            case 'this_year':
                $start = Carbon::now()->startOfYear();
                $end = Carbon::now()->endOfYear();
                $transactions = $transactions->whereBetween('transaction_date', [$start, $end]);
                break;
            default:
                break;
        }
        if (request()->year_to_date) {
            $start = Carbon::now()->startOfYear();
            $end = Carbon::createFromDate(request()->year_to_date);
            $transactions = $transactions->whereBetween('transaction_date', [$start, $end]);
        }
        if (request()->from) {
            $from=$this->dateConvertion(request()->from);
            if(!isset($from))
            {
               $from=Carbon::now()->startOfYear();
            }
            $transactions = $transactions->where('transaction_date', '>=',  $from);
        }
        if (request()->to) {
            $to=$this->dateConvertion(request()->to);
            if(!isset($to))
            {
                $to=Carbon::now()->startOfYear();
            }
            $transactions = $transactions->where('transaction_date', '<=', $to);
        }
        if ($request->vendor_name) {
            $name = $request->vendor_name;
            $transactions = $transactions->whereHas('vendor', function ($query) use ($name) {
                $query->where('name', 'like', "%{$name}%");
            });
        }
        if ($request->order_no != '') {
            $transactions = $transactions->where('order_no', 'like', "%{$request->order_no}%")->get();
        } else {
            $transactions = $transactions->get();
        }
        return DataTables::of($transactions)
            ->addColumn('checkbox', function ($transaction) {
                $select = '<input type="checkbox" name="checkbox[]" value="' . $transaction->id . '" id="checkbox_' . $transaction->id . ' checked">';
                return $select;

            })
            ->addColumn('transaction_date', function ($transaction) {
                return date('m-d-Y', strtotime($transaction->transaction_date));
            })
            ->addColumn('transaction_time', function ($transaction) {
                return date('h:i:s A', strtotime($transaction->transaction_date));
            })
            ->addColumn('vendor_name', function ($transaction) {
                return $transaction->vendor != null ? $transaction->vendor->name : '-';
            })
            ->addColumn('vendor_email', function ($transaction) {
                return $transaction->vendor != null ? $transaction->vendor->email : '-';
            })
            ->addColumn('actions', function ($transaction) {
                $action = '
                    <div class="btn-group">
                        <a href="' . route('AddReceipts.detail', ['transaction' => $transaction]) . '" class="btn btn-complete"><i class="fa fa-eye"></i>
                        </a>
                        <a href="#!" class="btn btn-success"><i class="fa fa-envelope"></i>
                        </a>
                        <a href="' . route('AddReceipts.mpdf', ['transaction' => $transaction->id]) . '" class="btn btn-primary"><i class="fa fa-download"></i>
                        </a>
                    </div>
                ';
                return $action; //$transaction->action_buttons;
            })
            ->rawColumns(['checkbox', 'actions'])
            ->make(true);
    }
}


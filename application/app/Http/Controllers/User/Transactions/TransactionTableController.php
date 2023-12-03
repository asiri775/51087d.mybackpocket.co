<?php

namespace App\Http\Controllers\User\Transactions;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class TransactionTableController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request)
    {
        $transactions = Transaction::where('user_id', Auth::user()->id)->where('is_hidden', '=', 0);
        switch (request()->date_option) {
            case 'yesterday':
                $transactions = $transactions->whereDate('transaction_date', '=', Carbon::yesterday());
                break;
            case 'today':
                $transactions = $transactions->whereDate('transaction_date', '=', Carbon::now());
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
        if (request()->from) {
            $transactions = $transactions->whereDate('transaction_date', '>=', Carbon::createFromDate(request()->from));
        }
        if(request()->to) {
            $transactions = $transactions->whereDate('transaction_date', '<=', Carbon::createFromDate(request()->to));
        }
        if (request()->vendor_email) {
            $email = $request->vendor_email;
            $transactions = $transactions->whereHas('vendor', function ($query) use ($email) {
                $query->where('email', 'like', "%{$email}%");
            });
        }
        if (request()->vendor_name) {
            $name = $request->vendor_name;
            $transactions = $transactions->whereHas('vendor', function ($query) use ($name) {
                $query->where('name', 'like', "%{$name}%");
            });
        }
       
        $transactions->orderBy('transaction_date', 'DESC');
        if ($request->order_no != '') {
            $transactions = $transactions->where('order_no', 'like', "%{$request->order_no}%")->get();
        } else {
            $transactions = $transactions->get();
        }
        return DataTables::of($transactions)
        ->addColumn('checkboxes', function ($transaction) {
            $action = '<input type="checkbox" name="pdr_checkbox[]" class="pdr_checkbox" value="'.$transaction->id.'" />';
            return $action;
        })
        ->addColumn('transaction_date', function ($transaction) {
            return date('m-d-Y', strtotime($transaction->transaction_date));
        })
        ->addColumn('vendor_name', function ($transaction) {
            return  (isset($transaction->vendor->name))?$transaction->vendor->name:'Undefined';
        })
        ->addColumn('vendor_id', function ($transaction) {
            return  (isset($transaction->vendor->id))?$transaction->vendor->id:'';
        })
        ->addColumn('vendor_email', function ($transaction) {
            return  (isset($transaction->vendor->email))?$transaction->vendor->email:'';
        })

        ->addColumn('total', function ($transaction) {
            $total = "$". number_format((float)$transaction->total, 2, '.', '');
            return $total;
        })
        ->addColumn('actions', function ($transaction) {
            $action = '
                <div class="btn-group">
                    <a href="' . route('user.transactions.show', ['transaction' => $transaction]) . '" class="btn btn-complete" data-toggle="tooltip"
                    data-placement="bottom" title="Preview"><i class="fa fa-eye"></i>
                    </a>
                    <a href="#!" onclick="modalSend(' . $transaction->id . ')" class="btn btn-success" data-toggle="modal" data-target="#send" trans_id="' . $transaction->id . '"
                    data-placement="bottom" title="Email"><i class="fa fa-envelope"></i>
                    </a>
                    <a href="' . route('transactions.mpdf', ['transaction' => $transaction->id]) . '" class="btn btn-primary" data-toggle="tooltip"
                    data-placement="bottom" title="Download"><i class="fa fa-download"></i>
                    </a>

                </div>
            ';
            return $action; //$transaction->action_buttons;
        })
        ->rawColumns(['checkboxes','actions'])
        ->make(true);
    }
}

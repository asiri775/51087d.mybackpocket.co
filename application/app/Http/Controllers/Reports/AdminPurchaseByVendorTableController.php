<?php
namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;


class AdminPurchaseByVendorTableController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function __invoke(Request $request)
    {
        $transactions = Transaction::orderBy('transaction_date', 'DESC');
        switch (request()->date_option) {
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
        if ($request->vendor_id != '') {
            $transactions->where('vendor_id', "{$request->vendor_id}");
        }

        if (request()->search_user) {
            $user = $request->search_user;
            $transactions = $transactions->whereHas('user', function ($query) use ($user) {
                $query->where('name', 'like', "%{$user}%");
            });
        }
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
            return date('m/d/Y', strtotime($transaction->transaction_date));
        })
        ->addColumn('vendor_name', function ($transaction) {
            return $transaction->vendor->name;
        })
        ->addColumn('vendor_id', function ($transaction) {
            return $transaction->vendor->id;
        })
        ->addColumn('user_name', function ($transaction) {
            return $transaction->user->name;
        })
        ->addColumn('vendor_email', function ($transaction) {
            return $transaction->vendor->email;
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

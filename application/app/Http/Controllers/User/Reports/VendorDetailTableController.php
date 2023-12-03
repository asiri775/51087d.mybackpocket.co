<?php

namespace App\Http\Controllers\User\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Transaction;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class VendorDetailTableController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function __invoke(Request $request)
    {
      
        $transactions = Transaction::where('user_id', Auth::user()->id)->where('vendor_id', $request->vendor);
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
        if (request()->to) {
            $transactions = $transactions->whereDate('transaction_date', '<=', Carbon::createFromDate(request()->to));
        }
        if ($request->vendor_id != '') {
            $transactions->where('vendor_id', "{$request->vendor_id}");
        }
        $transactions->orderBy('transaction_date', 'DESC');
        if ($request->order_no != '') {
            $transactions = $transactions->where('order_no', 'like', "%{$request->order_no}%")->get();
        } else {
            $transactions = $transactions->get();
        }
        return DataTables::of($transactions)
            ->addColumn('checkboxes', function ($transaction) {
                $action = '<input type="checkbox" name="pdr_checkbox[]" class="pdr_checkbox" value="' . $transaction->id . '" />';
                return $action;
            })
                    //get the date
            ->addColumn('date', function ($transaction) {
                $date = date("d-m-Y", strtotime($transaction->transaction_date));
                return $date;
            })
          
            ->rawColumns(['checkboxes'])
            ->make(true);
    }
}

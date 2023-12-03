<?php

namespace App\Http\Controllers\User\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Transaction;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;


class PurchasesByVendortableController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function __invoke(Request $request)
    {
        $transactions = Transaction::where('user_id', Auth::user()->id)->where('vendor_id', "!=", NULL);
        $start = Carbon::today();
        $end = Carbon::yesterday();

        $rangeType =  request()->date_option;
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

        $transactions = $transactions->get();

        foreach ($transactions as $transaction) {
            $v[] = $transaction->vendor_id;
        }
        $vendors = Vendor::whereIn('id', $v);
        // if (request()->from) {
        //     $transactions = $transactions->whereDate('transaction_date', '>=', Carbon::createFromDate(request()->from));
        // }
        // if (request()->to) {
        //     $transactions = $transactions->whereDate('transaction_date', '<=', Carbon::createFromDate(request()->to));
        // }
        if ($request->vendor_name != '') {
            $vendors->where('name', "like", "%{$request->vendor_name}%");
        } else {
            $vendors = $vendors->get();
        }

        return DataTables::of($vendors)
            ->addColumn('checkboxes', function ($vendor) {
                $action = '<input type="checkbox" name="pdr_checkbox[]" class="pdr_checkbox" value="' . $vendor->id . '" />';
                return $action;
            })
            //get the category
            ->addColumn('category', function ($vendor) {
                if ($vendor->category_id != NULL) {
                    return $vendor->categories->name;
                } else {
                    return "No Category";
                }
            })
            //get the date
            ->addColumn('date', function ($vendor)  use ($start, $end, $rangeType) {
                $date = date("d-m-Y", strtotime($vendor->getDate(Auth::user()->id, $vendor->id, $start, $end, $rangeType)));
                return $date;
            })

            //get the amount of the transactions
            ->addColumn('amount', function ($vendor) use ($start, $end, $rangeType) {
                $total = number_format((float)$vendor->transactionAmount(Auth::user()->id, $vendor->id, $start, $end, $rangeType), 2, '.', '');
                return "$" . $total;
            })
            ->rawColumns(['checkboxes'])
            ->make(true);
    }
}

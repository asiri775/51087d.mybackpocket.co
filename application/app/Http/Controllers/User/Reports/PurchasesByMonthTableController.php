<?php

namespace App\Http\Controllers\User\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Purchase;
use App\Models\Category;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;


class PurchasesByMonthTableController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function __invoke(Request $request)
    {
        //$purchases = Purchase::where('user_id', Auth::user()->id);
        $purchases = DB::table('purchases')
        ->join('transactions', 'transactions.transaction_no', '=', 'purchases.transaction_id')
        ->select('purchases.*', 'transactions.transaction_date', 'transactions.user_id')
        ->where('transactions.user_id',Auth::user()->id)
        ->get();
        // switch (request()->date_option) {
        //     case 'this_month':
        //         $start = Carbon::now()->startOfMonth();
        //         $end = Carbon::now()->endOfMonth();
        //         $purchases = $purchases->transaction->whereBetween('transaction_date', [$start, $end]);
        //         break;
        //     case 'this_year':
        //         $start = Carbon::now()->startOfYear();
        //         $end = Carbon::now()->endOfYear();
        //         $purchases = $purchases->transaction->whereBetween('transaction_date', [$start, $end]);
        //         break;
        //     default:
        //         break;
        // }
        // if (request()->from) {
        //     $purchases = $transactions->whereDate('transaction_date', '>=', Carbon::createFromDate(request()->from));
        // }
        // if(request()->to) {
        //     $purchases = $transactions->whereDate('transaction_date', '<=', Carbon::createFromDate(request()->to));
        // }

        // if ($request->vendor_id != '') {
        //     $transactions->where('vendor_id', "{$request->vendor_id}");
        // }
        // if ($request->category_id != '') {
        //     $category = $request->category_id;
        //     $transactions = $transactions->whereHas('vendor', function ($query) use ($category) {
        //         $query->where('category_id', "{$category}");
        //     });
        // }

        // $transactions->orderBy('transaction_date', 'DESC');
        // if ($request->order_no != '') {
        //     $transactions = $transactions->where('order_no', 'like', "%{$request->order_no}%")->get();
        // } else {
        //     $transactions = $transactions->get();
        // }
        return DataTables::of($purchases)
        ->addColumn('checkboxes', function ($purchase) {
            $action = '<input type="checkbox" name="pdr_checkbox[]" class="pdr_checkbox" value="'.$purchase->id.'" />';
            return $action;
        })
        // ->addColumn('transaction_date', function ($transaction) {
        //     return date('m/d/Y', strtotime($transaction->transaction_date));
        // })
        // ->addColumn('vendor_name', function ($transaction) {
        //     return $transaction->vendor->name;
        // })
        // ->addColumn('vendor_id', function ($transaction) {
        //     return $transaction->vendor->id;
        // })
        // ->addColumn('category_name', function ($transaction) {
        //     $cat = Category::where('id', $transaction->vendor->category_id)->first();
        //     return $cat->name;
        // })

        ->rawColumns(['checkboxes'])
        ->make(true);
    }
}

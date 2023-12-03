<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;


class AdminPurchaseByCategoryTableController extends Controller
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
        if ($request->category_id != '') {
            $category = $request->category_id;
            $transactions = $transactions->whereHas('vendor', function ($query) use ($category) {
                $query->where('category_id', "{$category}");
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
        ->addColumn('user_name', function ($transaction) {
            return $transaction->user->name;
        })

        ->addColumn('vendor_name', function ($transaction) {
            return $transaction->vendor->name;
        })
        ->addColumn('vendor_id', function ($transaction) {
            return $transaction->vendor->id;
        })
        ->addColumn('category_name', function ($transaction) {
            $cat = Category::where('id', $transaction->vendor->category_id)->first();
            return $cat->name;
        })

        ->rawColumns(['checkboxes'])
        ->make(true);
    }
}

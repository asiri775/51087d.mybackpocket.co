<?php

namespace App\Http\Controllers\User\Envelopes;

use App\Models\Category;
use App\Models\Vendor;
use App\Models\Envelope;
use App\Models\Transaction;



use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EnvelopeAddReceiptTableController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request)
    {
        $transactions = Transaction::where('user_id', Auth::user()->id)->where('envelope_id', '=', NULL)->where('is_hidden', '=', 0)->where('type', '=', 0);

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
            $d=explode('-',request()->from);
            $from=$d[2].'-'.$d[0].'-'.$d[1];
            if(!isset($from))
            {
               $from=Carbon::now()->startOfYear();
            }
            $transactions = $transactions->where('transaction_date', '>=', $from);
        }
        if (request()->to) {
            $d=explode('-',request()->to);
            $to=$d[2].'-'.$d[0].'-'.$d[1];
            if(!isset($to))
            {
               $to=Carbon::now()->endOfYear();
            }
            $transactions = $transactions->whereDate('transaction_date', '<=', $to);
        }
        if (request()->vendor) {
            $vendor = $request->vendor;
            $transactions = $transactions->whereHas('vendor', function ($query) use ($vendor) {
                $query->where('name', 'like', "%{$vendor}%");
            });
        }
        if (request()->vendor_email) {
            $vendor_email = $request->vendor_email;
            $transactions = $transactions->whereHas('vendor', function ($query) use ($vendor_email) {
                $query->where('email', 'like', "%{$vendor_email}%");
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
            ->addColumn('transaction_time', function ($transaction) {
                return date('h:i:s A', strtotime($transaction->transaction_date));
            })
            ->addColumn('vendor_name', function ($transaction) {
                return $transaction->vendor->name;
            })
            ->addColumn('vendor_email', function ($transaction) {
                return $transaction->vendor->email;
            })

            ->addColumn('total', function ($transaction) {
                $total = "$". number_format((float)$transaction->total, 2, '.', '') ;
                return $total;
            })

            ->rawColumns(['checkbox'])

            ->make(true);
    }
}

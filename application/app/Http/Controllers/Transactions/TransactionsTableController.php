<?php

namespace App\Http\Controllers\Transactions;

use Carbon\Carbon;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class TransactionsTableController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dateConvertion($date)
    {
        $d = explode('-', $date);
        $date = $d[2] . '-' . $d[0] . '-' . $d[1];
        return $date;
    }

    public function __invoke(Request $request)
    {
        $transactions = Transaction::where('is_archived', '=', 0)
            ->where('is_hidden', '=', 0)
            ->whereNull('bank_account_id');

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

        if (request()->from) {
            $from = $this->dateConvertion(request()->from);
            if (!isset($from)) {
                $from = Carbon::now()->startOfYear();
            }
            $transactions = $transactions->where('transaction_date', '>=', $from);
        }
        if (request()->to) {
            $to = $this->dateConvertion(request()->to);
            if (!isset($to)) {
                $to = Carbon::now()->startOfYear();
            }
            $transactions = $transactions->where('transaction_date', '<=', $to);
        }
        if (request()->vendor_email) {
            $email = $request->vendor_email;
            $transactions = $transactions->whereHas('vendor', function ($query) use ($email) {
                $query->where('email', 'like', "%{$email}%");
            });
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
            ->addColumn('transaction_date', function ($transaction) {
                return date('m-d-Y', strtotime($transaction->transaction_date));
            })
            ->addColumn('transaction_time', function ($transaction) {
                return date('h:i:s A', strtotime($transaction->transaction_date));
            })
            ->addColumn('vendor_name', function ($transaction) {
                return (isset($transaction->vendor->name)) ? $transaction->vendor->name : 'Undefined';
            })
            ->addColumn('vendor_id', function ($transaction) {
                return (isset($transaction->vendor->id)) ? $transaction->vendor->id : '';
            })
            ->addColumn('vendor_email', function ($transaction) {
                return (isset($transaction->vendor->email)) ? $transaction->vendor->email : '';
            })
            ->addColumn('total', function ($transaction) {
                return (isset($transaction->total)) ? number_format((float)($transaction->total), 2, '.', ',') : '';
            })
            ->addColumn('actions', function ($transaction) {
                $action = '
                    <div class="btn-group">
                        <a href="' . route('transactions.detail', ['transaction' => $transaction]) . '" class="btn btn-complete" data-toggle="tooltip"
                        data-placement="bottom" title="Preview"><i class="fa fa-eye"></i>
                        </a>
                        <a href="#!" onclick="modalSend(' . $transaction->id . ')" class="btn btn-success" data-toggle="modal" data-target="#send" trans_id="' . $transaction->id . '"
                        data-placement="bottom" title="Email"><i class="fa fa-envelope"></i>
                        </a>
                        <a href="' . route('transactions.mpdf', ['transaction' => $transaction->id]) . '" class="btn btn-primary" data-toggle="tooltip"
                        data-placement="bottom" title="Download"><i class="fa fa-download"></i>
                        </a>
                        <a href="#!" onclick="modalArchive(' . $transaction->id . ')" type="button" title="Archive" class="btn btn-danger" data-toggle="modal" data-target="#archive" trans_id="' . $transaction->id . '" style="color:#fff;"><i class="fa fa-archive"></i></a>
                        <a href="#!" onclick="modalHide(' . $transaction->id . ')" type="button" title="Hide" class="btn btn-danger" data-toggle="modal" data-target="#hide" trans_id="' . $transaction->id . '" style="color:#fff;"><i class="fa fa-diamond"></i></a>
                    </div>
                ';
                return $action; //$transaction->action_buttons;
            })
            ->rawColumns(['checkboxes', 'actions'])
            ->make(true);
    }
}

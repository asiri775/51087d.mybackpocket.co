<?php


namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class DashboardReceiptsTableController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request)
    {
        $transactions = Transaction::where('user_id', Auth::user()->id)->where('is_hidden', '=', 0)->where('type', '=', 0)->get();
       
        return DataTables::of($transactions)
        ->addColumn('checkboxes', function ($transaction) {
            $action = '<input type="checkbox" name="pdr_checkbox[]" class="pdr_checkbox" value="'.$transaction->id.'" />';
            return $action;
        })
        ->addColumn('transaction_date', function ($transaction) {
            return date('m-d-Y', strtotime($transaction->transaction_date));
        })
        ->addColumn('vendor_name', function ($transaction) {
            return $transaction->vendor->name;
        })

        ->addColumn('total', function ($transaction) {
            $total = "$". number_format((float)$transaction->total, 2, '.', '');
            return $total;
        })
        ->make(true);
    }
}

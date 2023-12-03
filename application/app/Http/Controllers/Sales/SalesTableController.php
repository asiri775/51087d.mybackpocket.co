<?php
namespace App\Http\Controllers\Sales;

use App\Models\Transaction;
use Carbon\Carbon;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class SalesTableController extends Controller
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

        $sales = Purchase::with(["transaction","product"]);
        switch (request()->date_option) {
            case 'yesterday':
                $sales = $sales->whereDate('created_at', '=', Carbon::now()->subDay());
                break;
            case 'today':
                $sales = $sales->whereDate('created_at', '=', Carbon::now());
                break;
            case 'this_weekdays':
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek()->subDays(2);
                $sales = $sales->whereBetween('created_at', [$start, $end]);
                break;
            case 'this_whole_week':
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();
                $sales = $sales->whereBetween('created_at', [$start, $end]);
                break;
            case 'this_month':
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
                $sales = $sales->whereBetween('created_at', [$start, $end]);
                break;
            case 'this_year':
                $start = Carbon::now()->startOfYear();
                $end = Carbon::now()->endOfYear();
                $sales = $sales->whereBetween('created_at', [$start, $end]);
                break;
            default:
                break;
        }
        if(request()->year_to_date) {
            $start = Carbon::now()->startOfYear();
            $end = Carbon::createFromDate(request()->year_to_date);
            $sales = $sales->whereBetween('created_at', [$start, $end]);
        }
        if(request()->from) {
            $from=$this->dateConvertion(request()->from);
            if(!isset($from))
            {
               $from=Carbon::now()->startOfYear();
            }
            $sales = $sales->where('created_at', '>=', $from);
        }
        if(request()->to) {
            $to=$this->dateConvertion(request()->to);
            if(!isset($to))
            {
                $to=Carbon::now()->startOfYear();
            }
            $sales = $sales->where('created_at', '<=',  $to);
        }

        if(request()->vendor_id) {
            $transactions= Transaction::getTranscationsListByvendorId(request()->vendor_id);
            $sales = $sales->whereIn('transaction_id',$transactions);
        }

        if(request()->product_id) {
            $sales = $sales->where('product_id', request()->product_id);
        }

        if(request()->price) {
            $sales = $sales->where('price', 'like', "%".request()->price."%");
        }

        if(request()->quantity) {
            $sales = $sales->where('quantity', request()->quantity);
        }

        return DataTables::of($sales)
            ->addColumn('created_at', function ($transaction) {
                return date('m-d-Y', strtotime($transaction->created_at));
            })
            ->addColumn('price', function ($transaction) {
                return (isset($transaction->price))?number_format((float)($transaction->price), 2, '.', ','):'';
            })
        ->make(true);

    }
}
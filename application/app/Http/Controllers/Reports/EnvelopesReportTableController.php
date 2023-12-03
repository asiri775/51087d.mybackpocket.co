<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;

use Carbon\Carbon;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Session;
use App\Bank_Account;
use App\Models\Envelope;
use App\Models\Category;
use App\Models\Transaction;
use App\User;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

use Illuminate\Http\Request;


class EnvelopesReportTableController extends Controller

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
        $envelopes = Envelope::query();

        switch (request()->date_option) {
            case 'yesterday':
                $envelopes = $envelopes->whereDate('envelope_date', '=', Carbon::now()->subDay());
                break;
            case 'today':
                $envelopes = $envelopes->whereDate('envelope_date', '=', Carbon::now());
                break;
            case 'this_weekdays':
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek()->subDays(2);
                $envelopes = $envelopes->whereBetween('envelope_date', [$start, $end]);
                break;
            case 'this_whole_week':
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();
                $envelopes = $envelopes->whereBetween('envelope_date', [$start, $end]);
                break;
            case 'this_month':
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
                $envelopes = $envelopes->whereBetween('envelope_date', [$start, $end]);
                break;
            case 'this_year':
                $start = Carbon::now()->startOfYear();
                $end = Carbon::now()->endOfYear();
                $envelopes = $envelopes->whereBetween('envelope_date', [$start, $end]);
                break;
            default:
                break;
        }
        if (request()->year_to_date) {
            $start = Carbon::now()->startOfYear();
            $end = Carbon::createFromDate(request()->year_to_date);
            $envelopes = $envelopes->whereBetween('envelope_date', [$start, $end]);
        }
        if (request()->from) {
            $from=$this->dateConvertion(request()->from);
            if(!isset($from))
            {
               $from=Carbon::now()->startOfYear();
            }
            $envelopes = $envelopes->where('envelope_date', '>=',  $from);
        }
        if (request()->to) {
            $to=$this->dateConvertion(request()->to);
            if(!isset($to))
            {
                $to=Carbon::now()->startOfYear();
            }
            $envelopes = $envelopes->where('envelope_date', '<=',  $to);
        }
        if ($request->user_name) {
            $name = $request->user_name;
            $userList = User::where('name', 'like',  "%{$name}%")->get();
            $envelopes->whereIn('enveloped_by', $userList);
        }

        if ($request->category_option) {
            $categoryList = array();
            $category = $request->category_option;
            $envelopes->where('category_id', $category);
        }
        
        if ($request->envelope_name != '') {
            $envelopes = $envelopes->where('name', 'like', "%{$request->envelope_name}%")->get();
        } else {
            $envelopes = $envelopes->get();
        }
        return DataTables::of($envelopes)
            ->addColumn('envelope_date', function ($envelope) {
                return date('m-d-Y', strtotime($envelope->envelope_date));
            })
            ->addColumn('user_name', function ($envelope) {

                return $envelope->getUserById($envelope->enveloped_by)->name;
            })
            ->addColumn('user_id', function ($envelope) {

                return $envelope->enveloped_by;
            })
            ->addColumn('envelope_name', function ($envelope) {

                return $envelope->name;
            })
            ->addColumn('envelope_category', function ($envelope) {
                return $envelope->getCategoryName($envelope->category_id)->name;
            })


            ->make(true);
    }
}

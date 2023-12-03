<?php

namespace App\Http\Controllers\Vendors;

use Carbon\Carbon;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Models\Calculate;

class VendorsTableController extends Controller
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
        $rangeType =  request()->date_option;
        $vendors = Vendor::where('is_hidden', '=', 0);
        switch (request()->date_option) {
            case 'yesterday':
                $vendors = $vendors->whereDate('created_at', '=', Carbon::now()->subDay());
                break;
            case 'today':
                $vendors = $vendors->whereDate('created_at', '=', Carbon::now());
                break;
            case 'this_weekdays':
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek()->subDays(2);
                $vendors = $vendors->whereBetween('created_at', [$start, $end]);
                break;
            case 'this_whole_week':
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();
                $vendors = $vendors->whereBetween('created_at', [$start, $end]);
                break;
            case 'this_month':
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
                $vendors = $vendors->whereBetween('created_at', [$start, $end]);
                break;
            case 'this_year':
                $start = Carbon::now()->startOfYear();
                $end = Carbon::now()->endOfYear();
                $vendors = $vendors->whereBetween('created_at', [$start, $end]);
                break;
            default:
                break;
        }

        if($request->from) {
            $from=$this->dateConvertion(request()->from);
            if(!isset($from))
            {
               $from=Carbon::now()->startOfYear();
            }
            $vendors = $vendors->whereDate('created_at', '>=', $from);
        }
        if($request->to) {
            $to=$this->dateConvertion(request()->to);
            if(!isset($to))
            {
                $to=Carbon::now()->startOfYear();
            }
            $vendors = $vendors->where('created_at', '<=',$to);
        }
        if($request->email) {
            $vendors = $vendors->where('email', 'LIKE', '%'.$request->email.'%');
        }
        if($request->address) {
            $vendors = $vendors->where('address', 'LIKE', '%'.$request->address.'%');
        }
        if($request->store_no) {
            $vendors = $vendors->where('store_no', 'LIKE', '%'.$request->store_no.'%');
        }
        if($request->vendor_id!=''){
            $vendors->where('id', "{$request->vendor_id}");
        }
        return DataTables::of($vendors)
            ->addColumn('checkboxes', function ($vendor) {
                $action = '<input type="checkbox" name="pdr_checkbox[]" class="pdr_checkbox" value="'.$vendor->id.'" />';
                return $action;
            })
            ->addColumn('actions', function ($vendor) {
                $action = '
                    <div class="btn-group">
                        <a href="#!" onclick="modalHide('.$vendor->id.')" type="button" title="Hide" class="btn btn-danger" data-toggle="modal" data-target="#hide" vendor_id="' . $vendor->id . '" style="color:#fff;"><i class="fa fa-diamond"></i></a>
                                 </div>
                        <a href="#!" onclick="modalDelete('.$vendor->id.')" type="button" title="Delete" class="btn btn-danger" data-toggle="modal" data-target="#delete" vendor_id="' . $vendor->id . '" style="color:#fff;"><i class="fa fa-trash"></i></a>
                                 </div>
                ';
                return $action;
            })
          

         
            ->rawColumns(['checkboxes','actions'])
            ->make(true);
    }
}

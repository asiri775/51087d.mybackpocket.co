<?php

namespace App\Http\Controllers\Reports;

use App\Models\Category;
use App\Models\Vendor;
use App\Models\Envelope;



use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminEnvelopesReportTableController extends Controller
{

    public function dateConvertion($date)
    {
        $d=explode('-',$date);
        $date=$d[2].'-'.$d[0].'-'.$d[1];
        return $date;
    }
    public function __invoke(Request $request)
    {
        $envelopes = Envelope::orderBy('id', 'ASC');
        switch (request()->date_option) {
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

        if (request()->from) {
            $from=$this->dateConvertion(request()->from);
            if(!isset($from))
            {
               $from=Carbon::now()->startOfYear();
            }
            $envelopes = $envelopes->where('envelope_date', '>=',$from);
        }
        if (request()->to) {
            $to=$this->dateConvertion(request()->to);
            if(!isset($to))
            {
                $to=Carbon::now()->startOfYear();
            }
            $envelopes = $envelopes->where('envelope_date', '<=', $to);
        }

        if (request()->search_user) {
            $user = $request->search_user;
            $envelopes = $envelopes->whereHas('user', function ($query) use ($user) {
                $query->where('name', 'like', "%{$user}%");
            });
        }

        if ($request->vendor_id != '') {
            $envelopes->where('vendor_id', "{$request->vendor_id}");
        } else {
            $envelopes = $envelopes->get();
        }
        return DataTables::of($envelopes)
            ->addColumn('checkboxes', function ($envelope) {
                $action = '<input type="checkbox" name="pdr_checkbox[]" class="pdr_checkbox" value="' . $envelope->id . '" />';
                return $action;
            })
            ->addColumn('envelope_date', function ($envelope) {
                return date('m/d/Y', strtotime($envelope->envelope_date));
            })
            ->addColumn('vendor_name', function ($envelope) {
                if ($envelope->vendor_id != null) {
                    $venName = $envelope->vendor->name;
                } else {
                    $venName = "No vendor";
                }
                return $venName;
            })
            ->addColumn('vendor_id', function ($envelope) {
                if ($envelope->vendor_id != null) {
                    $venId = $envelope->vendor->id;
                } else {
                    $venId = "No vendor";
                }
                return $venId;
            })
            ->addColumn('envelope_category', function ($envelope) {
                return $envelope->category->name;
            })
            ->addColumn('user_name', function ($envelope) {
                return $envelope->user->name;
            })
            ->addColumn('amount', function ($envelope) {
                $amount = $envelope->EnvelopAmount($envelope->id);
                return $amount;
            })
            ->rawColumns(['checkboxes'])
            ->make(true);
    }
}

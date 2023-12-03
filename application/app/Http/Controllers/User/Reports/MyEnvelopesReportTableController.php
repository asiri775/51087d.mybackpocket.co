<?php
namespace App\Http\Controllers\User\Reports;

use App\Models\Category;
use App\Models\Vendor;
use App\Models\Envelope;



use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MyEnvelopesReportTableController extends Controller
{
    public function __invoke(Request $request)
    {
        $envelopes = Envelope::where('enveloped_by', Auth::user()->id);
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
            $envelopes = $envelopes->whereDate('envelope_date', '>=', Carbon::createFromDate(request()->from));
        }
        if (request()->to) {
            $envelopes = $envelopes->whereDate('envelope_date', '<=', Carbon::createFromDate(request()->to));
        }
        if ($request->envelope_name != '') {
            $envelopes->where('name', 'like', "%{$request->envelope_name}%");
        }
        if ($request->vendor_id != '') {
            $envelopes->where('vendor_id', "{$request->vendor_id}");
        } 
        if ($request->category_id != '') {
            $envelopes->where('category_id', "{$request->category_id}");
        } else {
            $envelopes = $envelopes->get();
        }
        return DataTables::of($envelopes)
            ->addColumn('checkboxes', function ($envelope) {
                $action = '<input type="checkbox" name="pdr_checkbox[]" class="pdr_checkbox" value="' . $envelope->id . '" />';
                return $action;
            })
            ->addColumn('envelope_date', function ($envelope) {
                return date('m-d-Y', strtotime($envelope->envelope_date));
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
            ->addColumn('amount', function ($envelope) {
                $amount = $envelope->EnvelopAmount($envelope->id);
                $total = "$". number_format((float)$amount, 2, '.', '');
                return $total;
            })
         
            ->rawColumns(['checkboxes'])
            ->make(true);
    }
}

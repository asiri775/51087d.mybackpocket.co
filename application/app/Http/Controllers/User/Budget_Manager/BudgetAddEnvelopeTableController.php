<?php
namespace App\Http\Controllers\User\Budget_Manager;

use App\Models\Category;
use App\Models\Vendor;
use App\Models\Budget;
use App\Models\Envelope;



use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BudgetAddEnvelopeTableController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request)
    {
        $envelopes = Envelope::where('enveloped_by', Auth::user()->id)->where('budget_id', '=', NULL);
        switch (request()->date_option) {
            case 'today':
                $envelopes = $envelopes->whereDate('envelope_date', '=', Carbon::now());
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
        if (request()->from) {
            $envelopes = $envelopes->whereDate('envelope_date', '>=', Carbon::createFromDate(request()->from));
        }
        if (request()->to) {
            $envelopes = $envelopes->whereDate('envelope_date', '<=', Carbon::createFromDate(request()->to));
        }
        if ($request->envelope_name != '') {
            $envelopes = $envelopes->where('name', 'like', "%{$request->envelope_name}%");
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
                $amount = number_format((float)$envelope->EnvelopAmount($envelope->id), 2, '.', '') ;
                return $amount;
            })

            ->rawColumns(['checkboxes'])
            ->make(true);
    }
}

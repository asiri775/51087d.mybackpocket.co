<?php

namespace App\Http\Controllers\User\Envelopes;

use App\Models\Category;
use App\Models\Vendor;
use App\Models\Envelope;



use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\EnvelopeTransaction;
use App\Models\Transaction;

class EnvelopeTableController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request)
    {
        $envelopes = Envelope::where('enveloped_by', Auth::user()->id);
        switch (request()->date_option) {
            case 'today':
                $envelopes = $envelopes->whereDate('envelope_date', '=', Carbon::now());
                break;
            case 'this_whole_week':
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();
                $envelopes = $envelopes->whereBetween('envelope_date', [$start, $end]);
                break;
            case 'this_weekdays':
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek()->subDays(2);
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
            $envelopes->where('name', 'like', "%{$request->envelope_name}%");
        }
        if ($request->category_id != '') {
            $envelopes->where('category_id', "{$request->category_id}");
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
                $amount = $envelope->EnvelopAmount($envelope->id);
                $total = "$" . number_format((float)$amount, 2, '.', '');
                return $total;
            })
            ->addColumn('actions', function ($envelope) {
                $action = '
                <div class="btn-group">
                <a class="btn btn-primary"
                href="' . route('user.envelope.add.reciepts', ['Envelope' => $envelope]) . '"
                id="sessionSave" data-toggle="tooltip" data-placement="bottom"
                title="Add reciept" name="add"><i class="fa fa-plus"
                    aria-hidden="true"></i></a>
                 <a href="' . route('user.preview.envelope', ['Envelope' => $envelope]) . '"
                    class="btn btn-complete" data-toggle="tooltip"
                    data-placement="bottom" title="Edit"><i class="fa fa-edit"></i>
                </a>
                <a  href="' . route('user.delete.envelope', ['Envelope' => $envelope]) . '"  class="btn btn-danger" data-toggle="tooltip" 
                data-placement="bottom" title="Delete"><i class="fa fa-trash-o"></i></a>
                </div>
            ';
                return $action; //$transaction->action_buttons;
            })
            ->rawColumns(['checkboxes', 'actions'])
            ->make(true);
    }
}

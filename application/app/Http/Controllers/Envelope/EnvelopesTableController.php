<?php

namespace App\Http\Controllers\Envelope;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Models\Envelope;

class EnvelopesTableController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request)
    {

        $envelopes = Envelope::query();
        switch (request()->date_option) {
            case 'yesterday':
                $envelopes = $envelopes->whereDate('envelop_date', '=', Carbon::now()->subDay());
                break;
            case 'today':
                $envelopes = $envelopes->whereDate('envelop_date', '=', Carbon::now());
                break;
            case 'this_weekdays':
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek()->subDays(2);
                $envelopes = $envelopes->whereBetween('envelop_date', [$start, $end]);
                break;
            case 'this_whole_week':
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();
                $envelopes = $envelopes->whereBetween('envelop_date', [$start, $end]);
                break;
            case 'this_month':
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
                $envelopes = $envelopes->whereBetween('envelop_date', [$start, $end]);
                break;
            case 'this_year':
                $start = Carbon::now()->startOfYear();
                $end = Carbon::now()->endOfYear();
                $envelopes = $envelopes->whereBetween('envelop_date', [$start, $end]);
                break;
            default:
                break;
        }
        if (request()->year_to_date) {
            $start = Carbon::now()->startOfYear();
            $end = Carbon::createFromDate(request()->year_to_date);
            $envelopes = $envelopes->whereBetween('envelop_date', [$start, $end]);
        }
        if (request()->from) {
            $envelopes = $envelopes->whereDate('envelop_date', '>=', Carbon::createFromDate(request()->from));
        }
        if (request()->to) {
            $envelopes = $envelopes->whereDate('envelop_date', '<=', Carbon::createFromDate(request()->to));
        }
        if ($request->vendor_name) {
            $name = $request->vendor_name;
            $envelopes = $envelopes->whereHas('vendor', function ($query) use ($name) {
                $query->where('name', 'like', "%{$name}%");
            });
        }
        if ($request->order_no != '') {
            $envelopes = $envelopes->where('order_no', 'like', "%{$request->order_no}%")->get();
        } else {
            $envelopes = $envelopes->get();
        }
        return DataTables::of($envelopes)
            ->addColumn('checkbox', function ($envelope) {
                $select = '<input type="checkbox" name="checkbox[]" value="' . $envelope->id . '" id="checkbox_' . $envelope->id . ' checked">';
                return $select;

            })
            ->addColumn('envelop_date', function ($envelope) {
                return date('m-d-Y', strtotime($envelope->envelop_date));

            })
//            ->addColumn('envelop_time', function (envelope) {
//                return date('h:i:s A', strtotime($envelope->envelop_date));
//            })
            ->addColumn('vendor_name', function ($envelope) {
                return $envelope->vendor != null ? $envelope->vendor->name : '-';
            })
            ->addColumn('vendor_email', function ($envelopes) {
                return $envelopes->vendor != null ? $envelopes->vendor->email : '-';
            })
            ->addColumn('actions', function ($envelope) {
                $action = '
                    <div class="btn-group">
                        <a href="' . route('transactions.detail', ['transaction' => $envelope]) . '" class="btn btn-complete">Complete
                        </a>
                        <a href="#!" class="btn btn-success">Download
                        </a>
                        <a href="' . route('transactions.mpdf', ['transaction' => $envelope->id]) . '" class="btn btn-primary">Print
                        </a>
                        <a href="' . route('transactions.mpdf', ['transaction' => $envelope->id]) . '" class="btn btn-primary">Preview
                        </a>
                    </div>
                ';
                return $action; //$transaction->action_buttons;
            })
            ->rawColumns(['checkbox', 'actions'])
            ->make(true);
    }
}

<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Envelope;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class DashboardEnvelopesTableController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request)
    {
        $envelopes = Envelope::where('enveloped_by', Auth::user()->id)->get();
      
        return DataTables::of($envelopes)
           
            ->addColumn('items', function ($envelope) {
                $count =  $envelope->envCount($envelope->id);

                return $count;
            })
            ->make(true);
    }
}

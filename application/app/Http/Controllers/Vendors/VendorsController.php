<?php

namespace App\Http\Controllers\Vendors;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddVendorRequest;
use App\Http\Requests\EditVendorRequest;
use App\Models\Calculate;
use App\Models\Transaction;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Session;

class VendorsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $vendors = Vendor::all();
        return view('admin.vendors.list', compact('vendors'));
    }


    public function add()
    {
        return view('admin.vendors.add');
    }

    public function deleteVendor(Request $request)
    {

        $vendor = Vendor::where('id', $request->vendor_id)->first();
        $count = Calculate::vendorCount($vendor->id);

        if ($count != 0) {
            Session::flash('error', 'You are unable to delete the vendor.');
        } else {
            $vendor->delete();
            Session::flash('success', 'You have successfully deleted the vendor.');
        }

        return redirect('/admin/vendors');
    }

    public function storeVendor(AddVendorRequest $request)
    {
        $request->storeVendor();
        Session::flash('success', 'You have successfully created the vendor.');
        return redirect('/admin/vendors');
    }

    public function editVendor(Vendor $vendor)
    {
        return view('admin.vendors.edit_vendor_details', compact('vendor'));
    }

    public function editVendorPost(EditVendorRequest $request, $vendor)
    {
        $request->editVendor($vendor);
        return back()->with('success', 'Vendor is updated successfully.');
    }

    
    public function show(Vendor $vendor)
    {
        $today = date('Y-m-d');
        $trans = Transaction::select('total')->where('vendor_id', $vendor->id)->where('transaction_date', 'LIKE', "%{$today}%");
        $total = $trans->sum('total');
        $count = $trans->count();
        return view('admin.vendors.show', compact('vendor', 'total', 'count'));
    }

    public function search()
    {
        if (request()->search === '' || request()->search === null) return [];
        return Vendor::where('name', 'LIKE', request()->search . '%')->get();
    }

    public function week()
    {
        $first = Carbon::now()->startOfWeek();
        $today = Carbon::now();
        return Vendor::whereBetween('created_at', [$first, $today])->get();
    }

    public function month()
    {
        $first = Carbon::now()->startOfMonth();
        $today = Carbon::now();
        return Vendor::whereBetween('created_at', [$first, $today])->get();
    }

    public function recentVendors()
    {
        return Vendor::limit(request()->get('length'))->get();
    }

    public function selectDate(Request $request)
    {
        $transactions = Transaction::select('total')->where('vendor_id', $request->vendor_id)->get();
        $today = date('Y-m-d');
        $monday = strtotime("last monday");
        $monday = date('w', $monday) == date('w') ? $monday + 7 * 86400 : $monday;
        $monday = strtotime(date("Y-m-d H:i:s", $monday) . " -1 day");
        $sunday = strtotime(date("Y-m-d H:i:s", $monday) . " +7 days");
        $this_week_sd = date("Y-m-d", $monday);
        $this_week_ed = date("Y-m-d", $sunday);
        switch ($request->filter) {
            case 'this_week':
                $trans = $transactions->whereBetween('transaction_date', [$this_week_sd, $this_week_ed]);
                $total =  $trans->sum('total');
                $count =  $trans->count();
                break;
            case 'today':
                $trans = $transactions->where('transaction_date', 'LIKE', "%{$today}%");
                $total = $trans->sum('total');
                $count = $trans->count();
                break;
            default:
                $total = 0;
                $count = 0;
                break;
        }
        $data = ['total' => number_format($total, 2, '.', ''), 'count' => $count];
        return $data;
    }

    public function hide(Request $request)
    {
        Vendor::where('id', $request->vendor_id)->update(['is_hidden' => 1]);
        Session::flash('success', 'You have successfully hide product id # ' . $request->vendor_id);
        return redirect()->back();
    }

    public function hideAll(Request $request)
    {
        $ids = $request->vendor_ids;
        if (is_array($ids)) {
            //for multiple Transactions
            foreach ($ids as $id) {
                Vendor::where('id', $id)->update(['is_hidden' => 1]);
            }
        }

        Session::flash('success', 'You have successfully hide vendors.');
        return redirect()->back();
    }

    public function visible()
    {
        DB::table('vendors')->where(['is_hidden' => 1])->update(['is_hidden' => 0]);
        Session::flash('success', 'Vendors made visible successfully.');
        return redirect('/admin/vendors');
    }

    public function getAllVendors()
    {
       $vend='<option value="">Select Vendor</option>'; 
       foreach (Vendor::orderBy('name', 'ASC')->get() as $vendor) {
           $vend.='<option value="'.$vendor->id.'">'.$vendor->name.'</option>';
       }

       return $vend; 
    }
}

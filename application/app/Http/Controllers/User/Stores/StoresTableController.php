<?php


namespace App\Http\Controllers\User\Stores;

use App\Models\Vendor;
use App\User;

use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoresTableController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request)
    {
        $vendors = Vendor::where('is_hidden', '=', 0);
        if (request()->phone) {
            $phone = $request->phone;
            $vendors = $vendors->where('phone', 'like', "%{$phone}%");
        }
        if (request()->branch_id) {
            $branch_id = $request->branch_id;
            $vendors = $vendors->where('store_no', 'like', "%{$branch_id}%");
        }
        if (request()->city) {
            $city = $request->city;
            $vendors = $vendors->where('city', 'like', "%{$city}%");
        }
        if (request()->province) {
            $province = $request->province;
            $vendors = $vendors->where('state', 'like', "%{$province}%");
        }
        if (request()->zip) {
            $zip = $request->zip;
            $vendors = $vendors->where('zip_code', 'like', "%{$zip}%");
        }
        if (request()->address) {
            $address = $request->address;
            $vendors = $vendors->where('address', 'like', "%{$address}%");
        }
        if (request()->vendor_name) {
            $vendor_name = $request->vendor_name;
            $vendors = $vendors->where('name', 'like', "%{$vendor_name}%");
        }
            $vendors = $vendors->get();

        return DataTables::of($vendors)
            ->addColumn('checkboxes', function ($vendors) {
                $action = '<input type="checkbox" name="pdr_checkbox[]" class="pdr_checkbox" value="' . $vendors->id . '" />';
                return $action;
            })

            ->rawColumns(['checkboxes'])
            ->make(true);
    }
}

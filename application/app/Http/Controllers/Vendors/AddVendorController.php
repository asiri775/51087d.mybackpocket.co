<?php
namespace App\Http\Controllers\Vendors;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class AddVendorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
       echo '<pre>';
       print_r('ssss');
       die();
        return view('admin.vendors.list', compact('vendors'));
    }
}

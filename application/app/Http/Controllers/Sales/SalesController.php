<?php
namespace App\Http\Controllers\Sales;

use App\Models\Purchase;
use App\Http\Controllers\Controller;
use App\Models\Vendor;

class SalesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $vendors = Vendor::all();
        return view('admin.sales.list', compact('vendors'));
    }


    public function topSales()
    {
        return Purchase::with(['product' => function($query){
            $query->select('id', 'name', 'created_at');
        }, 'transaction' => function($query){
            $query->select('id', 'transaction_no');
        }])->limit(request()->get('length'))->orderBy('price', 'desc')->get();
    }
}

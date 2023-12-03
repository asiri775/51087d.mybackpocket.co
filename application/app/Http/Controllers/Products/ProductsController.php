<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;

class ProductsController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(){
        $products = Product::all();
        $vendors = Vendor::all();
        return view('admin.products.list', compact('products', 'vendors'));
    }

    public function show(Product $product){
        return view('admin.products.show', compact('product'));
    }

    public function hide(Request $request) {
        Product::where('id',  $request->product_id)->update(['is_hidden' => 1]);
        Session::flash('success', 'You have successfully hide product id # '. $request->product_id);
        return redirect()->back();
    }

    public function hideAll(Request $request)
    {
        $ids = $request->product_ids;
        if (is_array($ids)) {
            //for multiple Transactions
            foreach ($ids as $id) {
                Product::where('id', $id)->update(['is_hidden' => 1]);
            }
        }
        Session::flash('success', 'You have successfully hide products.');
        return redirect()->back();
    }

    public function productVisible()
    {  
        DB::table('products')->where(['is_hidden' => 1])->update(['is_hidden' => 0]);
        Session::flash('success', 'Products made visible successfully.');
        return redirect('/admin/products');
    }
}

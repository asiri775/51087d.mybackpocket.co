<?php

namespace App\Http\Controllers\Products;

use Carbon\Carbon;
use App\Models\Product;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Models\Vendor;

class ProductsTableController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function dateConvertion($date)
    {
        $d=explode('-',$date);
        $date=$d[2].'-'.$d[0].'-'.$d[1];
        return $date;
    }
    public function __invoke(Request $request)
    {
        $products = Product::where('is_hidden', '=', 0);
        if($request->vendor_id!=''){
            $products = $products->where('vendor_id', "{$request->vendor_id}");
        } 
        if($request->product_id!='') {
            $products = $products->where('id', "{$request->product_id}");
        }
        if($request->sku) {
            $products = $products->where('sku', 'LIKE', '%'.$request->sku.'%');
        }
        switch (request()->date_option) {
            case 'yesterday':
                $products = $products->whereDate('created_at', '=', Carbon::now()->subDay());
                break;
            case 'today':
                $products = $products->whereDate('created_at', '=', Carbon::now());
                break;
            case 'this_weekdays':
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek()->subDays(2);
                $products = $products->whereBetween('created_at', [$start, $end]);
                break;
            case 'this_whole_week':
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();
                $products = $products->whereBetween('created_at', [$start, $end]);
                break;
            case 'this_month':
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
                $products = $products->whereBetween('created_at', [$start, $end]);
                break;
            case 'this_year':
                $start = Carbon::now()->startOfYear();
                $end = Carbon::now()->endOfYear();
                $products = $products->whereBetween('created_at', [$start, $end]);
                break;
            default:
                break;
        }

        if(request()->from) {
            $from=$this->dateConvertion(request()->from);
            if(!isset($from))
            {
               $from=Carbon::now()->startOfYear();
            }
            $products = $products->where('created_at', '>=', $from);
        }
        if(request()->to) {
            $to=$this->dateConvertion(request()->to);
            if(!isset($to))
            {
                $to=Carbon::now()->startOfYear();
            }
            $products = $products->where('created_at', '<=', $to);
        }
     
        return DataTables::of($products->get())
            ->addColumn('checkboxes', function ($product) {
                $action = '<input type="checkbox" name="pdr_checkbox[]" class="pdr_checkbox" value="'.$product->id.'" />';
                return $action;
            })
            ->addColumn('vendor', function ($product) {
                return (isset($product->vendor->name))?$product->vendor->name:'Undefined';
            })
            ->addColumn('vendor_id', function ($product) {
                return (isset($product->vendor->id))?$product->vendor->id:'';
            })
            ->addColumn('price', function ($product) {  
                return (isset($product->price))?number_format((float)($product->price), 2, '.', ','):'';
            })
            ->addColumn('created', function ($product) {  
                $created=Carbon::parse($product->created)->format('m-d-Y');
                return $created;
            })
            
            
//            ->addColumn('actions', function ($product) {
//                $action = '
//                    <div class="btn-group">
//                        <a href="'. route('products.detail', ['product' => $product]) .'" class="btn btn-complete"><i class="fa fa-eye"></i>
//                        </a>
//                        <a href="#!" class="btn btn-success"><i class="fa fa-envelope"></i>
//                        </a>
//                    </div>
//                ';
//                return $action;
//            })
            ->addColumn('actions', function ($product) {
                $action = '
                    <div class="btn-group">
                        <a href="#!" onclick="modalHide('.$product->id.')" type="button" title="Hide" class="btn btn-danger" data-toggle="modal" data-target="#hide" product_id="' . $product->id . '" style="color:#fff;"><i class="fa fa-diamond"></i></a>
                    </div>
                ';
                return $action;
            })
            ->rawColumns(['checkboxes','actions'])
            ->make(true);
    }
}

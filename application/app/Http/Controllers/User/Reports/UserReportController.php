<?php

namespace App\Http\Controllers\User\Reports;

use App\Models\Category;
use App\Models\Vendor;
use App\Models\Envelope;
use App\Models\Transaction;
use App\Models\Budget;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\VendorAddToFavourite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class UserReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('user.reports.index');
    }


    public function purchaseByVendor($time)
    {
        $vendors = Vendor::all();
        $categories = Category::all();
        $favourites = VendorAddToFavourite::where('user_id', Auth::user()->id)->get();
        return view('user.reports.purchasesByVendor', compact('time', 'vendors', 'categories', 'favourites'));
    }

    //display store detail page
    public function showStore($id)
    {
        $vendor = Vendor::where('id', $id)->first();
        //get transactions amount
        $transactions = Transaction::where('user_id', Auth::user()->id)->where('vendor_id', $vendor->id)->get();
        $total = 0;
        foreach($transactions as $transaction)
        {
            $total = $total + $transaction->total;
        }
        $vendors = Vendor::all();
        $categories = Category::all();
        return view('user.reports.vendor_detail', compact('id', 'vendors', 'vendor','categories', 'transactions', 'total'));
    }

    //add vendor to favourites
    public function addToFavourites(Request $request)
    {
        if($request->status == 1)
        {
            $fav= new VendorAddToFavourite();
            $fav->user_id =  $request->user_id;
            $fav->vendor_id =  $request->vendor_id;
            $fav->save();
            return response()->json(['success'=>'Status change successfully.']);
        }
        elseif($request->status == 0)
        {
            $fav = VendorAddToFavourite::where('vendor_id', $request->vendor_id)->where('user_id', $request->user_id)->first()->delete();
            return response()->json(['error'=>'Status change successfully.']);
        }
    }
    public function purchaseByCategory($time)
    {
        $vendors = Vendor::all();
        $categories = Category::all();
        return view('user.reports.purchasesByCategory', compact('time', 'vendors', 'categories'));
    }

    public function purchaseByMonth(Request $request)
    {
        $time = $request->route('time');
        $list=[];
        $categories = DB::table('categories')
            ->leftJoin('vendors', 'categories.id', '=', 'vendors.category_id')
            ->select('vendors.category_id')
            ->where('vendors.category_id', '!=', '')
            ->distinct()
            ->get();
        foreach ($categories as $category) {
            $list[] = $category->category_id;
        }
        $tranactionsLast = Transaction::where('user_id', Auth::user()->id)->orderBy('transaction_date', 'DESC')->first();
        $tranactionsFirst = Transaction::where('user_id', Auth::user()->id)->orderBy('transaction_date', 'ASC')->first();
        // echo '<pre>';    
        // print_r($tranactionsFirst);die;
        $categories = Category::whereIn('id', $list)->get();
        if ($time == "all_time") {
            $year = $request->query('year');
            if ($year == 'all') {
                $start_year = date('Y', strtotime($tranactionsFirst->transaction_date));
                $end_year = date('Y', strtotime($tranactionsLast->transaction_date));
                $y = $start_year;
                for ($y = $start_year; $y <= $end_year; $y++) {
                    $monthlyPurchaseEachYear[$y] = $this->getMonthlyEachYearData($y, $categories);
                }
            } else {
                $start_year = $year;
                $end_year = $year;
                $monthlyPurchaseEachYear[$year] = $this->getMonthlyEachYearData($year, $categories);
            }


            // echo '<pre>';    govi
            // print_r($monthlyPurchaseEachYear);die;

            return view('user.reports.purchasesByMonthAll', compact('time', 'monthlyPurchaseEachYear', 'categories', 'time', 'start_year', 'end_year'));
        } else if ($time == "this_year") {
            $year = date("Y");
            //to get start date of previous year
            $st = date("Y-m-d", strtotime("January 1st, {$year}"));
            $et = date("Y-m-d", strtotime("December 31st, {$year}"));
            $start_date = date('Y-m-d', strtotime($st));
            $end_date = date('Y-m-d', strtotime($et));
            $monthfrom = array();
            $monthto = array();
            for ($date = $start_date; $date <= $end_date; $date = date('Y-m-d', strtotime($date . ' +1 month'))) {
                $from = date("Y-m-d", strtotime(date("Y-m-d", strtotime($date)) . ", first day of this month"));
                $to = date("Y-m-d", strtotime(date("Y-m-d", strtotime($date)) . ", last day of this month"));
                array_push($monthfrom, $from);
                array_push($monthto, $to);
            }

            $monthlyPurchaseData = [];
            foreach ($categories as $key => $categorie) {
                $total = 0.00;
                for ($i = 0; $i < 12; $i++) {
                    $monthlyPurchaseData[$categorie['id']][] = $this->getMonthlyPurchaseData($monthfrom[$i], $monthto[$i], $categorie['id']);
                }
            }

            for ($i = 0; $i < 12; $i++) {
                $total = 0.00;
                foreach ($categories as $key => $categorie) {
                    $values = $this->getMonthlyPurchaseData($monthfrom[$i], $monthto[$i], $categorie['id']);
                    $total = $values['grandTotal'] + $total;
                }
                $monthlyPurchaseTotal[$i] = $total;
            }

            return view('user.reports.purchasesByMonth', compact('time', 'monthlyPurchaseData', 'monthlyPurchaseTotal', 'categories', 'time'));
        } else {

            $from = date("Y-m-d", strtotime(date("Y-m-d", strtotime('2021-02-03')) . ", first day of this month"));
            $to = date("Y-m-d", strtotime(date("Y-m-d", strtotime('2021-02-03')) . ", last day of this month"));
            $monthlyPurchaseData = [];
            $total = 0.00;
            foreach ($categories as $key => $categorie) {
                $monthlyPurchaseData[$categorie['id']] = $this->getMonthlyPurchaseData($from, $to, $categorie['id']);
                $values = $this->getMonthlyPurchaseData($from, $to, $categorie['id']);
                $total = $values['grandTotal'] + $total;
            }
            return view('user.reports.purchasesByMonthThis', compact('time', 'monthlyPurchaseData', 'categories', 'time', 'total'));
        }
    }

    public function getMonthlyEachYearData($y, $categories)
    {
        //to get start date of previous year
        $st = date("Y-m-d", strtotime("January 1st, {$y}"));
        $et = date("Y-m-d", strtotime("December 31st, {$y}"));
        $start_date = date('Y-m-d', strtotime($st));
        $end_date = date('Y-m-d', strtotime($et));
        $monthfrom = array();
        $monthto = array();
        for ($date = $start_date; $date <= $end_date; $date = date('Y-m-d', strtotime($date . ' +1 month'))) {
            $from = date("Y-m-d", strtotime(date("Y-m-d", strtotime($date)) . ", first day of this month"));
            $to = date("Y-m-d", strtotime(date("Y-m-d", strtotime($date)) . ", last day of this month"));
            array_push($monthfrom, $from);
            array_push($monthto, $to);
        }

        $monthlyPurchaseData = [];
        foreach ($categories as $key => $categorie) {
            for ($i = 0; $i < 12; $i++) {
                $monthlyPurchaseData[$categorie['id']][$i] = $this->getMonthlyPurchaseData($monthfrom[$i], $monthto[$i], $categorie['id']);
            }
        }

        $monthlyPurchaseTotal = [];
        for ($i = 0; $i < 12; $i++) {
            $total = 0.00;
            foreach ($categories as $key => $categorie) {
                $values = $this->getMonthlyPurchaseData($monthfrom[$i], $monthto[$i], $categorie['id']);
                $total = $values['grandTotal'] + $total;
            }
            $monthlyPurchaseTotal[$i] = $total;
        }


        return  ['purchaseTotal' => $monthlyPurchaseTotal, 'monthlyPurchaseData' => $monthlyPurchaseData];
    }

    public function getMonthlyPurchaseData($txt_from_date, $txt_to_date, $cat_id)
    {
        $transactions = DB::table('transactions')
            ->leftJoin('vendors', 'vendors.id', '=', 'transactions.vendor_id')
            ->select('*')
            ->when($txt_from_date, function ($purchase) use ($txt_from_date) {
                return $purchase->where('transactions.transaction_date', '>=', '' . $txt_from_date . '');
            })
            ->when($txt_to_date, function ($purchase) use ($txt_to_date) {
                return $purchase->where('transactions.transaction_date', '<=', '' . $txt_to_date . '');
            })
            ->where('transactions.user_id', Auth::user()->id)
            ->where('vendors.category_id', $cat_id)
            ->get();
        $grandTotal = 0.00;
        if ($transactions) {
            foreach ($transactions as $key => $transaction) {
                $total = (float)$transaction->total;
                $grandTotal = $grandTotal + $total;
            }
        }
        return  ['grandTotal' => $grandTotal];
    }

    public function myEnvelopesReports($time)
    {
        $categories = Category::orderBy('id', 'ASC')->get();
        $vendors = Vendor::orderBy('id', 'ASC')->get();
        return view('user.reports.myEnvelopesReports', compact('categories', 'vendors', 'time'));
    }

    public function myBudgetsReports($time)
    {
        $categories = Category::orderBy('id', 'ASC')->get();
        $vendors = Vendor::orderBy('id', 'ASC')->get();
        $budgets = Budget::where('created_by', Auth::user()->id)->get();
        return view('user.reports.myBudgetsReports', compact('categories', 'vendors', 'budgets', 'time'));
    }
}

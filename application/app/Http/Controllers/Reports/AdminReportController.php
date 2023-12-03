<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;

use App\Models\Category;
use App\Models\Vendor;
use App\Models\Envelope;
use App\Models\Transaction;
use App\Models\Purchase;
use App\Models\Budget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index() {
        return view('admin.adminReports.index');
    }


    public function adminPurchaseByVendor($time)
    {
        $vendors = Vendor::all();
        return view('admin.adminReports.purchasesByVendor', compact('time', 'vendors'));
    }
    public function adminPurchaseByCategory($time)
    {
        $vendors = Vendor::all();
        $categories = Category::all();
        return view('admin.adminReports.purchasesByCategory', compact('time', 'vendors', 'categories'));
    }

    public function adminPurchaseByMonth($time)
    {
        $purchases = DB::table('purchases')
        ->join('transactions', 'transactions.transaction_no', '=', 'purchases.transaction_id')
        ->select('purchases.*', 'transactions.transaction_date', 'transactions.user_id')
        ->where('transactions.user_id',Auth::user()->id)
        ->get();
        // $vendors = Vendor::all();
        // $categories = Category::all();
        return view('admin.adminReports.purchasesByMonth', compact('time', 'vendors', 'categories'));
    }

    public function adminEnvelopesReports($time)
    {
        $categories = Category::orderBy('id', 'ASC')->get();
        $vendors = Vendor::orderBy('id', 'ASC')->get();
        return view('admin.adminReports.envelopesReports', compact('categories', 'vendors', 'time'));
    }

    public function adminBudgetsReports($time)
    {
        $categories = Category::orderBy('id', 'ASC')->get();
        $vendors = Vendor::orderBy('id', 'ASC')->get();
        $budgets = Budget::where('created_by', Auth::user()->id)->get();
        return view('admin.adminReports.budgetsReports', compact('categories', 'vendors', 'budgets', 'time'));
    }
}

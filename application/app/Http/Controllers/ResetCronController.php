<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Session;

class ResetCronController extends Controller
{

    public function index()
    {
        DB::table('products')->truncate();
        DB::table('transactions')->truncate();
        DB::table('purchases')->truncate();
        DB::table('vendors')->truncate();

    }

    public function resetDatabase()
    {
        DB::table('products')->truncate();
        DB::table('transactions')->truncate();
        DB::table('purchases')->truncate();
        DB::table('vendors')->truncate();
        Session::flash('success', 'Database successfully reset');
        return redirect()->back();
    }

}

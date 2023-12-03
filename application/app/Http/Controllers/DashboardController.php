<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vendor;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $vendors = Vendor::all();
        return view('admin.dashboard', compact('vendors'));
    }
    public function userIndex()
    {
        return view('user.dashboard');
    }
}

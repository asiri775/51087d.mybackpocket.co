<?php

namespace App\Http\Controllers\User\Stores;

use App\User;
use App\Models\Vendor;


use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoresController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(){
        $myvendors = User::find( Auth::user()->id)->vendors->unique();
     
        $vendors = Vendor::orderBy('id', 'DESC')->get();
        return view('user.stores', compact('vendors'));
    }
}

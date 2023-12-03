<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Setting;
use Illuminate\Http\Request;

class UserSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $setting = Setting::where('user_id', Auth::user()->id)->first();
        return view('user.setting', compact('setting'));
    }

    public function edit(Request $request){

        $setting = Setting::where('user_id',  Auth::user()->id)->first();

        if(isset($setting))
        {
            $setting->user_id = Auth::user()->id;
            $setting->IMAP_HOST = $request->IMAP_HOST;
            $setting->IMAP_PORT = $request->IMAP_PORT;
            $setting->IMAP_ENCRYPTION = $request->IMAP_ENCRYPTION;
            if($request->IMAP_VALIDATE_CERT == "TURE")
            {
                $setting->IMAP_VALIDATE_CERT = 1;
            }
            elseif($request->IMAP_VALIDATE_CERT == "FALSE")
            {
                $setting->IMAP_VALIDATE_CERT = 0;
            }
            $setting->IMAP_USERNAME = $request->IMAP_USERNAME;
            $setting->IMAP_PASSWORD = $request->IMAP_PASSWORD;
            $setting->IMAP_DEFAULT_ACCOUNT = $request->IMAP_DEFAULT_ACCOUNT;
            $setting->IMAP_PROTOCOL = $request->IMAP_PROTOCOL;
            $setting->MAIL_DRIVER = $request->MAIL_DRIVER;
            $setting->MAIL_HOST = $request->MAIL_HOST;
            $setting->MAIL_PORT = $request->MAIL_PORT;
            $setting->MAIL_USERNAME = $request->MAIL_USERNAME;
            $setting->MAIL_PASSWORD = $request->MAIL_PASSWORD;
            $setting->MAIL_ENCRYPTION = $request->MAIL_ENCRYPTION;
            $setting->phone = $request->phone;
            $setting->address = $request->address;
            $setting->update();
        }else{
            $setting = new Setting();
            $setting->user_id = Auth::user()->id;
            $setting->IMAP_HOST = $request->IMAP_HOST;
            $setting->IMAP_PORT = $request->IMAP_PORT;
            $setting->IMAP_ENCRYPTION = $request->IMAP_ENCRYPTION;
            if($request->IMAP_VALIDATE_CERT == "TURE")
            {
                $setting->IMAP_VALIDATE_CERT = 1;
            }
            elseif($request->IMAP_VALIDATE_CERT == "FALSE")
            {
                $setting->IMAP_VALIDATE_CERT = 0;
            }
            $setting->IMAP_USERNAME = $request->IMAP_USERNAME;
            $setting->IMAP_PASSWORD = $request->IMAP_PASSWORD;
            $setting->IMAP_DEFAULT_ACCOUNT = $request->IMAP_DEFAULT_ACCOUNT;
            $setting->IMAP_PROTOCOL = $request->IMAP_PROTOCOL;
            $setting->MAIL_DRIVER = $request->MAIL_DRIVER;
            $setting->MAIL_HOST = $request->MAIL_HOST;
            $setting->MAIL_PORT = $request->MAIL_PORT;
            $setting->MAIL_USERNAME = $request->MAIL_USERNAME;
            $setting->MAIL_PASSWORD = $request->MAIL_PASSWORD;
            $setting->MAIL_ENCRYPTION = $request->MAIL_ENCRYPTION;
            $setting->phone = $request->phone;
            $setting->address = $request->address;
            $setting->save();
        }
        Session::flash('success', 'You have successfully updated settings');
        return redirect()->back();
    }
}

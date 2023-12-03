<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Session;
use App\Bank_Account;
use App\Models\Envelope;
use App\Models\Category;
use App\Models\Transaction;
use App\Mail\InvoiceEmail;
use App\User;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

use Illuminate\Http\Request;

class EnvelopeReportController extends Controller
{
    public $section = "Accounting";
    public $page = "Banking & Financial";

    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index()
    {
        $section = $this->section;
        $page = $this->page . ' envelope';

        $user = Auth::user()->name;
        $userId = Auth::user()->id;
        $categories = Category::orderBy('id', 'ASC')->get();
        $envelopes = Envelope::all();

        return view('admin.reports.index', compact('envelopes', 'section', 'page', 'categories', 'user','userId'));
    }

    public function usersList($id){

        $user = User::where('id', $id)->first();

        $section = $this->section;
        $page = $this->page . ' envelope';
        $categories = Category::orderBy('id', 'ASC')->get();
        $envelopes = Envelope::where('enveloped_by', $id)->get();

        $envCount = Envelope::count('enveloped_by', $id);

       $grandTotal = Envelope::getGrandTotal($id);

        return view('admin.reports.user-list', compact('section', 'page', 'categories', 'envelopes', 'envCount', 'user','grandTotal'));

    }

    public function notify(Request $request)
    {
        //find booking
        if ($request->trans_id) {
            $transaction=Transaction::where('id',$request->trans_id)->first();
            $product_listing = Transaction::emailProductLIst($request->trans_id);
            $token = [$request->trans_id, now()];
            $link = serialize($token);
            $encrypted = Crypt::encryptString($link);
            $link= url('/email/share-email/'.$encrypted);
            //send email to customer - refund true
            try {
                // Send Booking Cancelled email
                $EmailSubject = EmailSubject::where('token', 's4ad52j8')->first();
                $EmailTemplate = EmailTemplate::where('domain', 6)->where('subject_id', $EmailSubject['id'])->first();
                Mail::to($request->send_email)->queue(new ReportEmail($EmailSubject['subject'], $transaction, $product_listing, $transaction->vendor, $EmailTemplate,$link));
            } catch (\Exception $ex) {
                //do nothing
            }
            //set success message and redirect to bookings.show
            Session::flash('booking_updated', __('Vendor invoice email successfully sent.'));
            return redirect(url('/admin/transactions/'.$request->trans_id));
        }
    }

}

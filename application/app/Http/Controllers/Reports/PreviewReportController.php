<?php

namespace App\Http\Controllers\Reports;

use PDF;
use MPDF;
use Session;
use Mpdf\Tag\Tr;
use App\Models\Envelope;
use App\User;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\EmailSubject;
use App\Models\EmailTemplate;
use App\Mail\EnvelopeEmail;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Model;

class PreviewReportController extends Controller
{

    public $section = "Accounting";
    public $page = "Banking & Financial";


    public function __construct()
    {
        $this->middleware('auth');
    }


    public function previewReport($id)
    {
        $envelopes = Envelope::find($id);
        $allenvelopes = Envelope::OrderBy('id', 'asc')->get();
        $transactions = Transaction::where('envelope_id', $id)->get();
        $category = Category::where('id', $envelopes->category_id)->first();
        $token = [$envelopes->id, now()];
        $link = serialize($token);
        $encrypted = Crypt::encryptString($link);
        $categoryName = $category->name;
        $section = $this->section;
        $grandTotal = 0;
        foreach ($transactions as $transaction) {
            $p = $transaction->total;
            $grandTotal += $p;
        }
        return view('admin.reports.reports-preview', compact('envelopes', 'allenvelopes', 'section', 'id', 'transactions', 'grandTotal', 'categoryName', 'encrypted'));
    }


    public function printUserReportPdf($id)
    {
        $user = User::where('id', $id)->first();
        $categories = Category::orderBy('id', 'ASC')->get();
        $envelopes = Envelope::where('enveloped_by', $id)->get();
        $envCount = Envelope::count('enveloped_by', $id);
        $grandTotal = Envelope::getGrandTotal($id);
        return view('admin.reports.user_report_pdf_print', compact('categories', 'envelopes', 'envCount', 'user','grandTotal'));
    }


     public function printUserReportPdfDownload($id)
    {
        $user = User::where('id', $id)->first();
        $categories = Category::orderBy('id', 'ASC')->get();
        $envelopes = Envelope::where('enveloped_by', $id)->get();
        $envCount = Envelope::count('enveloped_by', $id);
        $grandTotal = Envelope::getGrandTotal($id);
        $f_name = $user->name . uniqid() . '.pdf';
        $pdf = PDF::loadView('admin.reports.user_report_pdf', compact('categories', 'envelopes', 'envCount', 'user','grandTotal'));
        return $pdf->stream($f_name);

    }

    public function printReportPdf($id)
    {
        $transactions = Transaction::where('envelope_id', $id)->get();
        $envelope = Envelope::where('id', $id)->first();
        $count = Transaction::where('envelope_id', $id)->count();
        $category = Category::where('id', $envelope->category_id)->first();
        $categoryName = $category->name;

        $grandTotal = 0;
        foreach ($transactions as $transaction) {
            $p = $transaction->total;
            $grandTotal += $p;
            $printname = $transaction->printName($transaction->vendor_id);
        }
        $user = Auth::user()->name;
        $f_name = $envelope->name . uniqid() . '.pdf';
        $pdf = PDF::setOptions(['images' => true])->loadView('admin.reports.report_pdf', compact('envelope', 'transactions', 'count', 'grandTotal', 'user', 'categoryName'));
        return $pdf->stream($f_name);

    }


    public function reportDownload($id)
    {
        $transactions = Transaction::where('envelope_id', $id)->get();
        $envelope = Envelope::where('id', $id)->first();
        $count = Transaction::where('envelope_id', $id)->count();
        $category = Category::where('id', $envelope->category_id)->first();
        $categoryName = $category->name;

        $grandTotal = 0;
        foreach ($transactions as $transaction) {
            $p = $transaction->total;
            $grandTotal += $p;
            $printname = $transaction->printName($transaction->vendor_id);
        }
        $user = Auth::user()->name;
        $f_name = $envelope->name . uniqid() . '.pdf';

        // $pdf = PDF::loadView('home.shop.order_pdf');
        // return $pdf->download('order' . $order->id . '_' . ucwords($user->first_name) . ucwords($user->last_name) . '.pdf');

        $pdf = PDF::loadView('admin.reports.report_pdf', compact('envelope', 'transactions', 'count', 'grandTotal', 'user', 'categoryName'));
        return $pdf->download($f_name);
    }


    public function shereReport($id)
    {
        $report_id = Crypt::decryptString($id);
        $getid = unserialize($report_id);
        $reporId = $getid[0];

        $to_time = strtotime(date('Y-m-d H:i:s'));
        $from_time = strtotime($getid[1]);
        $time = round(abs($to_time - $from_time) / 60, 2);


        if ($time > 1440) {
            return view('admin.reports.expired');
        } else {
            $envelopes = Envelope::find($reporId);
            $allenvelopes = Envelope::OrderBy('id', 'asc')->get();
            $transactions = Transaction::where('envelope_id', $reporId)->get();
            $category = Category::where('id', $envelopes->category_id)->first();

            $categoryName = $category->name;
            $section = $this->section;
            $grandTotal = 0;
            foreach ($transactions as $transaction) {
                $p = $transaction->total;
                $grandTotal += $p;
            }
            return view('admin.reports.share-link', compact('envelopes', 'allenvelopes', 'section', 'id', 'transactions', 'grandTotal', 'categoryName'));
        }
    }

    public function shereLink($id)
    {
        $envelopes = Envelope::find($id);

        $token = [$envelopes->id, now()];

        $link = base64_encode(serialize($token));


        // $data = ['id'=>444, 'datetime' => '2021-04-23 17:10'];
        echo $para = base64_encode(serialize($link ));
        echo '<br>';

        print_r(unserialize(base64_decode($para)));
    }

     public function notifyReport(Request $request)
    {
        //find booking
        
        if ($request->user_id) {

            $envelope = Envelope::where('enveloped_by',$request->user_id)->first();
            $token = [$envelope->id, now()];
            $link = serialize($token);
            $encrypted = Crypt::encryptString($link);
            $link= url('/reports/share-report/'.$encrypted);

            //send email to customer - refund true
            try {
                // Send Booking Cancelled email
                $EmailSubject = EmailSubject::where('token', 'n7jd911k')->first();
                $EmailTemplate = EmailTemplate::where('domain', 6)->where('subject_id', $EmailSubject['id'])->first();

                Mail::to($request->send_email)->queue(new EnvelopeEmail($EmailSubject['subject'], $envelope->name,$EmailTemplate,$link));
            } catch (\Exception $ex) {
                //do nothing
                //print_r($ex);die;
            }
            //set success message and redirect to bookings.show
            Session::flash('success', __('User report link successfully sent.'));
            return redirect(url('/admin/reports/users/'.$request->user_id));
        }
    }
}

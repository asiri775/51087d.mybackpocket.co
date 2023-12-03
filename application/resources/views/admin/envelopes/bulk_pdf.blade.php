<div onload="myFunction()" style="width:100%;">
    <script type="text/javascript" language="javascript">
        function myFunction() {

            window.print();
            window.document.close();
            setTimeout(function () {
                window.close();
            }, 1000);
        }
    </script>
    @foreach($transactions as $key=>$transaction)
        <table @if($key>0) style="page-break-after:always;" @endif>
            <div style="font-family:sans-serif; box-sizing: border-box;">
                <div
                    style="width:100%; box-sizing: border-box; text-align:right; padding-bottom:15px; padding-top:20px; padding-right:10px; border-bottom:3px #808080 solid;">
                    <img src="http://users.test/admin/logo.png" width="190px" height="48px" style="">
                </div>
                <div class="row" style="font-family:sans-serif; font-size:14px; margin-top:15px;">
                    <table border="0" cellpadding="8" cellspacing="0" align="left">
                        <caption style="margin:5px ; font-size:20px;">Envelope Summary
                            #{{$transaction->envelope_id}}</caption>
                        <tr>
                            <th width="150"
                                style="text-align:left; background-color:#fff; color:#000; border:1px solid #000;">Name
                                of Account
                            </th>
                            <th width="154"
                                style="text-align:left; background-color:#fff; color:#000; border:1px solid #000;">{{$transaction->vendor!=null?$transaction->vendor->name:'-'}}</th>
                        </tr>
                    </table>
                </div>
                <div class="row" style="font-family:sans-serif; font-size:14px; margin-top:15px;">
                    <table border="0" cellpadding="3" cellspacing="0" align="left" style="margin-top:30px;">
                        <tr>
                            <th width="160"
                                style="text-align:left; background-color:#000; color:#fff; border:1px solid #000;">
                                Envelope Date
                            </th>
                            <th width="160"
                                style="text-align:left; background-color:#000; color:#fff; border:1px solid #000;">
                                Account Number
                            </th>
                        </tr>
                        <tr>
                            <td style="text-align:left; border:1px solid #000;">{{ date('m/d/Y', strtotime($envelope->envelope_date)) }}</td>
                            <td style="text-align:left; border:1px solid #000;">account_number</td>
                        </tr>
                    </table>
                </div>
                <div class="row" style="font-family:sans-serif; font-size:14px;font-weight:bolder; ">
                    <table border="0" cellpadding="3" cellspacing="0" align="left" style="margin-top:90px;">
                        <tr>
                            <td width="150" height="15" style="text-align:left; background: transparent; "></td>
                            <td width="154" height="15"
                                style="text-align:left; background: transparent; border:0px"></td>
                            <td width="50" height="15" style="text-align:center; color:#000; border:0px solid #000;">
                                Initials
                            </td>
                        </tr>
                        <tr>
                            <td width="150" height="15" style="text-align:left; background: transparent; ">Prepared By
                                :
                            </td>
                            <td width="154" height="15"
                                style="text-align:left; background: transparent; border:0px solid #000;border-bottom:3px solid black;"></td>
                            <td width="50" height="15" style="text-align:left; color:#000; border:4px solid #000;"></td>
                        </tr>
                    </table>
                </div>
                <div class="row" style="font-family:sans-serif; font-size:14px;font-weight:bolder; ">
                    <table border="0" cellpadding="3" cellspacing="0" align="left" style="margin-top:155px;">
                        <tr>
                            <td width="150" height="15"
                                style="text-align:left; background: transparent; color:#000; "></td>
                            <td width="154" height="15"
                                style="text-align:left; background: transparent; border:0px"></td>
                            <td width="50" height="15" style="text-align:center; color:#000; border:0px solid #000;">
                                Initials
                            </td>
                        </tr>
                        <tr>
                            <td width="150" height="15" style="text-align:left; background: transparent; color:#000; ">
                                Employee Depositor :
                            </td>
                            <td width="154" height="15"
                                style="text-align:left; background: transparent; border:0px ;border-bottom:3px solid black;"></td>
                            <td width="50" height="15"
                                style="text-align:left; background: transparent; border:4px solid #000;"></td>
                        </tr>
                    </table>
                </div>
                <div class="row" style="font-family:sans-serif; font-size:14px; margin-top:80px;">
                    <table border="0" cellpadding="3" cellspacing="0" align="left" style="margin-top:230px;">
                        <tr>
                            <td width="100"
                                style="text-align:center; background-color:#000; color:#fff;  border:1px solid #000;">
                                Reference
                            </td>
                            <td width="60"
                                style="text-align:center; background-color:#000; color:#fff;  border:1px solid #000;">
                                Method
                            </td>
                            <td width="200"
                                style="text-align:center; background-color:#000; color:#fff;  border:1px solid #000;">
                                Vendor
                            </td>
                            <td width="100"
                                style="text-align:center; background-color:#000; color:#fff; border:1px solid #000;">
                                Transaction
                            </td>
                            <td width="60"
                                style="text-align:right; background-color:#000; color:#fff; border:1px solid #000;">
                                Amount
                            </td>
                        </tr>
                        @foreach($transactions as $trans)
                            <tr>
                                <td style="text-align:left; border:2px solid #000;">{{ $trans->order_no}}</td>
                                <td style="text-align:left; border:2px solid #000;">{{ $trans->payment_method }}</td>
                                <td style="text-align:left; border:2px solid #000;">{{ $trans->vendor!=null?$trans->vendor->name:'-' }}</td>
                                <td style="text-align:left; border:2px solid #000;">{{ $trans->transaction_no}}</td>
                                <td style=" border:2px solid #000; vertical-align:middle;"><span
                                        style="float:left;"></span><span
                                        style="float:right;">{{ '$'. number_format($trans->amount, 2) }}</span></td>
                            </tr>
                        @endforeach
                        <tr>
                            <td></td>
                            <td></td>
                            <td width="150"
                                style="font-size:18px;text-align:right; background-color:#fff; color:#000; border:4px solid #000;">
                                Total
                            </td>
                            <td colspan="2" width="60"
                                style="font-size:22px;text-align:center; background-color:#fff; color:#000; border:4px solid #000;">
                                $ {{$transaction->sum('total')}}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td colspan="1" width="150"
                                style="font-size:18px;text-align:right; background-color:#fff; color:#000; border:4px solid #000;">
                                No. of Items
                            </td>
                            <td colspan="2" width="60"
                                style="font-size:22px;text-align:center; background-color:#fff; color:#000; border:4px solid #000;">{{$count}}</td>
                        </tr>
                    </table>
                </div>
                {{--     <div class="row" style="font-family:sans-serif; font-size:14px; ">

                        <table border="0" cellpadding="8" cellspacing="0" align="left" style="margin-top:520px;">


                            <tr>
                                <th width="150" style="text-align:left; background-color:#fff; color:#000; border:4px solid #000;">Deposit Total</th>
                                <th width="154" style="text-align:left; background-color:#fff; color:#000; border:4px solid #000;">Total {{$deposit->toInvoice->first()->toMethod->NAME}} value $</th>
                                <th width="154" style="text-align:left; background-color:#fff; color:#000; border:4px solid #000;">{{$deposit->toInvoice->sum('GRAND_TOTAL')}}</th>
                            </tr>
                            <tr>
                                <th width="150" style="text-align:left; background-color:#fff; color:#000; border:4px solid #000;">Total {{$deposit->toInvoice->first()->toMethod->NAME}}</th>
                                <th width="154" style="text-align:left; background-color:#fff; color:#000; border:4px solid #000;">Total {{$deposit->toInvoice->first()->toMethod->NAME}} deposit</th>
                                <th width="154" style="text-align:left; background-color:#fff; color:#000; border:4px solid #000;">{{$deposit->toInvoice->count()}}</th>
                            </tr>




                        </table>
                    </div> --}}



                {{--  <div class="row" style="position:absolute;font-family:sans-serif; font-size:18px;font-weight:bolder;bottom:150px; ">
                 <span>Prepared By: &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; _________________________________________</span><table><tr><th width="8" style="text-align:left; background-color:#fff; color:#000; border:4px solid #000;">asd</th></tr></table>
                 <br>
                 <br>
                 <span>Employee Depositor: &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;   __________________________________________</span>
                 </div> --}}

            </div>
        </table>
    @endforeach
</div>

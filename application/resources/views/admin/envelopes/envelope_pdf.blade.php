<div style="width:95%;">
    <div style="font-family:sans-serif; box-sizing: border-box;height: 1000px;">
        <div
            style="width:100%; box-sizing: border-box; text-align:right; padding-bottom:15px; padding-top:20px; padding-right:10px; border-bottom:3px #808080 solid;">
            <img src="https://users.backpocket.ca/admin/logo.jpeg" width="190px" height="48px" style="">
        </div>
        <div class="row" style="font-family:sans-serif; font-size:14px; margin-top:15px;">
            <table>
                <caption style="margin:15px 15px 30px 15px ; font-size:30px; ">Envelope Summary</caption>
                <tbody>
                <tr>
                    <td><strong>Envelope ID # </strong> &nbsp;&nbsp;&nbsp;</td>
                    <td>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ $envelope->id }}</td>
                </tr>
                <tr>
                    <td><strong>Envelope Name</strong> &nbsp;&nbsp;&nbsp;</td>
                    <td>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ $envelope->name }}</td>
                </tr>
                <tr>
                    <td><strong>Envelope Date </strong> &nbsp;&nbsp;&nbsp;</td>
                    <td width="200px">:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ $envelope->envelope_date }}</td>
                </tr>
                <tr>
                    <td width="200px"><strong>Envelope Category</strong> &nbsp;&nbsp;</td>
                    <td>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ $categoryName }}</td>
                </tr>
                <tr>
                    <td width="200px"><strong>Enveloped By</strong> &nbsp;&nbsp;</td>
                    <td>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ $user }} </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="row" style="font-family:sans-serif; font-size:14px; margin-top:10px;">
            <table width="100%" cellpadding="3" cellspacing="0" align="left"
                   style="margin-top:30px; border:2px solid #000;">
                <tr>
                    <th width="150"
                        style="font-size:17px;text-align:center; background-color:#000000; color:#ffffff; border:1px solid #000;">
                        Transaction No
                    </th>
                    <th width="60"
                        style="text-align:center;   border:1px solid #000; color:#ffffff; background-color:#000000;">
                        Method
                    </th>
                    <th width="60"
                        style="text-align:center;   border:1px solid #000; color:#ffffff; background-color:#000000;">
                        Vendor
                    </th>
                    <th width="60"
                        style="text-align:right;  border:1px solid #000; color:#ffffff; background-color:#000000;">
                        Amount
                    </th>
                </tr>
                @foreach ($transactions as $transaction)
                    <tr>
                        <td style="text-align:left; border:1px solid #000;">{{ $transaction->transaction_no }}</td>
                        <td style="text-align:left; border:1px solid #000;">{{ $transaction->payment_method }}</td>
                        <td style="text-align:left; border:1px solid #000;">{{ $transaction->vendor!=null?$transaction->vendor->name:'-' }}</td>
                        <td style=" border:1px solid #000; vertical-align:middle;"><span
                                style="float:left;"></span><span style="float:right;">$
                                {{ $transaction->total }}</span>
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="3" width="150"
                        style="font-size:18px;text-align:right; background-color:#ffffff; color:#000; border:1px solid #000;">
                        <b>Total</b>
                    </td>
                    <td width="60"
                        style="font-size:22px;text-align:right; background-color:#ffffff; color:#000; border:1px solid #000;">
                        <b>$ {{ $grandTotal }}</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" width="150"
                        style="font-size:18px;text-align:right; background-color:#ffffff; color:#000; border:1px solid #000;">
                        <b>No. of Items</b>
                    </td>
                    <td width="60"
                        style="font-size:22px;text-align:right; background-color:#ffffff; color:#000; border:1px solid #000;">
                        <b>{{ $count }}</b>
                    </td>
                </tr>


            </table>
        </div>
    </div>

    @foreach ($transactions as $transaction)
        <div style="font-family:sans-serif; box-sizing: border-box;height: 950px;margin-top: 20px;">
            <link href="{{ asset('admin/pages/css/pages-icons.css') }}" rel="stylesheet" type="text/css">
            <link class="main-stylesheet" href="{{ asset('admin/pages/css/pages.css') }}" rel="stylesheet"
                  type="text/css"/>
            <style>
                body {
                    background: #fff;
                    padding: 1rem;
                }

                footer {
                    position: absolute;
                    left: 0;
                    bottom: 1%;
                    padding: 0 1.5rem;
                }
            </style>
            <div class="invoice sm-padding-10">
                <div>
                    <div class="row">
                        @if($transaction->vendor!=null)
                        <div class="col-md-4" style="float: left;">
                            <img width="20%"
                                 src="https://users.backpocket.ca/admin/assets/img/vendor-logos/<?=$transaction->vendor->logo . '.png'?>"
                                 alt="Logo">
                            <address class="m-t-10">
                                <?php
                                $store_no = trim($transaction->vendor->store_no);
                                $street_name = trim($transaction->vendor->street_name);
                                $city = trim($transaction->vendor->city);
                                $state = trim($transaction->vendor->state);
                                $zip_code = trim($transaction->vendor->zip_code);
                                $phone = trim($transaction->vendor->phone);
                                $HST = trim($transaction->vendor->HST);
                                $extra_info = collect(json_decode($transaction->extra_info, true));
                                ?>
                                @if($store_no) Store# {{$store_no}}<br> @endif
                                @if($street_name){{ $street_name }}, @endif
                                @if($city){{$city }}<br>@endif
                                @if($state){{ $state }}, @endif
                                @if($zip_code){{ $zip_code }}<br>@endif
                                @if($phone){{ $phone }}@endif
                                @if($HST) | HST#{{ $HST }} @endif
                            </address>
                        </div>
                        @endif
                        <div class="col-md-5"></div>
                        <div class="col-md-3">
                            <div class="sm-m-t-10">
                                <h2 class="font-montserrat all-caps text-right font-weight-bold">
                                    Total:
                                    $ {{ number_format((float)($transaction->total)?$transaction->total:0, 2, '.', '')}}
                                </h2>
                                <address style="clear: both; margin-top: 70px;">
                                    <p>
                                        <strong>Date:</strong> {{ date("d/m/Y", strtotime($transaction->transaction_date)) }}
                                    </p>
                                    <p>
                                        <strong>Time:</strong> {{ date("h:i A", strtotime($transaction->transaction_date)) }}
                                    </p>
                                    <p>
                                        <strong>Order # </strong> {{ $transaction->order_no }}
                                    </p>
                                    <p>
                                        <strong>Transaction # </strong> {{ $transaction->transaction_no }}
                                    </p>
                                </address>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="table-responsive table-invoice">
                    <table class="table m-t-10" style="width: 100%; height: 600px;">
                        <thead>
                        <tr>
                            <th class="text-left">ITEM</th>
                            <th class="text-center">QTY</th>
                            <th class="text-right">AMOUNT</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($transaction->purchase as $purchase)
                            <tr>
                                <td class="v-align-middle text-left">
                                    <strong>{{ $purchase->product->name }}</strong>
                                    @if($purchase->product->description)
                                        <br/>
                                        {!! $purchase->product->description !!}
                                    @endif
                                </td>
                                <td class="v-align-middle text-center">{{$purchase->quantity}}</td>
                                <td class="v-align-middle text-right">
                                    $ {{ number_format((float)($purchase->price)?$purchase->price:0, 2, '.', '') }}</td>
                            </tr>
                        @endforeach
                        @if($extra_info && $extra_info->where('type', 'desc')->count())
                            <tr>
                                <td class="v-align-middle text-left" colspan="3"
                                    style="padding: 1px!important; border-bottom: none;">
                                    <div class="b-a b-grey p-t-10 p-b-40 p-l-5 p-r-5">
                                        <br/>
                                        <h5 class="font-weight-bold" style="padding: 5px; border: none !important;">
                                            EXTRA
                                            INFORMATION</h5>
                                        <br/>
                                        <div class="justify-content-left align-items-end m-b-30 m-t-10">

                                            <table class="border table-striped" width="100%">
                                                @foreach($extra_info as $info)
                                                    @if($info['type'] == 'desc')
                                                        <tr>
                                                            <td>
                                                                {{ $info['label'] }}
                                                            </td>
                                                            <td>
                                                                {!! $info['value'] !!}
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </table>
                                        </div>
                                    </div>

                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td
                                style="padding: 1px!important; border-bottom: none;">
                                <div class="b-a b-grey p-t-10 p-b-40 p-l-5 p-r-5">
                                    <h5 class="m-b-10 font-weight-bold ml-2">PAYMENT
                                        INFORMATION
                                    </h5>
                                    <br/>
                                    <address class="m-t-10 p-r-50 ml-2">
                                        <p>
                                            <strong>METHOD:</strong>
                                            &nbsp;&nbsp;&nbsp; {{ $transaction->payment_method }}
                                        </p>
                                        <br/>
                                        <p>
                                            <strong>REFERENCE:</strong>&nbsp;N/A
                                        </p>
                                    </address>
                                </div>

                            </td>
                            <td class="v-align-middle text-right" colspan="2"
                                style="border-bottom: none;">
                                <div class="align-items-end">
                                    <p>
                                        <strong>SUBTOTAL:</strong>&nbsp;$ {{ number_format((float)($transaction->sub_total)?$transaction->sub_total:0, 2, '.', '')}}
                                    </p>
                                    @if($transaction->discount != null)
                                        <p>
                                            <strong>Discount:</strong>&nbsp;$ {{ number_format((float)($transaction->discount)?$transaction->discount:0, 2, '.', '')}}
                                        </p>
                                    @endif
                                    @if($extra_info && $extra_info->where('type', 'amount')->count())
                                        @foreach($extra_info as $info)
                                            @if($info['type'] == 'amount')
                                                <p>
                                                    <strong>{{ $info['label'] }}:</strong>
                                                    {{ $info['value'] }}
                                                </p>
                                            @endif
                                        @endforeach
                                    @endif
                                    @if($transaction->vendor!=null && $transaction->vendor->name != 'Apple')
                                        <p>
                                            <strong>TAXES:</strong>&nbsp;$ {{ number_format((float)($transaction->tax_amount)?$transaction->tax_amount:0, 2, '.', '') }}
                                        </p>
                                    @endif

                                    <hr/>
                                    <strong style="font-size: 18px;">
                                        Total:
                                        $ {{ number_format((float)($transaction->total)?$transaction->total:0, 2, '.', '')}}
                                    </strong>
                                </div>

                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div width="50%">
                        <img align="left" style="width: 15%; float: left;"
                             src="https://users.backpocket.ca/admin/logo.jpeg" alt="Logo">
                        <hr/>
                        <div align="left" style="width: 50%; float: left;" style="font-size: 1rem; margin-top: 2px;">
                            BackPocket <br>
                            27 Evans Avenue, Toronto, Ontario, Canada M8Z 1K2 Inc. <br> info@backpocket.ca
                        </div>
                    </div>


                </div>
            </div>

        </div>
    @endforeach
</div>


<div style="width:95%;">
    <div style="font-family:sans-serif; box-sizing: border-box;height: 1000px;">
        <div
            style="width:100%; box-sizing: border-box; text-align:right; padding-bottom:15px; padding-top:20px; padding-right:10px; border-bottom:3px #808080 solid;">
            <img src="http://shop.protectica.ca/users-images/logo.jpeg" width="190px" height="48px">
        </div>
        <div style="font-family:sans-serif; font-size:14px; margin-top:15px;">
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
        <div style="font-family:sans-serif; font-size:14px; margin-top:10px;height: 500px;">
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
                        <td style="text-align:left; border:1px solid #000;">{{ $transaction->vendor!=null ? $transaction->vendor->name: '-' }}</td>
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
        <div style="font-family:sans-serif; box-sizing: border-box;height: 970px;">
            <div class="row">
                <div style="clear: both;" width="100%">
                    @if($transaction->vendor!=null)
                        <div width="50%">
                            <div style="float: left;">
                                <img width="20%"
                                     src="http://shop.protectica.ca/users-images/vendor-logos/<?=$transaction->vendor->logo . '.png'?>"
                                     alt="Logo">
                                <div>
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
                                </div>
                            </div>
                        </div>
                    @endif
                    <div width="50%">
                        <h2 style="float: right;font-size: 24px;font-weight: 700;">
                            Total: $ {{ number_format((float)($transaction->total)?$transaction->total:0, 2, '.', '')}}
                        </h2>
                    </div>
                </div>
                <div style="clear: both; margin-top: 70px;">
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
                </div>
            </div>
            <hr>
            <table style="width: 100%;height: 650px;">
                <thead>
                <tr>
                    <th style="text-align:left;">ITEM</th>
                    <th style="text-align:center;">QTY</th>
                    <th style="text-align:right;">AMOUNT</th>
                </tr>
                </thead>
                <tbody>
                @foreach($transaction->purchase as $purchase)
                    <tr>
                        <td style="text-align:left;">
                            <strong>{{ $purchase->product->name }}</strong>
                            @if($purchase->product->description)
                                <br/>
                                {!! $purchase->product->description !!}
                            @endif
                        </td>
                        <td style="text-align:center;">{{$purchase->quantity}}</td>
                        <td style="text-align:right;">
                            $ {{ number_format((float)($purchase->price)?$purchase->price:0, 2, '.', '') }}</td>
                    </tr>
                @endforeach
                @if($extra_info && $extra_info->where('type', 'desc')->count())
                    <tr>
                        <td style="padding: 1px!important; border-bottom: none;">
                            <div>
                                <br/>
                                <h4 style="padding: 5px; border: none !important;font-size: 18px;font-weight: 700;">
                                    EXTRA
                                    INFORMATION</h4>
                                <div>

                                    <table width="100%">
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
                    <td style="padding: 1px!important; border-bottom: none;">
                        <div>
                            <h5 style="font-weight: 700;">PAYMENT INFORMATION </h5>
                            <div>
                                <p>
                                    <strong>METHOD:</strong>
                                    &nbsp;&nbsp;&nbsp; {{ $transaction->payment_method }}
                                </p>
                                <p>
                                    <strong>REFERENCE:</strong>&nbsp;N/A
                                </p>
                            </div>
                        </div>

                    </td>
                    <td colspan="2" style="border-bottom: none;">
                        <div>
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
            <div style="clear: both; margin-top: 70px;">
                <table style="width: 100%;">
                    <tr>
                        <td><img style="float: left;" src="http://shop.protectica.ca/users-images/logo.jpeg"
                                 width="140px" height="32px" alt="Logo">
                            <p style="float: left;font-size: 1rem; margin-top: 2px;">
                                BackPocket 27 Evans Avenue, Toronto, Ontario,<br/> Canada M8Z 1K2 Inc. <br/>
                                info@backpocket.ca
                            </p></td>
                    </tr>

                </table>
            </div>
        </div>
    @endforeach
</div>


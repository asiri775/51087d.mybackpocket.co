<link href="{{ asset('admin/pages/css/pages-icons.css') }}" rel="stylesheet" type="text/css">
<link class="main-stylesheet" href="{{ asset('admin/pages/css/pages.css') }}" rel="stylesheet" type="text/css" />
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
            <div class="col-md-4" style="float: left;">
                <img width="20%" src="http://shop.protectica.ca/users-images/vendor-logos/<?=$transaction->vendor->logo.'.png'?>" alt="Logo">
                <address class="m-t-10">
                    <?php
                    $store_no=trim($transaction->vendor->store_no);
                    $street_name=trim($transaction->vendor->street_name);
                    $city=trim($transaction->vendor->city);
                    $state=trim($transaction->vendor->state);
                    $zip_code=trim($transaction->vendor->zip_code);
                    $phone=trim($transaction->vendor->phone);
                    $HST=trim($transaction->vendor->HST);
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
            <div class="col-md-5"></div>
            <div class="col-md-3">
                <div class="sm-m-t-10">
                    <h2 class="font-montserrat all-caps text-right font-weight-bold">
                        Total: $ {{ number_format(($transaction->total)?$transaction->total:0, 2, '.', '')}}
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
        <table class="table m-t-10" style="width: 100%;">
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
                            <br />
                            {!! $purchase->product->description !!}
                        @endif
                    </td>
                    <td class="v-align-middle text-center">1</td>
                    <td class="v-align-middle text-right"> $ {{ number_format(($purchase->price)?$purchase->price:0, 2, '.', '') }}</td>
                </tr>
            @endforeach
            @if($extra_info && $extra_info->where('type', 'desc')->count())
            <tr>
                <td class="v-align-middle text-left" colspan="3"
                    style="padding: 1px!important; border-bottom: none;">
                    <div class="b-a b-grey p-t-10 p-b-40 p-l-5 p-r-5">
                        <br />
                        <h5 class="font-weight-bold" style="padding: 5px; border: none !important;">EXTRA INFORMATION</h5>
                        <br />
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
                        <br />
                        <address class="m-t-10 p-r-50 ml-2">
                        <p>
                            <strong>METHOD:</strong> &nbsp;&nbsp;&nbsp; {{ $transaction->payment_method }}
                        </p>
                            <br />
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
                        <strong>SUBTOTAL:</strong>&nbsp;$ {{ number_format(($transaction->sub_total)?$transaction->sub_total:0, 2, '.', '')}}
                    </p>
                        @if($transaction->discount != null)
                        <p>
                            <strong>Discount:</strong>&nbsp;$ {{ number_format(($transaction->discount)?$transaction->discount:0, 2, '.', '')}}
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
                        @if($transaction->vendor->name != 'Apple')
                            <p>
                                <strong>TAXES:</strong>&nbsp;$ {{ number_format(($transaction->tax_amount)?$transaction->tax_amount:0, 2, '.', '') }}
                            </p>
                        @endif

                        <hr />
                        <strong style="font-size: 18px;">
                            Total: $ {{ number_format(($transaction->total)?$transaction->total:0, 2, '.', '')}}
                        </strong>
                    </div>

                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<footer>
    <div width="100%"> 
        <img align="left" style="width: 15%; float: left;" src="http://shop.protectica.ca/users-images/logo.jpeg" alt="Logo">
        <div align="right" style="width: 50%; float: left;" style="font-size: 1rem; margin-top: 2px;">BackPocket Inc.</div>
    </div>
    <hr />
    <div width="100%"> 
        <div align="left" style="width: 50%; float: left;">27 Evans Avenue, Toronto, Ontario, Canada M8Z 1K2</div>
        <div align="right" style="width: 48%; float: left;">info@backpocket.ca</div>
    </div>
</footer>
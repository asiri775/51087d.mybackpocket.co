@extends('admin.layouts.newMaster')
@section('title', 'Transaction Details')
@section('page-css')
@endsection
@section('content')
    <style>
        .list-group-item {
            padding: unset;
        }

        #archive {
            z-index: 10000 !important;
        }

        .table-header {
            color: #fff !important;
            font-weight: bold !important;
        }

        .payment_note {
            width: 22%;
            display: inline-block;
        }

    </style>
    <!-- START CONTAINER FLUID -->
    <div class="container-fluid transaction-page">
        @if (Session::has('success'))
            <div class="alert alert-success">{{ Session::get('success') }}</div>
        @elseif(Session::has('error'))
            <div class="alert alert-danger">{{ Session::get('error') }}</div>
    @endif
    <!-- START card -->
        <div class="card card-default">
            <div class="card-header separator">
                <div class="d-flex justify-content-between flex-column flex-md-row">
                    <div class="card-title">
                        <h5><strong style="color:#626262!important">Transaction Details</strong></h5>
                    </div>
                    <div class="card-title">
                       <h5><strong style="color:#626262!important">Transaction ID # {{ $transaction->id }}</strong></h5>
                    </div>
                 </div>

            </div>
            <div class="card-body p-t-20">
                <!-- <div class="container-fluid"> -->
                <div class="row">
                    <div class="col-lg-7">
                        <div class="card card-default">
                            <div class="invoice">
                                <div class="d-flex align-items-center justify-content-around flex-column flex-xl-row p-t-10">
                                    <div>
                                        <?php
                                        $company_name='';
                                        ?>
                                        @if($transaction->vendor)
                                        <img class="transaction-logo"
                                             style="width: 200px; position: relative; left: 50%; transform: translateX(-50%)"
                                             alt="Logo"
                                             data-src-retina="{{ asset('admin/assets/img/vendor-logos/' . $transaction->vendor->logo_vendor ) }}"
                                             data-src="{{ asset('admin/assets/img/vendor-logos/' . $transaction->vendor->logo_vendor ) }}"
                                             src="{{ asset('admin/assets/img/vendor-logos/' . $transaction->vendor->logo_vendor ) }}">

                                        <address class="m-t-10 text-center">
                                            <?php
                                            $company_name = trim($transaction->vendor->name);
                                            $store_no = trim($transaction->vendor->store_no);
                                            $address = trim($transaction->vendor->address);
                                            $street_name = trim($transaction->vendor->street_name);
                                            $city = trim($transaction->vendor->city);
                                            $state = trim($transaction->vendor->state);
                                            $zip_code = trim($transaction->vendor->zip_code);
                                            $phone = trim($transaction->vendor->phone);
                                            $HST = trim($transaction->vendor->HST);
                                            ?>

                                            @if($company_name){{$company_name}}<br> @endif
                                            @if($company_name=='Uber Trip') {{$address}}@endif
                                            @if($store_no) Store# {{$store_no}}<br> @endif
                                            @if($street_name){{ $street_name }}, @endif
                                            @if($city){{$city }}<br>@endif
                                            @if($state){{ $state }}, @endif
                                            @if($zip_code){{ $zip_code }}<br>@endif
                                            @if($phone){{ $phone }}@endif
                                            @if($HST) | HST#{{ $HST }} @endif
                                        </address>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="sm-m-t-20">
                                            <h2 class="font-montserrat all-caps text-center font-weight-bold">
                                                $ {{ \App\Helpers\Helper::formatNumber($transaction->total) }}
                                            </h2>
                                            <address class="m-t-10 text-center">
                                                {{ date('m/d/Y', strtotime($transaction->transaction_date)) }} <br/>
                                                {{ date('h:i A', strtotime($transaction->transaction_date)) }} <br>
                                                @if($transaction->order_no){{($company_name=="Starbucks")?'Transaction':'Order'}} # {{ $transaction->order_no }}@endif
                                            </address>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="table-responsive">
                                    <table class="table m-t-10">
                                        <thead>
                                        <tr style="background: darkgray;">
                                            <th class="text-left col-md-7 table-header">
                                                ITEM
                                            </th>
                                            <th class="text-center col-md-2 table-header">
                                                QTY
                                            </th>
                                            <th class="text-right col-md-3 table-header">
                                                AMOUNT
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($transaction->purchase as $purchase)
                                            <tr>
                                                <td class="v-align-middle text-left">
                                                    {{ $purchase->product->name }}
                                                    @if ($purchase->product->description)
                                                        <br/>
                                                        {!! $purchase->product->description !!}
                                                    @endif
                                                </td>
                                                <td class="v-align-middle text-center">1</td>
                                                <td class="v-align-middle text-right">
                                                    ${{ \App\Helpers\Helper::formatNumber(trim($purchase->price))}}</td>
                                            </tr>
                                        @endforeach
                                        @if ($extra_info && $extra_info->where('type', 'desc')->count())
                                            <tr>
                                                <td class="v-align-middle text-center" colspan="3"
                                                    style="border-bottom: none;">
                                                    <div class="b-grey p-t-10 p-b-40 p-l-5 p-r-5">
                                                        <h5 class="m-b-20 font-weight-bold">EXTRA INFORMATION</h5>
                                                        <div class="row">
                                                            @foreach ($extra_info as $info)
                                                                @if ($info['type'] == 'desc')
                                                                    <div class="col-md-6 border p-2">
                                                                        <strong>{{ $info['label'] }}</strong>
                                                                    </div>
                                                                    <div class="col-md-6 border p-2">
                                                                        {!! $info['value'] !!}
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                        </tbody>
                                    </table>


                                    <br>
                                    <div class="d-flex align-items-center flex-column flex-xl-row">
                                        <div class="col-xl-7 col-lg-12">
                                            <div class="b-a b-grey p-t-10 p-b-40 p-l-5 p-r-5">
                                                <h5 class="m-b-10 font-weight-bold p-l-10 fs-16 text-uppercase">Payment Information</h5>
                                                <address class="m-t-10  text-left justify-content-start p-l-10">
                                                    <h6 class="m-b-10 m-t-10 font-weight-bold fs-14">PAYMENT DETAILS
                                                    </h6>
                                                    <table width="100%">
                                                        <thead>
                                                        <tr>
                                                            <th width="45%"></th>
                                                            <th width="55%"></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <td><span style="float: right;">METHOD &nbsp;:</span>
                                                            </td>
                                                            <td>
                                                                <span style="float: left;"> &nbsp;@if( $transaction->payment_method != '') {{ $transaction->payment_method }}@else
                                                                        N/A @endif </span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="float: right;"><span>PAYMENT REFERENCE
                                                                    &nbsp;:</span></td>
                                                            <td>
                                                                <span style="float: left;"> &nbsp;@if( $transaction->payment_ref != '') {{ $transaction->payment_ref }}@else
                                                                        N/A @endif </span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <hr>
                                                            </td>
                                                            <td>
                                                                <hr>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="float: right;"><span>AUTH ID &nbsp;:</span>
                                                            </td>
                                                            <td>
                                                                <span style="float: left;">&nbsp;@if( $transaction->auth_id){{ $transaction->auth_id }}@else
                                                                        N/A @endif </span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="float: right;"><span>TRANS. DATE
                                                                    &nbsp;:</span></td>
                                                            <td>
                                                                <span style="float: left;">&nbsp;@if( $transaction->auth_id){{ $transaction->auth_id }}@else
                                                                        N/A @endif </span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="float: right;"><span>TRANS. TIME
                                                                    &nbsp;:</span></td>
                                                            <td>
                                                                <span style="float: left;">&nbsp;@if( $transaction->auth_id){{ $transaction->auth_id }}@else
                                                                        N/A @endif </span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="float: right;"><span>OPERATOR ID
                                                                    &nbsp;:</span></td>
                                                            <td>
                                                                <span style="float: left;">&nbsp;@if( $transaction->operator_id){{ $transaction->operator_id }}@else
                                                                        N/A @endif </span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="float: right;"><span>TERMINAL #&nbsp;:</span>
                                                            </td>
                                                            <td>
                                                                <span style="float: left;">&nbsp;@if( $transaction->terminal_no){{ $transaction->terminal_no }}@else
                                                                        N/A @endif </span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="float: right;"><span>REGISTER #&nbsp;:</span>
                                                            </td>
                                                            <td>
                                                                <span style="float: left;">&nbsp;@if( $transaction->register_no){{ $transaction->register_no }}@else
                                                                        N/A @endif </span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="float: right;"><span>BAR CODE &nbsp;:</span>
                                                            </td>
                                                            <td>
                                                                <span style="float: left;">&nbsp;@if( $transaction->bar_qr_code){{ $transaction->bar_qr_code }}@else
                                                                        N/A @endif </span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="float: right;"><span>EMPLOYEE ID
                                                                    &nbsp;:</span></td>
                                                            <td>
                                                                <span style="float: left;">&nbsp;@if( $transaction->employee_no){{ $transaction->employee_no }}@else
                                                                        N/A @endif </span>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                    <br>
                                                    <h6 class="m-b-10 m-t-10 font-weight-bold fs-14">GENERAL NOTES</h6>
                                                    @foreach ($extra_info as $info)
                                                       @if ($info['key'] == 'DOCUMENT NO')
                                                       <table width="100%">
                                                        <thead>
                                                        <tr>
                                                            <th width="45%"></th>
                                                            <th width="45%"></th>
                                                        </tr></thead>
                                                        <tbody><tr>
                                                            <td style="float: right;"><span>DOCUMENT NO
                                                                    &nbsp;:</span></td>
                                                            <td>
                                                                <span style="float: left;">{{$info['value']}}</span>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                       </table>
                                                       @endif
                                                   @endforeach
                                                    @if($company_name=='PizzaPizza')
                                                    @foreach ($extra_info as $info)
                                                    @if ($info['key'] == 'address')
                                                  <?php
                                                  $formated='<p>';
                                                  $text  = explode(" " , $info['value'] );
                                                    foreach($text as $key=>$value){
                                                    if($key==1 OR $key==6 OR $key==9 OR $key==12){
                                                        $formated.=' '.$value.' <br/>';
                                                    }
                                                    else {
                                                        $formated.=' '.$value;
                                                    }
                                                    }
                                                ?>
                                                    <?=$formated?></p><br/>
                                                    @endif
                                                    @if ($info['key'] == 'delivery')
                                                    <b>Your expected delivery time:</b><br/>
                                                    <p>{{$info['value']}}</p><br/>
                                                    @endif
                                                    @endforeach
                                                    @endif
                                                </address>
                                            </div>
                                        </div>
                                        <div class="col-xl-5 col-lg-12 p-b-50">
                                            <div style="border-bottom: none;" class="extra">
                                                <div class="p-b-10 text-right justify-content-center align-items-end">
                                                    <table width="100%">
                                                        <thead>
                                                        <tr>
                                                            <th width="73%"></th>
                                                            <th width="27%"></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <td><span style="float: right;">SUBTOTAL&nbsp;:</span>
                                                            </td>
                                                            <td>
                                                                <span style="float: left;">${{ \App\Helpers\Helper::formatNumber(($transaction->sub_total)) }}</span>
                                                            </td>
                                                        </tr>
                                                        @if ($extra_info && $extra_info->where('type', 'amount')->count())
                                                            @foreach ($extra_info as $info)
                                                                @if ($info['type'] == 'amount')
                                                                    <tr>
                                                                        <td>
                                                                            <span style="float: right;">{{ $info['label'] }}&nbsp;:</span></td>
                                                                        <td>
                                                                            <span style="float: left;">${{ \App\Helpers\Helper::formatNumber($info['value'])}}</span>
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                        @if (isset($transaction->vendor_id) AND $transaction->vendor->name != 'Apple')
                                                            <tr>
                                                                <td><span style="float: right;">TAXES&nbsp;:</span></td>
                                                                <td>
                                                                    <span style="float: left;">${{ \App\Helpers\Helper::formatNumber($transaction->tax_amount) }}</span>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="p-t-10 text-right bg-master-darker col-sm-height padding-10 d-flex flex-column justify-content-center align-items-end">
                                                    <h5 class="font-montserrat all-caps small no-margin hint-text text-white bold">
                                                        Total</h5>
                                                    <h1 class="no-margin text-white">
                                                        ${{ \App\Helpers\Helper::formatNumber($transaction->total) }}</h1>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 col-md-12">
                        <div class="card card-default">
                            <div class="card-header separator">
                                <div class="card-title">
                                    <div class="row justify-content-center">
                                        <div class="col-md-4">
                                            <button class="btn btn-primary btn-cons m-b-10 btn-block" type="button"
                                                    onclick="PrintElem('.invoice')">
                                                <i class="fa fa-print"></i> <span class="bold">PRINT</span>
                                            </button>
                                        </div>
                                        <div class="col-md-4">
                                            <a href="#!" onclick="modalSend({{ $transaction->id }})"
                                               class="btn btn-success btn-cons m-b-10 btn-block" data-toggle="modal"
                                               data-target="#send" trans_id="{{ $transaction->id }}"><i
                                                        class="fa fa-envelope"></i> <span class="bold">EMAIL</span>
                                            </a>
                                        </div>
                                        <div class="col-md-4">
                                            <a href="{{ route('transactions.mpdf', ['transaction' => $transaction->id]) }}"
                                               class="btn btn-info btn-cons m-b-10 btn-block p-l-10" type="button"><i
                                                        class="fa fa-download"></i> <span class="bold">DOWNLOAD</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                @if ($transaction->is_archived != '0')
                                    <br>
                                    <h5 class="font-weight-bold"><strong>Archived</strong></h5>
                                @else
                                    <h5 class="font-weight-bold"><strong>Organize</strong></h5>
                                    <p class="m-b-20">We've made it easy for you to sort receipts and organize
                                        your finances.</p>
                                    <p class="m-b-30">Add them to `Envelopes` to categorize your expenses.
                                        Create Budgets to track your goal vs actual expenses</p>
                                    <form action="{{ url('admin/transactions/add-to-envelope') . '/' . $transaction->id }}"
                                          id="form_env" method="POST">
                                        {{ csrf_field() }}
                                        <div class="input-group required">
                                            <select id="filter_envelope" class="form-control" name="envelope_id">
                                                <option value="0">Select An Envelope</option>
                                                @foreach($envelopes as $envelope)
                                                    <option value="{{ $envelope->id }}">{{ $envelope->name }}</option>
                                                @endforeach
                                            </select>
                                            <button class="input-group-text primary " style="cursor: pointer;">ADD
                                            </button>
                                        </div>
                                        <p class="small m-t-10">
                                            <a href="{{ route('envelope.list') }}"><span>Go To Envelopes Manager</span>
                                                <i class="fa fs-12 fa-arrow-circle-o-right text-success m-l-10"></i></a>
                                        </p>
                                    </form>
                                    <br>
                                    <form action="{{ url('admin/transactions/add-to-budgets') . '/' . $transaction->id }}"
                                          id="form-budget" method="POST">
                                        {{ csrf_field() }}
                                        <div class="input-group required">
                                            <select id="filter_budget" class="form-control" name="budget_id">
                                                <option value="">Select A Budget</option>
                                                @foreach($budgets as $budget)
                                                    <option value="{{ $budget->id }}">{{ $budget->name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-append">
                                                <button class="input-group-text primary" style="cursor: pointer;">ADD
                                                </button>
                                            </div>
                                        </div>
                                        <p class="small m-t-10">
                                            <a href="{{route('budgets.list')}}">
                                                <span>Go To Budget Manager</span>
                                                <a class="fa fs-12 fa-arrow-circle-o-right text-success m-l-10"></a>
                                            </a>
                                        </p>
                                    </form>
                                    <br>
                                    <h5 class="font-weight-bold"><strong>Archive It!</strong></h5>
                                    <p class="m-b-20">Don't need receipt anymore? Put them away quickly with our one
                                        touch archive</p>
                                    <form action="" id="form-archive">
                                        <div class="input-group required">
                                            <a onclick="modalArchive({{ $transaction->id }})" type="button"
                                               class="btn btn-primary btn-block" data-toggle="modal"
                                               data-target="#archive" id="trans_id" style="color:#fff;">SEND TO
                                                ARCHIVES</a>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- </div> -->
    </div>
     @include('admin.transactions.print')
    </div>

@endsection
@section('page-js')
    <script>
        $(document).ready(function (e) {
            $("#filter_envelope").select2();
            $("#filter_budget").select2();
            var table = $('#tableTransactions');
            var trans_datatable = table.DataTable({
                "processing": true,
                "serverSide": true,
                "sDom": "<t><'row'<p i>>",
                "destroy": true,
                "scrollCollapse": true,
                "oLanguage": {
                    "sLengthMenu": "_MENU_ ",
                    "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
                },
                "iDisplayLength": 5,
                "method": "post",
                "ajax": {
                    "url": "{{ route('transactions.datatable') }}",
                    "type": "POST",
                    'data': function (data) {
                        data.order_no = $('#filter_order_no').val();
                    }
                },
                "order": [
                    [0, "asc"]
                ],
                "columns": [{
                    data: 'id',
                    name: 'id'
                },
                    {
                        data: 'transaction_date',
                        name: 'transaction_date'
                    },
                    {
                        data: 'order_no',
                        name: 'order_no'
                    },
                    {
                        data: 'vendor_id',
                        name: 'vendor_id'
                    },
                    {
                        data: 'vendor_email',
                        name: 'vendor_email'
                    },
                    {
                        data: 'total',
                        name: 'total'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            $(document).on('keyup', '#filter_order_no', function () {
                console.log($('#filter_order_no').val());
                trans_datatable.draw();
            });

            //Date Pickers
            $('#daterangepicker').daterangepicker({
                timePicker: true,
                timePickerIncrement: 30,
                format: 'MM/DD/YYYY h:mm A'
            }, function (start, end, label) {
                console.log(start.toISOString(), end.toISOString(), label);
            });
        });

        function modalSend(trans_id) {

            $('#trans_id').val(trans_id);
        }

        function modalArchive(trans_id) {
            $('#trans_id').val(trans_id);
        }

    </script>
    <div id="send" class="modal fade" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ __('Send invoice email') }}</h4>
                </div>
                <form method="post" action="{{ route('transactions.notify') }}">
                    {{ csrf_field() }}
                    {{ method_field('POST') }}
                <div class="modal-body">
                    <br>
                    <h5>{{ __('Share this Receipt via email to the address below') }}</h5>
                    <input type="text" name="send_email" placeholder="email@domain.com" class="form-control">
                    <input type="hidden" name="trans_id" value="{{ $transaction->id }}">
                    <input type="hidden" class="form-control" value="{{url('/email/share-email/'.$encrypted)}}" name="link">
                    <div id="successMessage" style="display:none;" class="alert alert-success" role="alert">{{ __('Invoice
                        successfully sent')  }}
                    </div>
                </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">{{ __('Send') }}</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <div id="archive" class="modal fade" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Archive Transaction</h4>
                </div>
                <div class="modal-body">
                    <p>Are sure you want to Archive Transaction ?</p>
                </div>
                <form method="POST" action=" {{ url('admin/archive/transactions') }} ">
                    <div class="modal-footer">
                        {{ csrf_field() }}
                        {{ method_field('POST') }}
                        <input type="hidden" name="trans_id" value="{{ $transaction->id }}" id="trans_id">
                        <button type="submit" class="btn btn-danger">Archive</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

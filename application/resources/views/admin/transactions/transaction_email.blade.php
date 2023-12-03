<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8"/>
    <meta charset="utf-8"/>
    <title>@yield('title')</title>
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no"/>
    <link rel="apple-touch-icon" href="{{ asset('admin/pages/ico/60.png') }} ">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('admin/pages/ico/76.png') }} ">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('admin/pages/ico/120.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('admin/pages/ico/152.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('admin/favicon.ico') }}"/>
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta content="" name="description"/>
    <meta content="" name="author"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('admin/assets/plugins/pace/pace-theme-flash.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('admin/assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('admin/assets/plugins/font-awesome/css/font-awesome.css') }}" rel="stylesheet"
          type="text/css"/>
    <link href="{{ asset('admin/assets/plugins/jquery-scrollbar/jquery.scrollbar.css') }}" rel="stylesheet"
          type="text/css" media="screen"/>
    <link href="{{ asset('admin/assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css"
          media="screen"/>
    <link href="{{ asset('admin/assets/plugins/switchery/css/switchery.min.css') }}" rel="stylesheet" type="text/css"
          media="screen"/>
    <link href="{{ asset('admin/assets/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css') }}" rel="stylesheet"
          type="text/css"
          media="screen">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.1/css/buttons.dataTables.min.css">
    <link href="{{ asset('admin/pages/css/pages-icons.css') }}" rel="stylesheet" type="text/css">
    <link class="main-stylesheet" href="{{ asset('admin/pages/css/pages.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('admin/css/dashboard.css') }}" rel="stylesheet"/>
    @yield('page-css')
    <script src="{{ asset('admin/assets/plugins/pace/pace.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('admin/assets/plugins/jquery/jquery-3.2.1.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('admin/assets/plugins/modernizr.custom.js') }}" type="text/javascript"></script>
    <script src="{{ asset('admin/assets/plugins/jquery-ui/jquery-ui.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('admin/assets/plugins/popper/umd/popper.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('admin/assets/plugins/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('admin/assets/plugins/jquery/jquery-easy.js') }}" type="text/javascript"></script>
    <script src="{{ asset('admin/assets/plugins/jquery-unveil/jquery.unveil.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('admin/assets/plugins/jquery-ios-list/jquery.ioslist.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('admin/assets/plugins/jquery-actual/jquery.actual.min.js') }}"></script>
    <script src="{{ asset('admin/assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('admin/assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('admin/assets/plugins/classie/classie.js') }}"></script>
    <script src="{{ asset('admin/assets/plugins/switchery/js/switchery.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('admin/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('admin/assets/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('admin/assets/plugins/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('admin/assets/plugins/bootstrap-timepicker/bootstrap-timepicker.min.js') }}"></script>
    
    <!-- END VENDOR JS -->
    <!-- BEGIN CORE TEMPLATE JS -->
    <script src="{{ asset('admin/pages/js/pages.js') }}"></script>
    <!-- END CORE TEMPLATE JS -->
    <!-- BEGIN PAGE LEVEL JS -->
    <script src="{{ asset('admin/assets/js/scripts.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL JS -->
    <!-- END CORE TEMPLATE JS -->
    <!-- BEGIN PAGE LEVEL JS -->
    <script src="{{ asset('admin/assets/js/datatables.js') }}" type="text/javascript"></script>
    <!-- <script src="{{ asset('admin/assets/js/form_elements.js') }}" type="text/javascript"></script> -->
    <script src="{{ asset('admin/assets/js/scripts.js') }}" type="text/javascript"></script>
    <script src="{{ asset('admin/js/dashboard.js') }}" type="text/javascript"></script>
</head>

<div class="header ">
    <!-- START MOBILE SIDEBAR TOGGLE -->
    <a href="#" class="btn-link toggle-sidebar d-lg-none pg pg-menu" data-toggle="sidebar">
    </a>
    <!-- END MOBILE SIDEBAR TOGGLE -->
    <div class="">
        <div class="brand inline   ">
            <!-- <img src="assets/img/logo.png" alt="logo" data-src="assets/img/logo.png"
    data-src-retina="assets/img/logo_2x.png" width="78" height="22"> -->
            <strong><img style="margin-left: 4rem" src="/admin/logo.jpeg" alt="Logo"></strong>
        </div>

    </div>
   
</div>
    <!-- START PAGE CONTENT WRAPPER -->
   
        <!-- START PAGE CONTENT -->
        <div class="content ">
            <!-- START JUMBOTRON -->
            <div class="jumbotron" data-pages="parallax">
                <div class=" container-fluid   container-fixed-lg sm-p-l-0 sm-p-r-0">
                    <div class="inner">
                        <!-- START BREADCRUMB -->
                        <ol class="breadcrumb">
                            <!-- <li class="breadcrumb-item"><a href="#">Title</a></li> -->
                            <li class="breadcrumb-item"><a href="" >Dashboard</a></li>
                           
                            <li class="breadcrumb-item active">Transaction</li>
                      
                        </ol>
                        <!-- END BREADCRUMB -->
                    </div>
                </div>
            </div>

           


    <style>
        .dataTables_filter {
            display: none;
        }

    </style>


<section>
        <?php
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Input;
    ?>
    {{-- Modal --}}
    <div class="page-content-wrapper ">
        <!-- START PAGE CONTENT -->
        <div class="content ">
            <!-- START JUMBOTRON -->

            @if (Session::has('success'))
                <div class="alert alert-success">{{ Session::get('success') }}</div>
            @elseif(Session::has('error'))
                <div class="alert alert-danger">{{ Session::get('error') }}</div>
            @endif


            <div class="container-fluid container-fixed-lg">
                <div class="card card-default">
                    <div class="card-header separator">
                        <div class="card-title">
                            <h5><b>Envelope Details Report</b></h5>
                        </div>
                    </div>
                    <div class="card-body p-t-20">
                       
                            <div class="card card-default">
                                <div class="invoice">
                                    <div class="d-flex align-items-center justify-content-around flex-column flex-xl-row p-t-10">
                                        <div>
                                            <img class="transaction-logo"
                                                 style="width: 200px; position: relative; left: 50%; transform: translateX(-50%)"
                                                 alt="Logo"
                                                 data-src-retina="{{ asset('admin/assets/img/vendor-logos/' . $transaction->vendor->logo . '.png') }}"
                                                 data-src="{{ asset('admin/assets/img/vendor-logos/' . $transaction->vendor->logo . '.png') }}"
                                                 src="{{ asset('admin/assets/img/vendor-logos/' . $transaction->vendor->logo . '.png') }}">
                                            <address class="m-t-10 text-center">
                                                <?php
                                                $company_name = trim($transaction->vendor->name);
                                                $store_no = trim($transaction->vendor->store_no);
                                                $street_name = trim($transaction->vendor->street_name);
                                                $city = trim($transaction->vendor->city);
                                                $state = trim($transaction->vendor->state);
                                                $zip_code = trim($transaction->vendor->zip_code);
                                                $phone = trim($transaction->vendor->phone);
                                                $HST = trim($transaction->vendor->HST);
                                                ?>
    
                                                @if($company_name){{$company_name}}<br> @endif
                                                @if($store_no) Store# {{$store_no}}<br> @endif
                                                @if($street_name){{ $street_name }}, @endif
                                                @if($city){{$city }}<br>@endif
                                                @if($state){{ $state }}, @endif
                                                @if($zip_code){{ $zip_code }}<br>@endif
                                                @if($phone){{ $phone }}@endif
                                                @if($HST) | HST#{{ $HST }} @endif
                                            </address>
                                        </div>
                                        <div>
                                            <div class="sm-m-t-20">
                                                <h2 class="font-montserrat all-caps text-center font-weight-bold">
                                                    $ {{ number_format($transaction->total, 2, '.', '') }}
                                                </h2>
                                                <address class="m-t-10 text-center">
                                                    {{ date('m/d/Y', strtotime($transaction->transaction_date)) }} <br/>
                                                    {{ date('h:i A', strtotime($transaction->transaction_date)) }} <br>
                                                    Order # {{ $transaction->order_no }}
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
                                                        <strong>{{ $purchase->product->name }}</strong>
                                                        @if ($purchase->product->description)
                                                            <br/>
                                                            {!! $purchase->product->description !!}
                                                        @endif
                                                    </td>
                                                    <td class="v-align-middle text-center">1</td>
                                                    <td class="v-align-middle text-right">
                                                        ${{ number_format(($purchase->price)?$purchase->price:0, 2, '.', '') }}</td>
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
                                                    <h5 class="m-b-10 font-weight-bold p-l-10">Notes</h5>
                                                    <address class="m-t-10  text-left justify-content-start p-l-10">
                                                        <h6 class="m-b-10 m-t-10 font-weight-bold">PAYMENT
                                                            DETAILS
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
                                                                <td><strong style="float: right;">METHOD &nbsp;:</strong>
                                                                </td>
                                                                <td>
                                                                    <span style="float: left;"> &nbsp;@if( $transaction->payment_method != '') {{ $transaction->payment_method }}@else
                                                                            N/A @endif </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="float: right;"><strong>PAYMENT REFERENCE
                                                                        &nbsp;:</strong></td>
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
                                                                <td style="float: right;"><strong>AUTH ID &nbsp;:</strong>
                                                                </td>
                                                                <td>
                                                                    <span style="float: left;">&nbsp;@if( $transaction->auth_id){{ $transaction->auth_id }}@else
                                                                            N/A @endif </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="float: right;"><strong>TRANS. DATE
                                                                        &nbsp;:</strong></td>
                                                                <td>
                                                                    <span style="float: left;">&nbsp;@if( $transaction->auth_id){{ $transaction->auth_id }}@else
                                                                            N/A @endif </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="float: right;"><strong>TRANS. TIME
                                                                        &nbsp;:</strong></td>
                                                                <td>
                                                                    <span style="float: left;">&nbsp;@if( $transaction->auth_id){{ $transaction->auth_id }}@else
                                                                            N/A @endif </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="float: right;"><strong>OPERATOR ID
                                                                        &nbsp;:</strong></td>
                                                                <td>
                                                                    <span style="float: left;">&nbsp;@if( $transaction->operator_id){{ $transaction->operator_id }}@else
                                                                            N/A @endif </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="float: right;"><strong>TERMINAL #&nbsp;:</strong>
                                                                </td>
                                                                <td>
                                                                    <span style="float: left;">&nbsp;@if( $transaction->terminal_no){{ $transaction->terminal_no }}@else
                                                                            N/A @endif </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="float: right;"><strong>REGISTER #&nbsp;:</strong>
                                                                </td>
                                                                <td>
                                                                    <span style="float: left;">&nbsp;@if( $transaction->register_no){{ $transaction->register_no }}@else
                                                                            N/A @endif </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="float: right;"><strong>BAR CODE &nbsp;:</strong>
                                                                </td>
                                                                <td>
                                                                    <span style="float: left;">&nbsp;@if( $transaction->bar_qr_code){{ $transaction->bar_qr_code }}@else
                                                                            N/A @endif </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="float: right;"><strong>EMPLOYEE ID
                                                                        &nbsp;:</strong></td>
                                                                <td>
                                                                    <span style="float: left;">&nbsp;@if( $transaction->employee_no){{ $transaction->employee_no }}@else
                                                                            N/A @endif </span>
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                        <br>
                                                        <h6 class="m-b-10 m-t-10 font-weight-bold">GENERAL NOTES
                                                        </h6>
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
                                                                    <span style="float: right;">${{ number_format(($transaction->sub_total)?$transaction->sub_total:0, 2, '.', '') }}</span>
                                                                </td>
                                                            </tr>
                                                            @if ($extra_info && $extra_info->where('type', 'amount')->count())
                                                                @foreach ($extra_info as $info)
                                                                    @if ($info['type'] == 'amount')
                                                                        <tr>
                                                                            <td>
                                                                                <span style="float: right;">{{ $info['label'] }}&nbsp;:</span></td>
                                                                            <td>
                                                                                <span style="float: right;">${{ number_format(($info['value'])?$info['value']:0, 2, '.', '') }}</span>
                                                                            </td>
                                                                        </tr>
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                            @if ($transaction->vendor->name != 'Apple')
                                                                <tr>
                                                                    <td><span style="float: right;">TAXES&nbsp;:</span></td>
                                                                    <td>
                                                                        <span style="float: right;">${{ number_format(($transaction->tax_amount)?$transaction->tax_amount:0, 2, '.', '') }}</span>
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
                                                            ${{ number_format($transaction->total, 2, '.', '') }}</h1>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                </div>
                            </div>
                       
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<footer>
    <div class=" container-fluid  container-fixed-lg footer">
        <div class="copyright sm-text-center">
            <p class="small no-margin pull-left sm-pull-reset">
                &copy;2019 Backpocket Inc.<span class="hint-text"> All Rights Reserved</span>
            </p>
            <div class="clearfix"></div>
        </div>
    </div>
</footer>



     <!-- START CONTAINER FLUID -->

    

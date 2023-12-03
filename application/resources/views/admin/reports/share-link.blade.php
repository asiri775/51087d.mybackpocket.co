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
                           
                            <li class="breadcrumb-item active">Reports</li>
                      
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
                        <div class="row">
                            <div class="row justify-content-left p-l-50 col-md-8">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td>Envelope ID #: &nbsp;&nbsp;&nbsp;</td>
                                            <td><b>{{ $envelopes->id }}</b></td>
                                        </tr>
                                        <tr>
                                            <td>Envelope Name : &nbsp;&nbsp;&nbsp;</td>
                                            <td><b>{{ $envelopes->name }}</b></td>
                                        </tr>
                                        <tr>
                                            <td>Envelope Date : &nbsp;&nbsp;&nbsp;</td>
                                            <td><b>{{ $envelopes->envelope_date }}</b></td>
                                        </tr>
                                        <tr>
                                            <td>Envelope Category: &nbsp;&nbsp;</td>
                                            <td><b>{{ $categoryName }}</b></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            {{-- <div class="pull-right col-md-4">
                                <div class="pull-right" style="">
                                    <a class="btn btn-primary" href="{{url('admin/reports/print').'/'.$envelopes->id}}" data-toggle="tooltip" data-placement="bottom"
                                        title="Print"><i class="fa fa-print fa-10x"></i></a>
                                    <a class="btn btn-danger" href="{{url('admin/reports/download').'/'.$envelopes->id}}" data-toggle="tooltip" data-placement="bottom"
                                        title="Download"><i class="fa fa-download"></i></a>
                                        <a href="" class="btn btn-success btn-md">Share Link</a>
                                </div>

                            </div> --}}
                        </div>

                        <div class="widget-11-2-table p-t-20">
                            <table class="table table-hover table-condensed table-responsive" id="previewTable">

                                <thead>
                                    <tr>
                                        <th class="text-center" width="1%">Transaction#</th>
                                        <th class="text-center">Vendor</th>
                                        <th class="text-center">Reference#</th>
                                        <th class="text-center">Method</th>
                                        <th class="text-center">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>


                                    @foreach ($transactions as $transaction)
                                        <tr>
                                            <td class="text-center">{{ $transaction->id }}</td>
                                            <td class="text-center">{{ $transaction->vendor->name }}</td>
                                            <td class="text-center">{{ $transaction->order_no }}</td>
                                            <td class="text-center">{{ $transaction->payment_method }}</td>
                                            <td class="text-center">${{ $transaction->getAmount($transaction->id) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="content-group "
                            style="float: right !important; padding-right: 100px; padding-right: 100px;  ">
                            <br>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <label style="font-size: 20px;">No. of Receipts :
                                                <b>{{ $transactions->count() }}</b></label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <label style="font-size: 20px;">Envelope Total : <b>${{ $grandTotal }}</b>
                                            </label>
                                        </div>
                                    </div>
                                </div>
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


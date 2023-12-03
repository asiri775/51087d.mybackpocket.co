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
    <link href="{{ asset('admin/assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css"
          media="screen"/>
    <link href="{{ asset('admin/pages/css/pages-icons.css') }}" rel="stylesheet" type="text/css">
    <link class="main-stylesheet" href="{{ asset('admin/pages/css/pages.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('admin/css/dashboard.css') }}" rel="stylesheet"/>
    @yield('page-css')
</head>
<body class="fixed-header ">
<x-admin.sidebar/>

<!-- START PAGE-CONTAINER -->
<div class="page-container ">

    <x-admin.header/>

    <!-- START PAGE CONTENT WRAPPER -->
    <div class="page-content-wrapper ">
        <!-- START PAGE CONTENT -->
        <div class="content ">
            <!-- START JUMBOTRON -->
            <div class="jumbotron" data-pages="parallax">
                <div class=" container-fluid   container-fixed-lg sm-p-l-0 sm-p-r-0">
                    <div class="inner">
                        <!-- START BREADCRUMB -->
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard')}}">Dashboard</a></li>
                            @if(request()->is('admin/vendors*'))
                                <li class="breadcrumb-item active">Vendors</li>
                            @endif
                            @if(request()->is('admin/products*'))
                                <li class="breadcrumb-item active">Products</li>
                            @endif
                            @if(request()->is('admin/transactions*'))
                                <li class="breadcrumb-item active">Transactions</li>
                            @endif
                            @if(request()->is('admin/sales*'))
                                <li class="breadcrumb-item active">Sales</li>
                            @endif
                            @if(request()->is('admin/envelopes*'))
                                <li class="breadcrumb-item active">Envelopes</li>
                            @endif
                            @if(request()->is('admin/reports*'))
                                <li class="breadcrumb-item active">Reports</li>
                            @endif
                        </ol>
                        <!-- END BREADCRUMB -->
                    </div>
                </div>
            </div>
            <!-- END JUMBOTRON -->

            @yield('content')

        </div>
        <div class=" container-fluid  container-fixed-lg footer">
            <div class="copyright sm-text-center">
                <p class="small no-margin pull-left sm-pull-reset">
                    &copy;2019 Backpocket Inc.<span class="hint-text"> All Rights Reserved</span>
                </p>
                <div class="clearfix"></div>
            </div>
        </div>
        <!-- END COPYRIGHT -->
    </div>
    <!-- END PAGE CONTENT WRAPPER -->
</div>

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

<script src="{{ asset('admin/pages/js/pages.js') }}"></script>
<script src="{{ asset('admin/assets/js/scripts.js') }}" type="text/javascript"></script>
<script src="{{ asset('admin/assets/js/scripts.js') }}" type="text/javascript"></script>
<script src="{{ asset('admin/js/dashboard.js') }}" type="text/javascript"></script>

@yield('page-js')

<script>

       function modalSend(envelope_id) 
       {
        $('#envelope_id').val(envelope_id);
        $("#successMessage").hide();
       }
       
    // $(document).ready(function() {

    //     $('#sendEnvelope').on('click', function(e) {
    //         $.ajaxSetup({
    //             headers: {
    //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
    //                     'content')
    //             }
    //         });
    //         $.ajax({
    //             type: "POST",
    //             url: '{{ route('envelopes.notify') }}',
    //             data: {
    //                 'envelope_id': $("#envelope_id").val(),
    //                 'send_email': $("#send_email").val(),
    //             },
    //             success: function(data) {
    //                 $("#successMessage").show();
    //             }
    //         });
    //         return false;
    //     });
    // });

</script>

<!-- END PAGE LEVEL JS -->
</body>

</html>

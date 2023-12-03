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
    <link href="{{ asset('admin/assets/plugins/jquery-datatable/media/css/dataTables.bootstrap.min.css') }}"
          rel="stylesheet"
          type="text/css"/>
    <link
        href="{{ asset('admin/assets/plugins/jquery-datatable/extensions/FixedColumns/css/dataTables.fixedColumns.min.css') }}"
        rel="stylesheet" type="text/css"/>
    <link href="{{ asset('admin/assets/plugins/datatables-responsive/css/datatables.responsive.css') }}"
          rel="stylesheet" type="text/css"
          media="screen"/>
    <link href="{{ asset('admin/pages/css/pages-icons.css') }}" rel="stylesheet" type="text/css">
    <link class="main-stylesheet" href="{{ asset('admin/pages/css/pages.css') }}" rel="stylesheet" type="text/css"/>

    <script src="//unpkg.com/alpinejs" defer></script>

    <script>
        const BASE_URL = "{{env('APP_URL')}}";
    </script>

    <style>
        .dataTables_filter {
            display: none;
        }

        #ActivateAdvanceSerach, #HideActivateAdvanceSerach {
            color: #fff !important;
            background-color: #00238C !important;
            border-color: #3b475 !important;
        }

        .searchFilters {
            background-color: #eaebe6;
        }

        .card .card-body {
            padding: 5px 10px 5px 10px !important;
        }

        #transactionsTable_info {
            text-align: center;
            text-transform: uppercase;
            font-size: 14px;
        }

        .dataTables_wrapper .dataTables_paginate {
            margin-top: 15px !important;
            float: unset !important;
            text-align: center !important;
            margin-bottom: 20px !important;
        }

        .dataTables_wrapper .dataTables_paginate ul > li {
            font-size: 14px !important;
        }

        .batch-action {
            color: #fff !important;
            background-color: #010267 !important;
            border: 1px solid #f0f0f0 !important;
        }

        .batch-export {
            color: #fff !important;
            background-color: #248c01 !important;
            border: 1px solid #f0f0f0 !important;
        }

        .batch-export:hover, .batch-action:hover {
            background-color: #fafafa !important;
            border: 1px solid rgba(98, 98, 98, 0.27) !important;
            color: #333 !important;
        }

        .batch-export.active, .batch-action.active {
            border-color: #e6e6e6 !important;
            background: #fff !important;
            color: #333 !important;
        }

        .form-control {
            font-family: Montserrat, sans-serif !important;
        }

        .advanceNonAdvanceSearch {
            color: #fff !important;
            background-color: #00238C !important;
            border-color: #3b475 !important;
        }

        .akDataTable td, .akDataTable th {
            text-align: center;
        }

        .akDataTable .dt-buttons {
            display: none;
        }

        .akDataTable .table tbody tr.selected td {
            background: transparent !important;
        }

        .dataTables_length label {
            text-transform: capitalize;
        }

        .dataTables_length label select {
            text-align: center;
        }

        .akDataTable tfoot {
            display: none;
        }

        .modal-backdrop.show {
            z-index: 9999;
        }

        .modal {
            z-index: 99999 !important;
        }

    </style>
    <style>
        .datepicker table tr td.active {
            background-color: rgb(209, 209, 209)!important;
        }
        .datepicker table tr td {
            padding-left: 10px;
        }
    </style>
    @livewireStyles


    <link href="{{URL::asset('admin/livewire-custom.css')}}?v=1.0" rel="stylesheet">

    <link href="{{ asset('admin/css/dashboard.css') }}" rel="stylesheet"/>
    <script type="text/javascript">
        function PrintElem(elem) {
            Popup($(elem).html());
        }

        function Popup(data) {
            var myWindow = window.open('', 'TRANSACTION DETAILS', 'height=400,width=600');
            myWindow.document.write('<html><head><title>my div</title>');
            /*optional stylesheet*/ //myWindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
            myWindow.document.write('</head><body >');
            myWindow.document.write(data);
            myWindow.document.write('</body></html>');
            myWindow.document.close(); // necessary for IE >= 10

            myWindow.onload = function () { // necessary if the div contain images

                myWindow.focus(); // necessary for IE >= 10
                myWindow.print();
                myWindow.close();
            };
        }
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

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
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            @if(request()->is('admin/bank-statements*'))
                                <li class="breadcrumb-item"><a href="{{ route('bankStatements.list') }}">Bank
                                        Statements</a></li>
                            @endif
                            <li class="breadcrumb-item active">@yield('title')</li>
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
                        </ol>
                        <!-- END BREADCRUMB -->
                    </div>
                </div>
            </div>
            <!-- END JUMBOTRON -->

            @yield('content')

        </div>
        <!-- END PAGE CONTENT -->
        <!-- START COPYRIGHT -->
        <!-- START CONTAINER FLUID -->
        <!-- START CONTAINER FLUID -->
        <div class=" container-fluid  container-fixed-lg footer">
            <div class="copyright sm-text-center">
                <p class="small no-margin pull-left sm-pull-reset">
                    <span>&copy;{{date('Y')}} Backpocket Inc.</span><span class="hint-text"> All Rights Reserved</span>
                </p>
                <div class="clearfix"></div>
            </div>
        </div>
        <!-- END COPYRIGHT -->
    </div>
    <!-- END PAGE CONTENT WRAPPER -->
</div>
<!-- END PAGE CONTAINER -->

<!-- BEGIN VENDOR JS -->
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
<script src="{{ asset('admin/assets/plugins/jquery-datatable/media/js/jquery.dataTables.min.js') }}"
        type="text/javascript"></script>
<script src="{{ asset('admin/assets/plugins/jquery-datatable/extensions/TableTools/js/dataTables.tableTools.min.js') }}"
        type="text/javascript"></script>
<script src="{{ asset('admin/assets/plugins/jquery-datatable/media/js/dataTables.bootstrap.js') }}"
        type="text/javascript"></script>
<script src="{{ asset('admin/assets/plugins/jquery-datatable/extensions/Bootstrap/jquery-datatable-bootstrap.js') }}"
        type="text/javascript"></script>
<script type="text/javascript"
        src="{{ asset('admin/assets/plugins/datatables-responsive/js/datatables.responsive.js') }}"></script>
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
{{--<script src="{{ asset('admin/assets/js/jquery.maskedinput.js') }}" type="text/javascript"></script>--}}
<!-- <script src="{{ asset('admin/assets/js/form_elements.js') }}" type="text/javascript"></script> -->
<script src="{{ asset('admin/assets/js/scripts.js') }}" type="text/javascript"></script>
<script src="{{ asset('admin/js/dashboard.js') }}" type="text/javascript"></script>

<script src="//cdn.datatables.net/plug-ins/1.11.3/sorting/datetime-moment.js"></script>

<script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/buttons/2.1.0/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" language="javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" language="javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" language="javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/buttons/2.1.0/js/buttons.html5.min.js"></script>
<script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/buttons/2.1.0/js/buttons.print.min.js"></script>
<script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/select/1.3.3/js/dataTables.select.min.js"></script>

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>


<script>

    $(document).on("click", ".LwxSelectAll", function () {
        let chkBtn = $('.wire-grid-box table thead input[type="checkbox"]');
        if (!chkBtn.is(":checked")) {
            chkBtn.click();
        }
    });

    $(document).on("click", ".LwxDeSelectAll", function () {
        let chkBtn = $('.wire-grid-box table thead input[type="checkbox"]');
        if (chkBtn.is(":checked")) {
            chkBtn.click();
        } else {
            $('.wire-grid-box table tbody input[type="checkbox"]').each(function () {
                let chkBtn = $(this);
                if (chkBtn.is(":checked")) {
                    chkBtn.click();
                }
            });
        }
    });

    $(document).on("click", ".LwxBulkTrigger", function () {
        let obj = $(this);
        var targetLwxBtn = $(obj).attr('data-target-lwx');
        if (typeof targetLwxBtn !== 'undefined' && targetLwxBtn !== false) {
            $(targetLwxBtn).click();
        }
    });

</script>

@livewireScripts

@yield('page-js')

<!-- END PAGE LEVEL JS -->
</body>

</html>

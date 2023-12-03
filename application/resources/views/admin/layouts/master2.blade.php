<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta charset="utf-8" />
    <title>@yield('title')</title>
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no" />
    <link rel="apple-touch-icon" href="{{ asset('admin/pages/ico/60.png') }} ">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('admin/pages/ico/76.png') }} ">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('admin/pages/ico/120.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('admin/pages/ico/152.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('admin/favicon.ico') }}" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta content="" name="description" />
    <meta content="" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('admin/assets/plugins/pace/pace-theme-flash.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/plugins/font-awesome/css/font-awesome.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/plugins/jquery-scrollbar/jquery.scrollbar.css') }}" rel="stylesheet" type="text/css" media="screen" />
    <link href="{{ asset('admin/assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" media="screen" />
    <link href="{{ asset('admin/assets/plugins/switchery/css/switchery.min.css') }}" rel="stylesheet" type="text/css" media="screen" />
    <link href="{{ asset('admin/assets/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css') }}" rel="stylesheet" type="text/css"
          media="screen">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.1/css/buttons.dataTables.min.css">

    {{--    <link href="{{ asset('admin/assets/plugins/jquery-datatable/media/css/dataTables.bootstrap.min.css') }}" rel="stylesheet"--}}
    {{--          type="text/css" />--}}
    {{--    <link href="{{ asset('admin/assets/plugins/jquery-datatable/extensions/FixedColumns/css/dataTables.fixedColumns.min.css') }}"--}}
    {{--          rel="stylesheet" type="text/css" />--}}
    {{--    <link href="{{ asset('admin/assets/plugins/datatables-responsive/css/datatables.responsive.css') }}" rel="stylesheet" type="text/css"--}}
    {{--          media="screen" />--}}

    {{--    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.1/css/buttons.dataTables.min.css">--}}
    {{--    <link rel="stylesheet" href="https://editor.datatables.net/extensions/Editor/css/editor.dataTables.min.css">--}}





    <link href="{{ asset('admin/pages/css/pages-icons.css') }}" rel="stylesheet" type="text/css">
    <link class="main-stylesheet" href="{{ asset('admin/pages/css/pages.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/css/dashboard.css') }}" rel="stylesheet" />
    @yield('page-css')

</head>

<body class="fixed-header ">

<x-admin.sidebar />

<!-- START PAGE-CONTAINER -->
<div class="page-container ">

    <x-admin.header />

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
                            <!-- <li class="breadcrumb-item"><a href="#">Title</a></li> -->
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
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
                    &copy;2019 Backpocket Inc.<span class="hint-text"> All Rights Reserved</span>
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
<script src="{{ asset('admin/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') }}" type="text/javascript"></script>
<script src="{{ asset('admin/assets/plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('admin/assets/plugins/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('admin/assets/plugins/bootstrap-timepicker/bootstrap-timepicker.min.js') }}"></script>


{{--<script src="{{ asset('admin/assets/plugins/jquery-datatable/media/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>--}}
{{--<script src="{{ asset('admin/assets/plugins/jquery-datatable/extensions/TableTools/js/dataTables.tableTools.min.js') }}"--}}
{{--        type="text/javascript"></script>--}}

{{----}}
{{--<script src="{{ asset('admin/pages/js/dataTables.select.js') }}"></script>--}}
{{--<script src="{{ asset('admin/assets/plugins/jquery-datatable/extensions/Bootstrap/jquery-datatable-bootstrap.js') }}"--}}
{{--        type="text/javascript"></script>--}}
{{--<script type="text/javascript" src="{{ asset('admin/assets/plugins/datatables-responsive/js/datatables.responsive.js') }}"></script>--}}


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





<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.cancel-confirmation').on('click', function () {
        // alert('ss');
        return confirm('Do you really want to cancel envelope?');
    });
    $('.save-confirmation').on('click', function () {
        var name = $('#account option:selected').text();
        var account = $('#account option:selected').val();
        // if(account=='novalue'){
        //     // alert('Please Select Bank Account');
        //     return  false;
        // }
        // else {
        //     return confirm('Do you really want to save deposit to ' + name + ' ?');
        // }
    });
    $('.save-print-confirmation').on('click', function () {
        // alert('ss');
        var name = $('#account option:selected').text();
        return confirm('Do you really want to save Envelope to ' + name + ' and print immediately ?');
    });
    $('#account').on('change', function () {
        var check = $(this).val();
        if (check == 'novalue') {
            $('.save-confirmation').prop('disabled', true);
            $('.save-print-confirmation').prop('disabled', true);
        } else {
            $('.save-confirmation').prop('disabled', false);
            $('.save-print-confirmation').prop('disabled', false);

        }
    });
    $(document).ready(function ()
    {
        var array = [];
        $("#selectAllInvoices").on("click", function(e) {
            var table= $("#EnvelopeTable");
            var boxes = $('input:checkbox', table);
            $.each($('input:checkbox', table), function() {

                $(this).parent().addClass('checked');
                $(this).prop('checked', 'checked');

            });
            $('#deselectAllInvoices').prop('disabled',false);
            $('.save-confirmation').prop('disabled', false);
            $('.save-print-confirmation').prop('disabled', false);
        });

        $("#deselectAllInvoices").on("click", function(e) {
            var table= $("#EnvelopeTable");
            var boxes = $('input:checkbox', table);
            $.each($('input:checkbox', table), function() {

                $(this).parent().removeClass('checked');
                $(this).prop('checked', false);

            });
            $('.save-confirmation').prop('disabled', true);
            $('.save-print-confirmation').prop('disabled', true);
        });

        $('#EnvelopeTable').on('click', 'input', function () {
            // console.log(this.is(':checked'));
            var id = $(this).val();
            var status = $(this).is(':checked');
            if (status) {
                array.push(id);
            } else {
                array.splice(array.indexOf(id), 1);
            }
            if (array.length > 0) {
                $('.save-confirmation').prop('disabled', false);
                $('.save-print-confirmation').prop('disabled', false);
            } else {
                $('.save-confirmation').prop('disabled', true);
                $('.save-print-confirmation').prop('disabled', true);
            }
            // console.log(array)

        });

        $('#account').on('change', function () {
            var account = $(this).val();
            $('#store-deposit input[name="account"]').val(account);
        });
    });
    $('#txt_to_date').on('change', function () {
        var date = $(this).val();
        $('#store-deposit input[name="txt_to_date"]').val(date);
    });

</script>
@yield('page-js')

<!-- END PAGE LEVEL JS -->
</body>

</html>

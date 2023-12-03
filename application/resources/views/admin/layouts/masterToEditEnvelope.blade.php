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



<script type="text/javascript">

    $('.cancel-confirmation').on('click', function () {
        return confirm('Do you really want to cancel envelope?');
    });
    $('.save-confirmation').on('click', function () {
        var name = $('#envName').val();
        return confirm('Do you really want to save envelope ?');
    });

    $(document).ready(function () {
        var array = [];
        $("#selectAllReceipts").on('click', function() {
                array = [];
                var table = $("#previewTable");
                var boxes = $('input:checkbox', table);
                $.each($('input:checkbox', table), function() {
                    $(this).parent().addClass('checked');
                    $(this).prop('checked', 'checked');
                    var id = $(this).val();
                    array.push(id);
                });
                var urlpost = "{{ route('AddReceipts.bulkSession') }}";

                $.ajax({
                    type: 'POST',
                    url: urlpost,
                    data: {
                        fieldOne: array
                    },
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(data) {

                    }

                });

                $('#deselectAllEnvelopes').prop('disabled', false);
            $('#bulkDelete').prop('disabled', false);

                for ($i = 0; $i < array.length; $i++) {
                    $('#form-session').append(
                        $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', 'envelope_id[]')
                        .val(array[$i])
                    );
                }

            });

            $("#deselectAllReceipts").on('click', function() {
                var table = $("#previewTable");
                var boxes = $('input:checkbox', table);
                $.each($('input:checkbox', table), function() {
                    $(this).parent().removeClass('checked');
                    $(this).prop('checked', false);
                    var id = $(this).val();
                    array.splice(array.indexOf(id), 1);
                });
                var urlpost = "{{ route('AddReceipts.clearSession') }}";

                $.ajax({
                    type: 'POST',
                    url: urlpost,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(data) {

                    }
                });
                $('#bulkDelete').prop('disabled', true);
            $('.save-confirmation').prop('disabled', true);
            });

            $('#previewTable').on('click', 'input', function() {
                // console.log(this.is(':checked'));
                var isChecked = $(this).prop('checked');

                var id = $(this).val();
                if ($(this).is(':checked')) {
                    array.push(id);
                    var urlpost = "{{ route('AddReceipts.addSession') }}";
                    $.ajax({
                        type: 'POST',
                        url: urlpost,
                        data: {
                            id: id
                        },
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(data) {

                        }
                    });
                } else {
                    var urlpost = "{{ route('AddReceipts.removeSession') }}";
                    $.ajax({
                        type: 'POST',
                        url: urlpost,
                        data: {
                            id: id
                        },
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(data) {

                        }
                    });
                    array.splice(array.indexOf(id), 1);
                }

                if (array.length > 0) {
                    $('#bulkDelete').prop('disabled', false);
                $('.save-confirmation').prop('disabled', false);
                } else {
                    $('#bulkDelete').prop('disabled', true);
                $('.save-confirmation').prop('disabled', true);

                }

            });


       
        $('#bulkDelete').click(function () {
            for ($i = 0; $i < array.length; $i++) {
                $('#form-delete').append(
                    $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', 'envelope_id[]')
                        .val(array[$i])
                );
            }
        });
        $('#envelope_id').on('change', function () {
            var deposit_id = $(this).val();
            $('#store-envelope input[name="envelope_id"]').val(envelope_id);
        });
    });
</script>
@yield('page-js')

<!-- END PAGE LEVEL JS -->
</body>

</html>

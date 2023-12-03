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

<script>
    const BASE_URL = "{{env('APP_URL')}}";
</script>

<!-- END VENDOR JS -->
<!-- BEGIN CORE TEMPLATE JS -->
<script src="{{ asset('admin/pages/js/pages.js') }}"></script>
<!-- END CORE TEMPLATE JS -->
<!-- BEGIN PAGE LEVEL JS -->
<script src="{{ asset('admin/assets/js/scripts.js') }}" type="text/javascript"></script>
<!-- END PAGE LEVEL JS -->
<!-- END CORE TEMPLATE JS -->
<!-- BEGIN PAGE LEVEL JS -->
{{-- <script src="{{ asset('admin/assets/js/datatables.js') }}" type="text/javascript"></script> --}}
<!-- <script src="{{ asset('admin/assets/js/form_elements.js') }}" type="text/javascript"></script> -->
<script src="{{ asset('admin/assets/js/scripts.js') }}" type="text/javascript"></script>
<script src="{{ asset('admin/js/dashboard.js') }}" type="text/javascript"></script>


<script type="text/javascript">

</script>
@yield('page-js')
<script src="assets/plugins/pace/pace.min.js" type="text/javascript"></script>
<script src="assets/plugins/jquery/jquery-3.2.1.min.js" type="text/javascript"></script>
<script src="assets/plugins/modernizr.custom.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
<script src="assets/plugins/popper/umd/popper.min.js" type="text/javascript"></script>
<script src="assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="assets/plugins/jquery/jquery-easy.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-unveil/jquery.unveil.min.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-ios-list/jquery.ioslist.min.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-actual/jquery.actual.min.js"></script>
<script src="assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js"></script>
<script type="text/javascript" src="assets/plugins/select2/js/select2.full.min.js"></script>
<script type="text/javascript" src="assets/plugins/classie/classie.js"></script>
<script src="assets/plugins/switchery/js/switchery.min.js" type="text/javascript"></script>
<script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
<script src="assets/plugins/moment/moment.min.js"></script>
<script src="assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="assets/plugins/bootstrap-timepicker/bootstrap-timepicker.min.js"></script>
<script src="assets/plugins/jquery-datatable/media/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-datatable/extensions/TableTools/js/dataTables.tableTools.min.js"
    type="text/javascript"></script>
<script src="assets/plugins/jquery-datatable/media/js/dataTables.bootstrap.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-datatable/extensions/Bootstrap/jquery-datatable-bootstrap.js" type="text/javascript">
</script>
<script type="text/javascript" src="assets/plugins/datatables-responsive/js/datatables.responsive.js"></script>
<script src="assets/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<!-- END VENDOR JS -->
<!-- BEGIN CORE TEMPLATE JS -->
<script src="pages/js/pages.js"></script>
<!-- END CORE TEMPLATE JS -->
<!-- BEGIN PAGE LEVEL JS -->
<script src="assets/js/scripts.js" type="text/javascript"></script>
<!-- END PAGE LEVEL JS -->
<!-- END CORE TEMPLATE JS -->
<!-- BEGIN PAGE LEVEL JS -->
<script src="assets/js/datatables.js" type="text/javascript"></script>
<!-- <script src="assets/js/form_elements.js" type="text/javascript"></script> -->
<script src="assets/js/scripts.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function () {
        $(".mainCat").click(function () {
            var main_id_value = $(this).data('id');
            var main_cat_name = $(this).data('cat_name');
            var main_slug = $(this).data('cat_slug');
            $(".modal-body #main_id").val(main_id_value);
            $(".modal-body #edit_main_cat_name").val(main_cat_name);
            $(".modal-body #edit_main_cat_slug").val(main_slug);
        })
    });
</script>

<script type="text/javascript">
    $(function () {
        $(".subCat").click(function () {
            var sub_id_value = $(this).data('sub_id');
            var sub_cat_name = $(this).data('sub_cat_name');
            var sub_slug = $(this).data('sub_cat_slug');
            $(".modal-body #sub_id").val(sub_id_value);
            $(".modal-body #edit_sub_cat_name").val(sub_cat_name);
            $(".modal-body #edit_sub_cat_slug").val(sub_slug);
        })
    });
</script>
{{-- <style>
    .nav-tabs .show {
      background-color: #10cfbd ;
    }
</style> --}}
<script>
    $(document).ready(function(e) {
        //datatable
        var table = $('#main_category');
        table.dataTable({
            "sDom": "<'top'f<'clear'>><t><'row'<p i>>",
            "destroy": true,
            "scrollCollapse": true,
            "oLanguage": {
                "sLengthMenu": "_MENU_ ",
                "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
            },
            "iDisplayLength": 5
        })


        var table = $('#sub_category');
        table.dataTable({
            "sDom": "<'top'f<'clear'>><t><'row'<p i>>",
            "destroy": true,
            "scrollCollapse": true,
            "oLanguage": {
                "sLengthMenu": "_MENU_ ",
                "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
            },
            "iDisplayLength": 5
        })

        //Date Pickers
        $('#daterangepicker').daterangepicker({
            timePicker: true,
            timePickerIncrement: 30,
            format: 'MM/DD/YYYY h:mm A'
        }, function(start, end, label) {
            console.log(start.toISOString(), end.toISOString(), label);
        });

        //form validation
        $("#user_form").validate();
    });


</script>
<script>
 $(document).ready(function(e) {
        //datatable
        var main_datatable = $('#main_category_table').DataTable({
            "serverSide": true,
            "sDom": "<'top'f<'clear'>><t><'row'<p i>>",
            "destroy": true,
            "scrollCollapse": true,
            "oLanguage": {
                "sLengthMenu": "_MENU_ ",
                "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
            },

            "iDisplayLength": 5,
            "ajax": {
                "url": "{{ route('mainCat.datatable') }}",
                "method": "POST",
                "headers": {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                'data': function(data) {
                    data.main_tab_id = $('#filter_main_tab_id').val();
                    // data.main_tab_name = $('#filter_main_tab_name').val();
                    // data.main_tab_slug = $('#filter_main_tab_slug').val();
                }

            },
            "order": [
                [0, "asc"]
            ],
            "columns": [{
                    data: 'checkbox',
                    name: 'checkbox',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'slug',
                    name: 'slug'
                },
                {
                    data: 'actions',
                    name: 'actions'
                },
            ],
            "columnDefs": [{
                "targets": 0,

                "checkboxes": {
                    "selectRow": true
                }
            }],

            "select": {
                "style": "multi",
                "selector": "td:first-child"
            },
        });
        $(document).on('keyup', '#filter_main_tab_id', function() {
            main_datatable.draw();
        });

    });
   
    $(document).ready(function(e) {
        //datatable
        var table = $('#sub_category_table');
        $.fn.dataTable.ext.errMode = 'none';
        var sub_datatable = table.dataTable({
            "sDom": "<'top'f<'clear'>><t><'row'<p i>>",
            "destroy": true,
            "scrollCollapse": true,
            "oLanguage": {
                "sLengthMenu": "_MENU_ ",
                "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
            },

            "iDisplayLength": 5,
            "ajax": {
                "url": "{{ route('subCat.datatable') }}",
                "method": "POST",
                "headers": {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },

            },
            "order": [
                [0, "asc"]
            ],
            "columns": [{
                    data: 'checkbox',
                    name: 'checkbox',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'slug',
                    name: 'slug'
                },
                {
                    data: 'main_category',
                    name: 'main_category'
                },
                {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false
                },
            ],
            "columnDefs": [{
                "targets": 0,

                "checkboxes": {
                    "selectRow": true
                }
            }],

            "select": {
                "style": "multi",
                "selector": "td:first-child"
            },
        })



    });
    $(document).ready(function(e) {
        //datatable
        var table = $('#child_category_table');
        $.fn.dataTable.ext.errMode = 'none';
        table.dataTable({
            "sDom": "<'top'f<'clear'>><t><'row'<p i>>",
            "destroy": true,
            "scrollCollapse": true,
            "oLanguage": {
                "sLengthMenu": "_MENU_ ",
                "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
            },

            "iDisplayLength": 5,
            "ajax": {
                "url": "{{ route('childCat.datatable') }}",
                "method": "POST",
                "headers": {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },

            },
            "order": [
                [0, "asc"]
            ],
            "columns": [{
                    data: 'checkbox',
                    name: 'checkbox',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'slug',
                    name: 'slug'
                },
                {
                    data: 'main_category',
                    name: 'main_category'
                },
                {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false
                },
            ],
            "columnDefs": [{
                "targets": 0,

                "checkboxes": {
                    "selectRow": true
                }
            }],

            "select": {
                "style": "multi",
                "selector": "td:first-child"
            },
        })



    });


    function deleteCategory(cat_id) {
        $('#category_id').val(cat_id);
    }

    $("#filter_main").select2();
    $(document).on('change', '#filter_main', function() {
        sub_datatable.draw();
    });
</script>


<script>
    $('#main_category_table').on('click', 'input', function() {
        // console.log(this.is(':checked'));
        var isChecked = $(this).prop('checked');

        var id = $(this).val();

        if ($(this).is(':checked')) {
            var urlpost = "{{ route('Cat.addSession') }}";
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
            // $('#delete-button').removeClass('disabled');
        } else {
            var urlpost = "{{ route('Cat.removeSession') }}";
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

            // $('#delete-button').addClass('disabled');
        }

        if (array.length > 0) {
            $('#deleteMain').removeClass('disabled');
        } else {
            $('#deleteMain').addClass('disabled');
        }

    });
</script>
<script>
    $('#sub_category_table').on('click', 'input', function() {
        // console.log(this.is(':checked'));
        var isChecked = $(this).prop('checked');

        var id = $(this).val();

        if ($(this).is(':checked')) {
            var urlpost = "{{ route('Cat.addSession') }}";
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
            $('#delete-button').removeClass('disabled');
        } else {
            var urlpost = "{{ route('Cat.removeSession') }}";
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
            $('#delete-button').addClass('disabled');
        }


    });
</script>
<script>
    $('#child_category_table').on('click', 'input', function() {
        // console.log(this.is(':checked'));
        var isChecked = $(this).prop('checked');

        var id = $(this).val();

        if ($(this).is(':checked')) {
            var urlpost = "{{ route('Cat.addSession') }}";
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
            $('#delete-button').removeClass('disabled');
        } else {
            var urlpost = "{{ route('Cat.removeSession') }}";
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
            $('#delete-button').addClass('disabled');
        }


    });
</script>
<!-- END PAGE LEVEL JS -->
</body>

</html>

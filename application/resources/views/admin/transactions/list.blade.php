@extends('admin.layouts.newMaster')
@section('title', 'Transactions List')
@section('page-css')
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
       .batch-action{
           color: #fff !important;
           background-color: #010267 !important;
           border: 1px solid #f0f0f0 !important;
       }
        .batch-export{
            color: #fff !important;
            background-color: #248c01 !important;
            border: 1px solid #f0f0f0 !important;
        }

        .batch-export:hover,.batch-action:hover {
            background-color: #fafafa!important;
            border: 1px solid rgba(98, 98, 98, 0.27)!important;
            color: #333!important;
        }

        .batch-export.active,.batch-action.active {
            border-color: #e6e6e6!important;
            background: #fff!important;
            color: #333!important;
        }
        .form-control {
            font-family: Montserrat, sans-serif!important;
        }



    </style>
@endsection
@section('content')
    <!-- START CONTAINER FLUID -->
    <div class="container-fluid container-fixed-lg">
        @if (Session::has('success'))
            <div class="alert alert-success">{{ Session::get('success') }}</div>
        @elseif(Session::has('error'))
            <div class="alert alert-danger">{{ Session::get('error') }}</div>
    @endif
    <!-- START card -->
        <div class="card card-default  p-l-20  p-r-20">
            <div class="card-header separator">
                <div class="card-title">
                    <h5><strong>Transactions</strong></h5>
                </div>
            </div>
            <div class="p-b-10">
            <div class="card-body p-t-10 searchFilters">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="from" class="control-label">Vendor</label>
                            <select id="filter_vendor" class="form-control">
                                <option value="">Select Vendor</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}" @if((isset($_GET['vendor'])) && ($_GET['vendor']==$vendor->id))selected @endif>{{ $vendor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="from" class="control-label">From</label>
                            <input type="text" id="from" class="form-control" placeholder="mm-dd-yyyy">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="to" class="control-label">To</label>
                            <input type="text" id="to" class="form-control" placeholder="mm-dd-yyyy">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="date_options" class="control-label">Quick Date</label>
                            <select name="date_options" id="date_options" class="form-control">
                                <option value="">Pick an option</option>
                                <option value="yesterday">Yesterday</option>
                                <option value="today">Today</option>
                                <option value="this_weekdays">This Weekdays</option>
                                <option value="this_whole_week">This Whole Week</option>
                                <option value="this_month">This Month</option>
                                <option value="this_year">This Year</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="ActivateAdvanceSerach" class="control-label">&nbsp;</label>
                            <button type="button" class="btn btn-info form-control" id="ActivateAdvanceSerach">Advance Search</button>
                            <button type="button" class="btn btn-info form-control" id="HideActivateAdvanceSerach" style="display: none;">Hide Advance Search</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body searchFilters m-b-10" id="AdvanceFilters" style="display: none;">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="filter_order_no" class="control-label">Order No</label>
                            <input type="text" class="form-control" id="filter_order_no">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="filter_bar_code" class="control-label">Bar Code</label>
                            <input type="text" class="form-control" id="filter_bar_code">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="filter_register_no" class="control-label">Register No</label>
                            <input type="text" class="form-control" id="filter_register_no">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="filter_float_no" class="control-label">Float No</label>
                            <input type="text" class="form-control" id="filter_float_no">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="filter_operator_id" class="control-label">Operator Id</label>
                            <input type="text" class="form-control" id="filter_operator_id">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="filter_vendor_email" class="control-label">Vendor Email</label>
                            <input type="text" class="form-control" id="filter_vendor_email">
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <table class="table table-hover table-condensed table-responsive-block table-responsive"
                   id="transactionsTable">
                <thead>
                <tr>
                    <th style="width:10%;"></th>
                    <th style="width:10%;">ID</th>
                    <th style="width: 12%;">Transaction Date</th>
                    <th style="width: 12%;">Transaction Time</th>
                    <th style="width: 10%;">Order no</th>
                    <th style="width: 13%;">Vendor</th>
                    <th style="width: 13%;">Vendor Email</th>
                    <th style="width: 10%;">Amount</th>
                    <th style="width: 20%;">Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr></tr>
                </tbody>
            </table>
            <div class="col-xs-2 select-all-button p-b-10">
                <div class="p-t-10">
                    <div style="float: left;">
                    <button type="button" class="btn btn-info" id="selectAllTransactions">Select All</button>
                    <button type="button" class="btn btn-info" id="deselectAllTransactions"> De-Select All</button>
                    <a onclick="hideTransactions()" href="javascript:void(0);" class="btn btn-success"
                       id="hideTransactions">Hide Transactions</a>
                    <a href="<?php echo url('/admin/transactions/visible'); ?>" class="btn btn-danger"
                       id="showTransactions">Show Transactions</a>
                    <button type="button" target="_blank" class="btn btn-warning batch-action" id="footer-buttons-1" onclick="savePDF()">Batch Action1</button>
                    <button type="button" target="_blank" class="btn btn-warning batch-action" id="footer-buttons-2" onclick="savePDF()">Batch Action2</button>
                     <button type="button" onclick="bulkXLSTransactions()" id="footer-buttons-3" class="btn btn-success batch-export" ><i class="icon-file-excel" href="#"></i>Export XLS</button>
                     <button type="button" onclick="printAllTransactions()" id="footer-buttons-4" class="btn btn-success batch-export" target="_blank "><i class="glyphicon glyphicon-print"></i> PRINT</button>
                     <button type="button" onclick="savePDF()" id="footer-buttons-5" class="btn btn-success batch-export" target="_blank"><i class="icon-file-excel" href="#"></i>Save PDF</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- END card -->
    </div>
    <!-- END CONTAINER FLUID -->
@endsection
@section('page-js')
    <script>
        $("#ActivateAdvanceSerach").click(function () {
            $("#AdvanceFilters").show();
            $("#HideActivateAdvanceSerach").show();
            $("#ActivateAdvanceSerach").hide()
        });
        $("#HideActivateAdvanceSerach").click(function () {
            $("#AdvanceFilters").hide();
            $("#HideActivateAdvanceSerach").hide();
            $("#ActivateAdvanceSerach").show();
        });

        function hideTransactions() {
            var ids = [];
            $.each($("input[name='pdr_checkbox[]']:checked"), function () {
                ids.push($(this).val());
            });
            if (ids.length > 0) {
                var CSRF_TOKEN = $('input[name="_token"]').attr('value');
                $.ajax({
                    type: "POST",
                    dataType: 'JSON',
                    url: '<?php echo url('admin/hide-all/transactions'); ?>',
                    data: {_token: CSRF_TOKEN, transaction_ids: ids},
                    success: function (data) {

                    },
                    complete: function () {
                        window.location = '<?=url('admin/transactions');?>';
                    }
                });
            }
        }

        function savePDF() {
            var newWin = window.open();
            var ids = [];
            $.each($("input[name='pdr_checkbox[]']:checked"), function () {
                ids.push($(this).val());
            });
            if (ids.length > 0) {
                var CSRF_TOKEN = $('input[name="_token"]').attr('value');
                $.ajax({
                    type: "POST",
                    dataType: 'html',
                    url: '<?php echo url('admin/transactions/print-list'); ?>',
                    data: {_token: CSRF_TOKEN, transaction_ids: ids},
                    success: function (html) {
                    }
                });
            }
        }

        function printAllTransactions() {
            var newWin = window.open();
            var ids = [];
            $.each($("input[name='pdr_checkbox[]']:checked"), function () {
                ids.push($(this).val());
            });
            if (ids.length > 0) {
                var CSRF_TOKEN = $('input[name="_token"]').attr('value');
                $.ajax({
                    type: "POST",
                    dataType: 'html',
                    url: '<?php echo url('admin/transactions/print-all'); ?>',
                    data: {_token: CSRF_TOKEN, transaction_ids: ids},
                    success: function (html) {
                        newWin.document.write(html);
                        newWin.document.close();
                        newWin.focus();
                        newWin.print();
                        newWin.close();
                    }
                });
            }
        }

        function bulkXLSTransactions()
        {
            var download = function(content, fileName, mimeType) {
                var a = document.createElement('a');
                mimeType = mimeType || 'application/octet-stream';

                if (navigator.msSaveBlob) { // IE10
                    navigator.msSaveBlob(new Blob([content], {
                        type: mimeType
                    }), fileName);
                } else if (URL && 'download' in a) { //html5 A[download]
                    a.href = URL.createObjectURL(new Blob([content], {
                        type: mimeType
                    }));
                    a.setAttribute('download', fileName);
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                } else {
                    location.href = 'data:application/octet-stream,' + encodeURIComponent(content); // only this mime type is supported
                }
            }
            var newWin = window.open();
            var ids = [];
            $.each($("input[name='pdr_checkbox[]']:checked"), function () {
                ids.push($(this).val());
            });
            if (ids.length > 0) {
                var CSRF_TOKEN = $('input[name="_token"]').attr('value');
                $.ajax({
                    type: "POST",
                    dataType: 'html',
                    url: '<?php echo url('admin/transactions/export-all'); ?>',
                    data: {_token: CSRF_TOKEN, transaction_ids: ids},
                    success: function (html) {
                        download(html, 'dowload.csv', 'text/csv;encoding:utf-8');
                    }
                });
            }
        }

        $(document).ready(function () {
            var array = [];
            $("#selectAllTransactions").on("click", function (e) {
                var table = $("#transactionsTable");
                var boxes = $('input:checkbox', table);
                $.each($('input:checkbox', table), function () {

                    $(this).parent().addClass('checked');
                    $(this).prop('checked', 'checked');

                });

                $('#hideTransactions').prop('disabled', false);
                $('#deselectAllTransactions').prop('disabled', false);
            });

            $("#deselectAllTransactions").on("click", function (e) {
                var table = $("#transactionsTable");
                var boxes = $('input:checkbox', table);
                $.each($('input:checkbox', table), function () {

                    $(this).parent().removeClass('checked');
                    $(this).prop('checked', false);

                });
                $('#hideTransactions').prop('disabled', true);
            });

        });

        // Check all
        $(document).ready(function (e) {
            var table = $('#transactionsTable');
            $.fn.dataTable.ext.errMode = 'none';
            var trans_datatable = table.DataTable({
                "serverSide": true,
                "sDom": '<"H"lfr>t<"F"ip>',
                "destroy": true,
                "pageLength": 10,
                "sPaginationType": "full_numbers",
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                "ajax": {
                    "url": "{{ route('transactions.datatable') }}",
                    "method": "POST",
                    'data': function (data) {
                        // data.transaction_date = $('#filter_transaction_date').val();
                        data.vendor='<?=(isset($_GET['vendor']))?$_GET['vendor']:''?>';
                        data.vendor_email = $('#filter_vendor_email').val();
                        data.order_no = $('#filter_order_no').val();
                        data.vendor_id = $('#filter_vendor').val();
                        data.from = $('#from').val();
                        data.to = $('#to').val();
                        data.date_option = $('#date_options').val();
                        data.year_to_date = $('#year_to_date').val();
                    }
                },
                "order": [[0, "asc"]],
                "columns": [
                    // {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'checkboxes', name: 'checkboxes', orderable: false, searchable: false},
                    {data: 'id', name: 'id'},
                    {data: 'transaction_date', name: 'transaction_date'},
                    {data: 'transaction_time', name: 'transaction_time'},
                    // {data: 'total', name: 'total'},
                    {
                        data: 'order_no', name: 'order_no', fnCreatedCell: function (nTd, sData, oData, iRow, iCol) {
                            $(nTd).html("<a style='color: #0090d9' href='"+BASE_URL+"/admin/transactions/" + oData.id + "'>" + oData.order_no + "</a>");
                        }
                    },
                    {
                        data: 'vendor_name', name: 'vendor_name', fnCreatedCell: function (nTd, sData, oData, iRow, iCol) {
                            $(nTd).html("<a style='color: #0090d9' href='"+BASE_URL+"/admin/vendors/" + oData.vendor_id + "'>" + oData.vendor_name + "</a>");
                        }
                    },
                    {data: 'vendor_email', name: 'vendor_email'},
                    {
                        data: 'total', fnCreatedCell: function (nTd, sData, oData, iRow, iCol) {
                            $(nTd).html("$" + oData.total);
                        }
                    },
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                "language": {
                    "info": "Showing _START_ to _END_ of _TOTAL_ records",
                }
            });
            $(document).on('keyup', '#filter_order_no', function () {
                trans_datatable.draw();
            });
            $('#filter_vendor_email').keyup(function () {
                trans_datatable.draw();
            });
            $("#filter_vendor").select2();
            $(document).on('change', '#filter_vendor', function () {
                trans_datatable.draw();
            });

            $('#from').change(function () {
                trans_datatable.draw();
            });
            $('#to').change(function () {
                trans_datatable.draw();
            });
            $('#date_options').change(function () {
                trans_datatable.draw();
            });
            $('#year_to_date').change(function () {
                trans_datatable.draw();
            });
            //Date Pickers
            $('#from').datepicker({
                format: 'mm-dd-yyyy'
            });

            $('#to').datepicker({
                format: 'mm-dd-yyyy'
            });
        });

        function modalSend(trans_id) {
            $('#trans_id').val(trans_id);
        }

        function modalArchive(trans_id) {
            $('#trans_id_Arch').val(trans_id);
        }

        function modalHide(trans_id) {
            $('#trans_id_hide').val(trans_id);
        }
    </script>
    <script>
        $(document).ready(function () {
            $('#sendInvoice').on('click', function (e) {
                $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
                $.ajax({
                    type: "POST",
                    url: '{{ route('transactions.notify')}}',
                    data: {
                        'trans_id': $("#trans_id").val(),
                    },
                    success: function (data) {
                        $("#successMessage").show();
                    }
                });
                return false;
            });
        });
    </script>

<div id="send" class="modal fade" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title"><i class="fa fa-envelope"></i> &nbsp;<strong>{{ __('E-Mail Receipt') }}</strong></h3>
                </div>
                <form method="post" action="{{ route('transactions.notify.list') }}">
                 {{ csrf_field() }}
                <div class="modal-body">
                    <br>
                    <h5>{{ __('Share this Receipt via email to the address below') }}</h5>
                    <input type="text" placeholder="email@domain.com" class="form-control" name="send_email">
                    <div id="successMessage" style="display:none;" class="alert alert-success" role="alert"> Receipt successfully sent.
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="trans_id" value="" id="trans_id">
                    <div class="text-center">
                        <button type="submit" class="btn btn-success" >{{ __('Send') }}</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal" >{{ __('Cancel') }}</button>
                    </div>
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
                <form method="POST" action=" {{url('admin/archive/transactions')}} ">
                    <div class="modal-footer">
                        {{csrf_field()}}
                        <input type="hidden" name="trans_id" value="" id="trans_id_Arch">
                        <button type="submit" class="btn btn-danger">Archive</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="hide" class="modal fade" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Hide Transaction</h4>
                </div>
                <div class="modal-body">
                    <p>Are sure you want to Hide Transaction ?</p>
                </div>
                <form method="POST" action=" {{url('admin/hide/transactions')}} ">
                    <div class="modal-footer">
                        {{csrf_field()}}
                        <input type="hidden" name="trans_id" value="" id="trans_id_hide">
                        <button type="submit" class="btn btn-danger">Hide</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

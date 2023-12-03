@extends('admin.layouts.master')
@section('title', 'Add Budget Receipt List')


@section('page-css')
    <style>
        .dataTables_filter {
            display: none;
        }

        #ActivateAdvanceSerach,
        #HideActivateAdvanceSerach {
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

        .dataTables_wrapper .dataTables_paginate ul>li {
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

        .batch-export:hover,
        .batch-action:hover {
            background-color: #fafafa !important;
            border: 1px solid rgba(98, 98, 98, 0.27) !important;
            color: #333 !important;
        }

        .batch-export.active,
        .batch-action.active {
            border-color: #e6e6e6 !important;
            background: #fff !important;
            color: #333 !important;
        }

        .form-control {
            font-family: Montserrat, sans-serif !important;
        }

    </style>
@endsection

@section('content')

    <!-- START CONTAINER FLUID -->
    <div class="container-fluid container-fixed-lg">
        <!-- START card -->
        <div class="card card-default  p-l-20  p-r-20">
            <div class="card-header separator">
                <div class="card-title">
                    <h5><strong>Add Receipts</strong></h5>
                </div>
            </div>
            <div class="p-b-10">
                <div class="card-body p-t-10 searchFilters">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group" style="display: inline-block">
                                <label for="filter_order_no">Order No</label>
                                <input type="text" class="form-control" id="filter_order_no">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group" style="display: inline-block">
                                <label for="filter_vendor">Vendor</label>
                                <input type="text" class="form-control" id="filter_vendor">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="from" class="control-label">From</label>
                                <input type="text" id="from" class="form-control"  placeholder="mm-dd-yyyy">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="to" class="control-label">To</label>
                                <input type="text" id="to" class="form-control"  placeholder="mm-dd-yyyy">
                            </div>
                        </div>
                        <div class="col-md-2">
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="ActivateAdvanceSerach" class="control-label">&nbsp;</label>
                                <button type="button" class="btn btn-info form-control" id="ActivateAdvanceSerach">Advance
                                    Search</button>
                                <button type="button" class="btn btn-info form-control" id="HideActivateAdvanceSerach"
                                    style="display: none;">Hide Advance Search</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body searchFilters m-b-10" id="AdvanceFilters" style="display: none;">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date_options" class="control-label">Date Options</label>
                                <select name="date_options" id="date_options" class="form-control">
                                    <option value="">Pick a date</option>
                                    <option value="yesterday">Yesterday</option>
                                    <option value="today">Today</option>
                                    <option value="this_weekdays">This Weekdays</option>
                                    <option value="this_whole_week">This Whole Week</option>
                                    <option value="this_month">This Month</option>
                                    <option value="this_year">This Year</option>
                                </select>
                            </div>
                        </div>

                    </div>
                </div>
                <br>
                <div class="pull-right">
                    <a href="{{ route('budgets.add-receipts') }}" style="vertical-align: middle;"
                        class="btn btn-info btn-md  disabled" id="addBudget">Add to Budget</a>
                </div>
            </div>
            <table class="table table-hover table-condensed table-responsive-block table-responsive"
                id="budgetReceiptsTable">
                <thead>
                    <tr>
                        <th style="width:5%;">#</th>
                        <th style="width:10%;">ID</th>
                        <th style="width: 12%;">Transaction Date</th>
                        <th style="width: 12%;">Transaction Time</th>
                        <th style="width: 10%;">Order no</th>
                        <th style="width: 13%;">Bar QR Code</th>
                        <th style="width: 13%;">Register No</th>
                        <th style="width: 13%;">Float No</th>
                        <th style="width: 13%;">Operator Id</th>
                        <th style="width: 13%;">Vendor</th>
                        <th style="width: 13%;">Vendor Email</th>
                        <th style="width: 10%;">Amount</th>
                       <!--  <th style="width: 20%;">Actions</th> -->
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            <div class="pull-left p-t-10 p-b-20">
                <a href="{{ route('budgets.add-receipts') }}" style="vertical-align: middle;"
                        class="btn btn-info btn-md  disabled" id="addBudgetTwo">Add to Budget</a>
        </div>
        </div>
    </div>
    <!-- END card -->
    </div>
    <div>


        <br>
    </div>
    <!-- END CONTAINER FLUID -->

@endsection

@section('page-js')

    <script>
            $("#ActivateAdvanceSerach").click(function() {
            $("#AdvanceFilters").show();
            $("#HideActivateAdvanceSerach").show();
            $("#ActivateAdvanceSerach").hide()
        });
        $("#HideActivateAdvanceSerach").click(function() {
            $("#AdvanceFilters").hide();
            $("#HideActivateAdvanceSerach").hide();
            $("#ActivateAdvanceSerach").show();
        });

        $(document).ready(function(e) {
            var table = $('#budgetReceiptsTable');
            $.fn.dataTable.ext.errMode = 'none';
            var trans_datatable = table.DataTable({
                "dom": "Bfrtip",
                "serverSide": true,
                "sDom": '<"H"lfr>t<"F"ip>',
                "destroy": true,
                "pageLength": 10,
                "sPaginationType": "full_numbers",
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                "ajax": {
                    "url": "{{ route('AddBudgetReceipts.datatable') }}",
                    "method": "POST",
                    'data': function(data) {
                        data.order_no = $('#filter_order_no').val();
                        data.vendor_name = $('#filter_vendor').val();
                        data.from = $('#from').val();
                        data.to = $('#to').val();
                        data.date_option = $('#date_options').val();
                        data.year_to_date = $('#year_to_date').val();
                    }
                },
                "order": [
                    [0, "asc"]
                ],
                "columns": [
                    // {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {
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
                        data: 'transaction_date',
                        name: 'transaction_date'
                    },
                    {
                        data: 'transaction_time',
                        name: 'transaction_time'
                    },
                    {
                        data: 'order_no',
                        name: 'order_no',
                        fnCreatedCell: function(nTd, sData, oData, iRow, iCol) {
                            $(nTd).html(
                                "<a style='color: #0090d9' href='/admin/envelopes/AddReceipts/" +
                                oData.id + "'>" + oData.order_no + "</a>");
                        }
                    },
                    {
                        data: '',
                        name: ''
                    },
                    {
                        data: '',
                        name: ''
                    },
                    {
                        data: '',
                        name: ''
                    },
                    {
                        data: '',
                        name: ''
                    },
                    {
                        data: 'vendor_name',
                        name: 'vendor_name'
                    },
                    {
                        data: 'vendor_email',
                        name: 'vendor_email'
                    },
                    {
                         data: 'total', fnCreatedCell: function (nTd, sData, oData, iRow, iCol) {
                            $(nTd).html("$" + oData.total);
                        }
                    },
                    // {
                    //     data: 'actions',
                    //     name: 'actions',
                    //     orderable: false,
                    //     searchable: false
                    // },
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
                "buttons": [
                    "selectAll",
                    "selectNone",
                ]
            });
            $(document).on('keyup', '#filter_order_no', function() {
                trans_datatable.draw();
            });
            $('#filter_vendor').keyup(function() {
                trans_datatable.draw();
            });
            $('#from').change(function() {
                trans_datatable.draw();
            });
            $('#to').change(function() {
                trans_datatable.draw();
            });
            $('#date_options').change(function() {
                trans_datatable.draw();
            });
            $('#year_to_date').change(function() {
                trans_datatable.draw();
            });

            //Date Pickers
            $('#from').datepicker({
                format: 'mm-dd-yyyy'
            });

            $('#to').datepicker({
                format: 'mm-dd-yyyy'
            });
            var array = [];
            $('.buttons-select-all').on('click', function() {
                array = [];
                var table = $("#budgetReceiptsTable");
                var boxes = $('input:checkbox', table);
                $.each($('input:checkbox', table), function() {
                    $(this).parent().addClass('checked');
                    $(this).prop('checked', 'checked');
                    var id = $(this).val();
                    array.push(id);
                });
                var urlpost = "{{ route('AddBudgetReceipts.bulkSession') }}";

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

                $('#addEnvelope').removeClass('disabled', false);
                $('#addBudget').removeClass('disabled', false);
                for ($i = 0; $i < array.length; $i++) {
                    $('#form-session').append(
                        $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', 'envelope_id[]')
                        .val(array[$i])
                    );
                }

            });


            $('.buttons-select-none').on('click', function() {
                var table = $("#budgetReceiptsTable");
                var boxes = $('input:checkbox', table);
                $.each($('input:checkbox', table), function() {
                    $(this).parent().removeClass('checked');
                    $(this).prop('checked', false);
                    var id = $(this).val();
                    array.splice(array.indexOf(id), 1);
                });
                var urlpost = "{{ route('AddBudgetReceipts.clearSession') }}";

                $.ajax({
                    type: 'POST',
                    url: urlpost,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(data) {

                    }
                });
                $('#addEnvelope').addClass('disabled');
                $('#addBudget').addClass('disabled');
            });

            $('#budgetReceiptsTable').on('click', 'input', function() {
                // console.log(this.is(':checked'));
                var isChecked = $(this).prop('checked');

                var id = $(this).val();
                if ($(this).is(':checked')) {
                    array.push(id);
                    var urlpost = "{{ route('AddBudgetReceipts.addSession') }}";
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
                    var urlpost = "{{ route('AddBudgetReceipts.removeSession') }}";
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
                    $('#addEnvelope').removeClass('disabled');
                    $('#addBudget').removeClass('disabled');
                    $('#addBudgetTwo').removeClass('disabled');
                    $('.buttons-select-all').prop('disabled', false);
                } else {
                    $('#addEnvelope').addClass('disabled');
                    $('#addBudget').addClass('disabled');
                    $('#addBudgetTwo').addClass('disabled');
                    $('.buttons-select-none').prop('disabled', true);

                }

            });

        });

    </script>

    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" type="text/javascript"></script>
    <script src="{{ asset('admin/assets/plugins/jquery-datatable/media/js/dataTables.bootstrap.js') }}"
        type="text/javascript"></script>
    <script src="https://cdn.datatables.net/select/1.3.1/js/dataTables.select.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script>
    <script src="http://cdn.datatables.net/plug-ins/1.10.15/dataRender/datetime.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>

@endsection

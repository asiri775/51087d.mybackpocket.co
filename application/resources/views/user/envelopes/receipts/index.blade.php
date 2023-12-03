@extends('user.layouts.master')
@section('title', 'Add Receipt List')


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
                        <div class="col-md-3">
                            <div class="form-group" style="display: inline-block">
                                <label for="filter_vendor">Store</label>
                                <input type="text" class="form-control" id="filter_vendor">
                            </div>
                        </div>
                       
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="from" class="control-label">From</label>
                                <input type="text" id="from" placeholder="mm-dd-yyyy" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="to" class="control-label">To</label>
                                <input type="text" id="to" placeholder="mm-dd-yyyy" class="form-control">
                            </div>
                        </div>
                       
                        <div class="col-md-3">
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_vendor_email" class="control-label">Store Email</label>
                                <input type="text" id="filter_vendor_email"  class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="pull-right">
                    <a href="{{ route('user.add.receipt.add') }}" style="vertical-align: middle;"
                        class="btn btn-info btn-md  disabled" id="addEnvelopeExisting">Add to Envelope</a>
                </div>
            </div>



            <table class="table table-hover table-condensed table-responsive-block table-responsive" id="transactionsTable">
                <thead>
                    <tr>
                        <th class="v-align-middle" style="width:5%;">#</th>
                        <th class="v-align-middle" style="width:10%;">ID</th>
                        <th class="v-align-middle" style="width: 12%;">Transaction Date</th>
                        <th class="v-align-middle" style="width: 10%;">Order no</th>
                        <th class="v-align-middle" style="width: 13%;">Vendor</th>
                        <th class="v-align-middle" style="width: 13%;">Vendor Email</th>
                        <th class="v-align-middle" style="width: 10%;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            <div class="pull-left p-t-10 p-b-20">
                <a href="{{ route('user.add.receipt.add') }}" style="vertical-align: middle;"
                    class="btn btn-info btn-md  disabled" id="addEnvelopeExistingTwo">Add to Envelope</a>
            </div>
        </div>
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
            var table = $('#transactionsTable');
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
                    "url": "{{ route('user.envelope.add.receipt.datatable') }}",
                    "method": "POST",
                    'data': function(data) {
                        data.order_no = $('#filter_order_no').val();
                        data.vendor = $('#filter_vendor').val();
                        data.vendor_email = $('#filter_vendor_email').val();
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
                        data: 'transaction_time',
                        name: 'transaction_time'
                    },
                    {
                        data: 'order_no',
                        name: 'order_no',
                       
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
                        data: 'total',
                        name: 'total'
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
                "buttons": [
                    "selectAll",
                    "selectNone",
                ]
            });
            $(document).on('keyup', '#filter_order_no', function() {
                trans_datatable.draw();
            });
            $(document).on('keyup', '#filter_vendor', function() {
                trans_datatable.draw();
            });
            $(document).on('keyup', '#filter_vendor_email', function() {
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
                var table = $("#transactionsTable");
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

                $('#addEnvelope').removeClass('disabled', false);
                $('#addEnvelopeExisting').removeClass('disabled', false);
                $('#addEnvelopeExistingTwo').removeClass('disabled', false);

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
                var table = $("#transactionsTable");
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
                $('#addEnvelope').addClass('disabled');
                $('#addEnvelopeExisting').addClass('disabled');
                $('#addEnvelopeExistingTwo').addClass('disabled');
            });

            $('#transactionsTable').on('click', 'input', function() {
                // console.log(this.is(':checked'));
                var isChecked = $(this).prop('checked');

                var id = $(this).val();
                if ($(this).is(':checked')) {
                    array.push(id);
                    var urlpost = "{{ route('user.add.receipt.addSession') }}";
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
                    var urlpost = "{{ route('user.add.receipt.removeSession') }}";
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
                    $('#addEnvelopeExisting').removeClass('disabled');
                    $('#addEnvelopeExistingTwo').removeClass('disabled');
                    $('.buttons-select-all').prop('disabled', false);
                } else {
                    $('#addEnvelope').addClass('disabled');
                    $('#addEnvelopeExisting').addClass('disabled');
                    $('#addEnvelopeExistingTwo').addClass('disabled');
                    $('.buttons-select-none').prop('disabled', true);

                }

            });

        });

    </script>


@endsection

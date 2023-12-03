@extends('admin.layouts.masterToTransactions')

@section('title', 'Manage Reports')

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

        #envelopeReportTable_info {
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
                    <h5><strong>Manage Reports</strong></h5>
                </div>
            </div>
            <div class="p-b-10">
                <div class="card-body p-t-10 searchFilters">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group" style="display: inline-block">
                                <label for="filter_envelope_name">Envelope Name</label>
                                <input type="text" class="form-control" id="filter_envelope_name">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group" style="display: inline-block">
                                <label for="filter_user">User</label>
                                <input type="text" class="form-control" id="filter_user">
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="categories" class="control-label">Categories</label>
                                <select name="categories" id="categories" class="form-control">
                                    <option value="">Pick an option</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                <table class="table table-hover table-condensed table-responsive-block table-responsive"
                    id="envelopeReportTable">
                    <thead>
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 5%;">ID</th>
                            <th style="width: 15%;">Name</th>
                            <th style="width: 20%;">Category</th>
                            <th style="width: 20%;">User</th>
                            <th style="width: 20%;">Create Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- END card -->
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
            var table = $('#envelopeReportTable');
            $.fn.dataTable.ext.errMode = 'none';
            var trans_datatable = table.DataTable({
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
                    "url": "{{ route('envelopesReports.datatable') }}",
                    "method": "POST",
                    'data': function(data) {
                        data.envelope_name = $('#filter_envelope_name').val();
                        data.user_name = $('#filter_user').val();
                        data.from = $('#from').val();
                        data.to = $('#to').val();
                        data.date_option = $('#date_options').val();
                        data.category_option = $('#categories').val();
                        data.year_to_date = $('#year_to_date').val();
                    }
                },
                "order": [
                    [0, "asc"]
                ],
                "columns": [
                    // {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {
                        data: '',
                        name: '', orderable: false, searchable: false
                    },
                    {
                        data: 'id',
                        name: 'id', orderable: false, searchable: false
                    },
                    {
                        data: 'envelope_name',
                        name: 'envelope_name',
                        fnCreatedCell: function(nTd, sData, oData, iRow, iCol) {
                            $(nTd).html("<a style='color: #0090d9' href='envelopes/preview/" +
                                oData
                                .id + "'>" + oData.envelope_name + "</a>");
                        }
                    },
                    {
                        data: 'envelope_category',
                        name: 'envelope_category',orderable: false, searchable: false
                    },
                    {
                        data: 'user_name',
                        name: 'user_name',
                        fnCreatedCell: function(nTd, sData, oData, iRow, iCol) {
                            $(nTd).html("<a style='color: #0090d9' href='reports/users/" + oData
                                .user_id + "'>" + oData.user_name + "</a>");
                        }
                    },

                    {
                        data: 'envelope_date',
                        name: 'envelope_date',orderable: false, searchable: false
                    },
                ],
                "language": {
                    "info": "Showing _START_ to _END_ of _TOTAL_ records",
                }
            });
            $(document).on('keyup', '#filter_envelope_name', function() {
                trans_datatable.draw();
            });
            $('#filter_user').keyup(function() {
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

            $('#categories').change(function() {
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
        });

        function modalSend(trans_id) {
            $('#trans_id').val(trans_id);
        }

    </script>
    <script>
        $(document).ready(function() {
            $('#sendInvoice').on('click', function(e) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: '{{ route('transactions.notify') }}',
                    data: {
                        'trans_id': $("#trans_id").val(),
                    },
                    success: function(data) {
                        $("#successMessage").show();
                    }
                });
                return false;
            });
        });

    </script>

@endsection

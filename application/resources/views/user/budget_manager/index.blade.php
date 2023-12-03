@extends('user.layouts.master')
@section('title', 'Budget Manager')
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
    <?php
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Input;
    ?>
    <script type="text/javascript">
        function closePrint() {
            document.body.removeChild(this.__container__);
        }

        function setPrint() {
            this.contentWindow.__container__ = this;
            this.contentWindow.onbeforeunload = closePrint;
            this.contentWindow.onafterprint = closePrint;
            this.contentWindow.focus(); // Required for IE
            this.contentWindow.print();
        }

        function printPage(sURL) {
            var oHiddFrame = document.createElement("iframe");
            oHiddFrame.onload = setPrint;
            oHiddFrame.style.visibility = "hidden";
            oHiddFrame.style.position = "fixed";
            oHiddFrame.style.right = "0";
            oHiddFrame.style.bottom = "0";
            oHiddFrame.src = sURL;
            document.body.appendChild(oHiddFrame);
        }
    </script>
    <!-- START PAGE CONTENT -->
    <div class=" container-fluid   container-fixed-lg">
        <!-- START JUMBOTRON -->
        @if (Session::has('success'))
            <div class="alert alert-success">{{ Session::get('success') }}</div>
        @elseif(Session::has('error'))
            <div class="alert alert-danger">{{ Session::get('error') }}</div>
        @endif
        <!-- END JUMBOTRON -->
        <!-- START CONTAINER FLUID -->
        {{-- <div class=" container-fluid   container-fixed-lg"> --}}
        <!-- START card -->
        <div class="card card-default  p-l-20  p-r-20">
            <div class="card-header separator">
                <div class="card-title">
                    <h5><strong>Budget Manager</strong></h5>
                    <p
                        style="font-size:12px; line-height: 16px; color: #626262; font-family:Segoe UI; text-transform:lowercase;">
                        Budgets are a collection of transactions that can be tracked and monitored based on a predetermined
                        Budget Amount. For example, if you want to set a budget of $500.00 for “Moving Expenses’, simply add
                        Receipts and/or Envelopes to this
                        budget and you will receive a notification if the total amount of envelopes and/or receipts are
                        nearing and exceeding the budget amount.</p>
                </div>
            </div>
            <div class="card-body p-t-20">
                <form action="{{ route('user.budget.manager.create') }}" id="user_budget" method="POST">
                    {{ csrf_field() }}
                    <div class="row justify-content-left">
                        <div class="col-md-10">
                            <div class="form-group" style="float:none;">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label>New Budget Name</label>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" name="name" id="name" required>
                                    </div>
                                    <div class="col-md-1"></div>
                                    <div class="col-md-2">
                                        <label>Budget Category</label>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="category" id="category" class="form-control" required>
                                            <option value="">Select a Category</option>
                                            @foreach ($categories as $category)
                                                @if ($category->role == 'main')
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @else
                                                    <option value="{{ $category->id }}">
                                                        &nbsp;&nbsp;&nbsp;&nbsp;{{ $category->name }}</option>
                                                @endif

                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2">
                                        <label>Target Budget Value $</label>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" class="form-control" name="value" id="value" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-complete">Add Budget</button>
                        </div>
                    </div>
                </form>
            </div>
            <hr>
            <div class="p-b-10">
                <div class="card-body p-t-10 searchFilters">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="filter_budget_name" class="control-label">Budget Name</label>
                                <input type="text" id="filter_budget_name" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_category" class="control-label">Category</label>
                                <select id="filter_category" class="form-control">
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
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
                        {{-- <div class="col-md-3">
                            <div class="form-group">
                                <label for="ActivateAdvanceSerach" class="control-label">&nbsp;</label>
                                <button type="button" class="btn btn-info form-control" id="ActivateAdvanceSerach">Advance
                                    Search</button>
                                <button type="button" class="btn btn-info form-control" id="HideActivateAdvanceSerach"
                                    style="display: none;">Hide Advance Search</button>
                            </div>
                        </div> --}}
                    </div>
                </div>
                <div class="card-body searchFilters m-b-10" id="AdvanceFilters" style="display: none;">

                </div>

            </div>
            <div class="pull-right">
                <a href="{{ route('user.reports.myBudgetsReports', ['time' => 'all_time']) }}" class="btn btn-primary">Budget Report
                </a>
            </div>
            <hr>
            <div class="widget-11-2-table p-t-20">
                <table class="table table-hover table-condensed table-responsive-block
                table-responsive"
                    id="tableEnvelopes">
                    <thead>
                        <tr>
                            <th style="width: 5%; "></th>
                            <th style="width: 5%; ">ID</th>
                            <th style="width: 10%; ">Budget Name</th>
                            <th style="width: 10%; ">Category
                            </th>
                            <th style="width: 10%; ">Date</th>
                            <th style="width: 10%; ">Target Value</th>
                            <th style="width: 10%; ">Current Value</th>
                            <th style="width: 10%; ">Variance</th>
                            <th style="width: 30%; ">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- END PAGE CONTENT WRAPPER -->

    <div id="send" class="modal fade" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title"><i class="fa fa-envelope"></i>
                        &nbsp;<strong>{{ __('Send Envelope Email') }}</strong></h3>
                </div>
                <form method="post" action="{{ route('envelopes.notify') }}">
                    {{ csrf_field() }}
                    {{ method_field('POST') }}
                    <div class="modal-body">
                        <br>
                        <p>{{ __('Are sure you want to send email envelope ?') }}</p>
                        <input type="text" placeholder="email@domain.com" class="form-control" name="send_email"
                            id="send_email">
                        <input type="hidden" name="envelope_id" id="envelope_id">
                        <div id="successMessage" style="display:none;" class="alert alert-success" role="alert"> Invoice
                            successfully sent.</div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">{{ __('Send') }}</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- END PAGE CONTAINER -->
@endsection

@section('page-js')
    <!-- BEGIN VENDOR JS -->

    <script>
        $(document).ready(function(e) {
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
            var table = $('#tableEnvelopes');
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
                    "url": "{{ route('user.budget.datatable') }}",
                    "method": "POST",
                    'data': function(data) {
                        data.budget_name = $('#filter_budget_name').val();
                        data.category_id = $('#filter_category').val();
                        data.from = $('#from').val();
                        data.to = $('#to').val();
                        data.date_option = $('#date_options').val();
                        data.year_to_date = $('#year_to_date').val();
                    }
                },
                "order": [
                    [0, "asc"]
                ],
                "columns": [{
                        data: 'checkboxes',
                        name: 'checkboxes',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name', name: 'name', fnCreatedCell: function (nTd, sData, oData, iRow, iCol) {
                            $(nTd).html("<a style='color: #0090d9' href='/user/budget-manager/preview/" + oData.id + "'>" + oData.name + "</a>");
                        }
                    },
                   
                    {
                        data: 'category_name',
                        name: 'category_name'
                    },
                    {
                        data: 'budget_date',
                        name: 'budget_date'
                    },
                    {
                        data: 'target_budget_value',
                        fnCreatedCell: function(nTd, sData, oData, iRow, iCol) {
                            $(nTd).html("$" + oData.target_budget_value);
                        }
                    },
                    {
                        data: 'current_value',
                        fnCreatedCell: function(nTd, sData, oData, iRow, iCol) {
                            $(nTd).html("$" + oData.current_value);
                        }
                    },
                    {
                        data: 'variance',
                        name: 'variance'
                    },


                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    },
                ],
                "language": {
                    "info": "Showing _START_ to _END_ of _TOTAL_ records",
                }
            });

            $('#filter_budget_name').keyup(function() {
                trans_datatable.draw();
            });
            $("#filter_category").select2();
            $(document).on('change', '#filter_category', function() {
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

        });
    </script>

@endsection

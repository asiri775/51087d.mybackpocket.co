@extends('admin.layouts.newMaster')
@section('title', 'Sales')
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
    <div class="container-fluid container-fixed-lg">
        <div class="card card-default">
            <div class="card-header">
                <div class="card-title">
                    <h5><strong>Sales</strong></h5>
                </div>
            </div>
            <div class="row px-4">
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
                                <label for="filter_product" class="control-label">Product</label>
                                <input type="text" class="form-control" id="filter_product">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="filter_bar_code" class="control-label">Price</label>
                                <input type="text" class="form-control" id="filter_price">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="filter_register_no" class="control-label">Quantity</label>
                                <input type="text" class="form-control" id="filter_quantity">
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="card-body">
                <table class="table table-hover table-condensed table-responsive" id="salesDatatable">
                    <thead>
                    <tr>
                        <th>Transaction</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Date</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
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

        $(document).ready(function (e) {
            var table = $('#salesDatatable');
            $.fn.dataTable.ext.errMode = 'none';
            var sales_datatable = table.DataTable({
                "columnDefs": [
                    { "width": "20%", "targets": 0 },
                    { "width": "20%", "targets": 1 },
                    { "width": "20%", "targets": 2 },
                    { "width": "20%", "targets": 3 },
                    { "width": "20%", "targets": 4 }
                ],
                "serverSide": true,
                "sDom": '<"H"lfr>t<"F"ip>',
                "method": "post",
                "destroy" : true,
                "pageLength": 10,
                "sPaginationType": "full_numbers",
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                "ajax": {
                    "url": "{{ route('sales.datatable') }}",
                    "type": "POST",
                    "data": function (d) {
                        d.vendor_id=$('#filter_vendor').val();
                        d.product_id=$('#filter_product').val();
                        d.price=$('#filter_price').val();
                        d.quantity=$('#filter_quantity').val();
                        d.from = $('#from').val();
                        d.to = $('#to').val();
                        d.date_option = $('#date_options').val();
                        d.year_to_date = $('#year_to_date').val();
                    }
                },
                "order": [[ 0, "desc" ]],
                "columns": [
                    {data: 'transaction.transaction_no'},
                    {data: 'product.name'},
                    {data: 'price',fnCreatedCell: function (nTd, sData, oData, iRow, iCol) {
                                $(nTd).html("$"+oData.price);
                            }},
                    {data: 'quantity'},
                    {data: 'created_at'}
                ],
            });

            $("#filter_vendor").select2();
            $(document).on('change', '#filter_vendor', function () {
                sales_datatable.draw();
            });
            $('#from').change( function() {
                sales_datatable.draw();
            });
            $('#to').change( function() {
                sales_datatable.draw();
            });
            $('#date_options').change( function() {
                sales_datatable.draw();
            });
            $('#year_to_date').change( function() {
                sales_datatable.draw();
            });
            $('#filter_product').change( function() {
                sales_datatable.draw();
            });
            $('#filter_price').change( function() {
                sales_datatable.draw();
            });
            $('#filter_quantity').change( function() {
                sales_datatable.draw();
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
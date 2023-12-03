@extends('user.layouts.master')
@section('title', 'My Envelopes')
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
    <div class="card card-default">
        <div class="card-header separator">
            <div class="card-title">
                <h5><strong>My Envelopes Report</strong></h5>
            </div>
        </div>
        <div class="p-b-10">
            <div class="col md-12  p-t-10 p-l-30 row">
                <div class="col-md-4  card card-default p-r-1">
                    <div class="card-title">
                        <h5><strong>Favourites</strong></h5>
                    </div>

                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 10%; ">Envelope</th>
                                    <th style="width: 20%; ">Transactions
                                    </th>
                                    <th style="width: 20%; ">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-md-8 p-t-10 card card-default">
                    <div class="card-title">
                        <h5><strong>Transactions By Envelope</strong></h5>
                    </div>
                    <div class="card-body p-t-10 searchFilters">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filter_envelope_name" class="control-label">Envelope Name</label>
                                    <input type="text" class="form-control" id="filter_envelope_name">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="date_options" class="control-label">Quick Date</label>
                                    <select name="date_options" id="date_options" class="form-control">
                                        <option value="">Pick an option</option>
                                        <option value="this_month" @if ($time=='this_month' ) selected @endif>This Month</option>
                                        <option value="this_year" @if ($time=='this_year' ) selected @endif>This Year</option>
                                        <option value="all_time" @if ($time=='all_time' ) selected @endif>All Time</option>
                                    </select>
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


                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="ActivateAdvanceSerach" class="control-label">&nbsp;</label>
                                    <button type="button" class="btn btn-info form-control" id="ActivateAdvanceSerach">Advance
                                        Search</button>
                                    <button type="button" class="btn btn-info form-control" id="HideActivateAdvanceSerach" style="display: none;">Hide Advance Search</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body searchFilters m-b-10" id="AdvanceFilters" style="display: none;">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="from" class="control-label">From</label>
                                    <input type="text" id="from" placeholder="mm-dd-yyyy" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="to" class="control-label">To</label>
                                    <input type="text" id="to" placeholder="mm-dd-yyyy" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="from" class="control-label">From</label>
                                    <input type="text" id="from" placeholder="mm-dd-yyyy" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="to" class="control-label">To</label>
                                    <input type="text" id="to" placeholder="mm-dd-yyyy" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="widget-11-2-table p-t-20">
                        <table class="table table-hover table-condensed table-responsive-block
                        table-responsive" id="tableEnvelopes">
                            <thead>
                                <tr>
                                    <th style="width: 5%; "></th>
                                    <th style="width: 5%; ">ID</th>
                                    <th style="width: 10%; ">Name</th>
                                    <th style="width: 20%; ">Category
                                    </th>
                                    <th style="width: 20%; ">Date</th>
                                    <th style="width: 20%; ">Amount
                                    </th>
                                    <th style="width: 20%; ">Vendor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <hr>


    </div>
</div>
</div>


<!-- END PAGE CONTENT WRAPPER -->



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
                "url": "{{ route('user.reports.my.envelopes.datatable') }}",
                "method": "POST",
                'data': function(data) {
                    data.envelope_name = $('#filter_envelope_name').val();
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
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'envelope_category',
                    name: 'envelope_category'
                },
                {
                    data: 'envelope_date',
                    name: 'envelope_date'
                },
                {
                    data: 'amount',
                    name: 'amount'

                },

                {
                    data: 'vendor_name',
                    name: 'vendor_name',
                    fnCreatedCell: function(nTd, sData, oData, iRow, iCol) {
                        $(nTd).html("<a style='color: #0090d9' href='/admin/vendors/" + oData
                            .vendor_id + "'>" + oData.vendor_name + "</a>");
                    }
                },
               
            ],
            "language": {
                "info": "Showing _START_ to _END_ of _TOTAL_ records",
            }
        });

        $('#filter_envelope_name').keyup(function() {
            trans_datatable.draw();
        });
        $("#filter_vendor").select2();

        $("#filter_category").select2();

        $(document).on('change', '#filter_vendor', function() {
            trans_datatable.draw();
        });
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
    $(document).ready(function() {
        var array = [];
        $("#selectAllEnvelopes").on("click", function(e) {
            var table = $("#tableEnvelopes");
            var boxes = $('input:checkbox', table);
            $.each($('input:checkbox', table), function() {

                $(this).parent().addClass('checked');
                $(this).prop('checked', 'checked');

            });
            $('#deselectAllEnvelopes').prop('disabled', false);
        });

        $("#deselectAllEnvelopes").on("click", function(e) {
            var table = $("#tableEnvelopes");
            var boxes = $('input:checkbox', table);
            $.each($('input:checkbox', table), function() {

                $(this).parent().removeClass('checked');
                $(this).prop('checked', false);

            });

        });

    });
</script>

@endsection
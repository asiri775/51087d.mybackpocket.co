@extends('admin.layouts.newMaster')
@section('title', 'Vendors List')
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
       .card-button {
        margin-left: 12px !important;
        margin-bottom: 6px !important;
       }
    </style>
@endsection
@section('content')
    <div class="container-fluid container-fixed-lg">
        @if (Session::has('success'))
            <div class="alert alert-success">{{ Session::get('success') }}</div>
        @elseif(Session::has('error'))
            <div class="alert alert-danger">{{ Session::get('error') }}</div>
        @endif
        <div class="card card-default p-l-20  p-r-20">
            <div class="card-header separator">
                
                    <div class="col-md-12">
                        <div class="card-title">
                            <h5><strong>Vendors</strong></h5>
                        </div>
                        <div class="card-button card-title">
                        <a href="<?php echo url('/admin/vendors/add'); ?>" class="btn btn-success" id="showVendors">Add A Vendor</a>
                         </div>
                    </div>

            </div>
            <div class="p-b-10">
                <div class="card-body  p-t-10 searchFilters">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_vendor" class="control-label">Vendor Name</label>
                                <select id="filter_vendor" class="form-control">
                                    <option value="">Select Vendor</option>
                                    @foreach ($vendors as $vendor)
                                        <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
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
                                <label for="date_options" class="control-label">Date Options</label>
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
                                <label for="email" class="control-label">Email</label>
                                <input type="text" class="form-control" id="email">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="address" class="control-label">Address</label>
                                <input type="text" class="form-control" id="address">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="store_no" class="control-label">store no</label>
                                <input type="text" class="form-control" id="store_no">
                            </div>
                        </div>
                    </div>
                </div>


            </div>
            <table class="table table-hover table-condensed table-responsive-block table-responsive" id="vendorsTable">
                <thead>
                    <tr>
                        <th></th>
                        <th style="width:10%;">ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Store No.</th>
                        <th>Date</th>
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
                        <button type="button" class="btn btn-default" id="selectAllVendors">Select All</button>
                        <button type="button" class="btn btn-default" id="deselectAllVendors"> De-Select All</button>
                        <a onclick="hideVendors()" href="javascript:void(0);" class="btn btn-success" id="hideVendors">Hide
                            Vendors</a>
                        <a href="<?php echo url('/admin/vendors/visible'); ?>" class="btn btn-danger" id="showVendors">Show Vendors</a>
                        <button type="button" target="_blank" class="btn btn-warning batch-action" id="footer-buttons-1"
                            onclick="savePDF()">Batch Action1</button>
                        <button type="button" target="_blank" class="btn btn-warning batch-action" id="footer-buttons-2"
                            onclick="savePDF()">Batch Action2</button>
                        <button type="button" onclick="bulkXLSTransactions()" id="footer-buttons-3"
                            class="btn btn-success batch-export"><i class="icon-file-excel" href="#"></i>Export XLS</button>
                        <button type="button" onclick="printAllTransactions()" id="footer-buttons-4"
                            class="btn btn-success batch-export" target="_blank "><i class="glyphicon glyphicon-print"></i>
                            PRINT</button>
                        <button type="button" onclick="savePDF()" id="footer-buttons-5" class="btn btn-success batch-export"
                            target="_blank"><i class="icon-file-excel" href="#"></i>Save PDF</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

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

        function modalHide(vendor_id) {
            $('#vendor_id').val(vendor_id);
        }

        function modalDelete(vendor_id) {
            $('#vendor_delete_id').val(vendor_id);
        }

        function hideVendors() {
            var ids = [];
            $.each($("input[name='pdr_checkbox[]']:checked"), function() {
                ids.push($(this).val());
            });
            if (ids.length > 0) {
                var CSRF_TOKEN = $('input[name="_token"]').attr('value');
                $.ajax({
                    type: "POST",
                    dataType: 'JSON',
                    url: '<?php echo url('admin/hide-all/vendors'); ?>',
                    data: {
                        _token: CSRF_TOKEN,
                        vendor_ids: ids
                    },
                    success: function(data) {

                    },
                    complete: function() {
                        window.location = '<?= url('admin/vendors') ?>';
                    }
                });
            }

        }
        $(document).ready(function() {

            var array = [];
            $("#selectAllVendors").on("click", function(e) {
                var table = $("#vendorsTable");
                var boxes = $('input:checkbox', table);
                $.each($('input:checkbox', table), function() {

                    $(this).parent().addClass('checked');
                    $(this).prop('checked', 'checked');

                });

                $('#hideVendors').prop('disabled', false);
                $('#deselectAllVendors').prop('disabled', false);
            });

            $("#deselectAllVendors").on("click", function(e) {
                var table = $("#vendorsTable");
                var boxes = $('input:checkbox', table);
                $.each($('input:checkbox', table), function() {

                    $(this).parent().removeClass('checked');
                    $(this).prop('checked', false);

                });
                $('#hideVendors').prop('disabled', true);
            });

        });

        $(document).ready(function(e) {
            var table = $('#vendorsTable');

            $.fn.dataTable.ext.errMode = 'none';
            var vendor_datatable = table.DataTable({
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
                    "url": "{{ route('vendors.datatable') }}",
                    "method": "POST",
                    'data': function(data) {
                        data.from = $('#from').val();
                        data.to = $('#to').val();
                        data.date_option = $('#date_options').val();
                        data.year_to_date = $('#year_to_date').val();
                        data.vendor_id = $('#filter_vendor').val();
                        data.email = $('#email').val();
                        data.address = $('#address').val();
                        data.store_no = $('#store_no').val();

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
                        data: 'vendor',
                        name: 'vendor',
                        fnCreatedCell: function(nTd, sData, oData, iRow, iCol) {
                            $(nTd).html("<a style='color: #0090d9' href='/admin/vendors/" + oData
                                .id + "'>" + oData.name + "</a>");
                        }
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'short_address',
                        name: 'short_address'
                    },
                    {
                        data: 'store_no',
                        name: 'store_no'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
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

            $("#filter_vendor").select2();
            $(document).on('change', '#filter_vendor', function() {
                vendor_datatable.draw();
            });

            $('#from').change(function() {
                vendor_datatable.draw();
            });
            $('#to').change(function() {
                vendor_datatable.draw();
            });
            $('#date_options').change(function() {
                vendor_datatable.draw();
            });

            $('#email').keyup(function() {
                vendor_datatable.draw();
            });
            $('#address').keyup(function() {
                vendor_datatable.draw();
            });
            $('#store_no').keyup(function() {
                vendor_datatable.draw();
            });
            $('#hst').keyup(function() {
                vendor_datatable.draw();
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
    <div id="hide" class="modal fade" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Hide Vendor</h4>
                </div>
                <div class="modal-body">
                    <p>Are sure you want to Hide Vendor ?</p>
                </div>
                <form method="POST" action=" {{ url('admin/hide/vendors') }} ">
                    <div class="modal-footer">
                        {{ csrf_field() }}
                        <input type="hidden" name="vendor_id" value="" id="vendor_id">
                        <button type="submit" class="btn btn-danger">Hide</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="delete" class="modal fade" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Delete Vendor</h4>
                </div>
                <div class="modal-body">
                    <p>Are sure you want to Delete Vendor ?</p>
                </div>
                <form method="POST" action=" {{ route('delete.vednor') }} ">
                    <div class="modal-footer">
                        {{ csrf_field() }}
                        <input type="hidden" name="vendor_id" value="" id="vendor_delete_id">
                        <button type="submit" class="btn btn-danger">Delete</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

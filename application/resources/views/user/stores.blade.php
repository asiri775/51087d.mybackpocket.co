@extends('user.layouts.master')

@section('title', 'Stores List')

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

    #AdvanceFilters .select2 {
        width: 100%!important;
    }

</style>
@endsection
@section('content')
    <div class=" container-fluid   container-fixed-lg">
        @if (Session::has('success'))
        <div class="alert alert-success">{{ Session::get('success') }}</div>
    @elseif(Session::has('error'))
        <div class="alert alert-danger">{{ Session::get('error') }}</div>
    @endif
        <!-- START card -->
        <div class="card card-default  p-l-20  p-r-20">
            <div class="card-header separator">
                <div class="card-title">
                    <h5><strong>Stores list</strong></h5>
                </div>
                <div class="pull-right">
                    <a href="{{ route('user.reports.purchaseByVendor', ['time' => 'all_time']) }}" class="btn btn-primary">Store Report
                    </a>
                </div>
            </div>
            <div class="p-b-10">
                <div class="card-body p-t-10 searchFilters">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_vendor" class="control-label">Store</label>
                                <input type="text" id="filter_vendor" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="from" class="control-label">phone</label>
                                <input type="text" id="phone" class="form-control">
                            </div>
                        </div>
                     
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="to" class="control-label">city</label>
                                <input type="text" id="city" class="form-control">
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_order_no" class="control-label">Zip/Postal Code</label>
                                <input type="text" class="form-control" id="zip">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="branch_id" class="control-label">Branch ID</label>
                                <input type="text" class="form-control" id="branch_id">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="province" class="control-label">Province</label>
                                <input type="text" class="form-control" id="province">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="address" class="control-label">Address</label>
                                <input type="text" class="form-control" id="address">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                <hr>
                <div class="">
                <table class=" table table-hover table-condensed table-responsive-block
                    table-responsive" id="tableStore">
                    <thead>
                        <tr>
                            <th style="width:5%;">#</th>
                            <th style="width:5%;">ID</th>
                            <th style="width:10%;">Store</th>
                            <th style="width:15%;">Branch id</th>
                            <th style="width:15%;">Address</th>
                            <th style="width:15%;">city</th>
                            <th style="width:10%;">province/state</th>
                            <th style="width:5%;">zip/postal code</th>
                            <th style="width:10%;">phone</th>
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
    $(document).ready(function (e) {
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
           var table = $('#tableStore');
           $.fn.dataTable.ext.errMode = 'none';
           var trans_datatable = table.DataTable({
               "serverSide": true,
               "sDom": '<"H"lfr>t<"F"ip>',
               "destroy": true,
               "pageLength": 10,
               "sPaginationType": "full_numbers",
               "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
               "ajax": {
                    "url": "{{ route('user.stores.datatable') }}",
                    "method": "POST",
                    'data': function (data) {
                        data.phone = $('#phone').val();
                        data.city = $('#city').val();
                        data.zip = $('#zip').val();
                        data.vendor_name = $('#filter_vendor').val();
                        data.branch_id = $('#branch_id').val();
                        data.province = $('#province').val();
                        data.address = $('#address').val();
                    }
                },
                "order": [[0, "asc"]],
                "columns": [
                    {data: 'checkboxes', name: 'checkboxes', orderable: false, searchable: false},
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name'},
                    {data: 'store_no', name: 'store_no'},
                    {data: 'address', name: 'address'},
                    {data: 'city', name: 'city'},
                    {data: 'state', name: 'state'},
                    {data: 'zip_code', name: 'zip_code'},
                    {data: 'phone', name: 'phone'},


                ],
               "language": {
                   "info": "Showing _START_ to _END_ of _TOTAL_ records",
               }
           });
            $('#zip').keyup(function () {
                trans_datatable.draw();
            });
            $('#phone').keyup(function () {
                trans_datatable.draw();
            });
            $('#city').keyup(function () {
                trans_datatable.draw();
            });
            $('#province').keyup(function () {
                trans_datatable.draw();
            });
            $('#address').keyup(function () {
                trans_datatable.draw();
            });
            $('#branch_id').keyup(function () {
                trans_datatable.draw();
            });
            $('#filter_vendor').keyup(function () {
                trans_datatable.draw();
            });

       });

</script>

@endsection

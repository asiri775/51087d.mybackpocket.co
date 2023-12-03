@extends('admin.layouts.newMaster')

@section('title', 'User List')

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
                    <h5><strong>Users list</strong></h5>
                </div>
                <div class="pull-right">
                    <a href="{{ route('admin.users.addUsers') }}" style="vertical-align: middle;"
                        class="btn btn-info btn-md " id="addUser">Add Users</a>
                </div>
            </div>
            <div class="p-b-10">
                {{-- <div class="card-body p-t-10 searchFilters">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="from" class="control-label">Vendor</label>
                                <select id="filter_vendor" class="form-control">
                                    <option value="">Select Vendor</option>
                                    @foreach($vendors as $vendor)
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
                                <input type="text" id="to"  placeholder="mm-dd-yyyy" class="form-control">
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
                                <label for="filter_vendor_email" class="control-label">Vendor Email</label>
                                <input type="text" class="form-control" id="filter_vendor_email">
                            </div>
                        </div>
                    </div>
                </div> --}}

            </div>


                <hr>
                <div class="">
                <table class=" table table-hover table-condensed table-responsive-block
                    table-responsive" id="tableUsers">
                    <thead>
                        <tr>
                            <th style="width:5%"></th>
                            <th style="width:10%;">ID</th>
                            <th style="width:20%;">Name</th>
                            <th style="width:20%;">Email</th>
                            <th style="width:20%;">Role</th>
                            <th style="width:25%;">Actions</th>
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
           var table = $('#tableUsers');
           $.fn.dataTable.ext.errMode = 'none';
           var trans_datatable = table.DataTable({
               "serverSide": true,
               "sDom": '<"H"lfr>t<"F"ip>',
               "destroy": true,
               "pageLength": 10,
               "sPaginationType": "full_numbers",
               "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
               "ajax": {
                    "url": "{{ route('admin.users.datatable') }}",
                    "method": "POST",
                    'data': function (data) {
                        data.order_no = $('#filter_order_no').val();
                        data.vendor_id = $('#filter_vendor').val();
                        data.vendor_email = $('#filter_vendor_email').val();
                        data.from = $('#from').val();
                        data.to = $('#to').val();
                        data.date_option = $('#date_options').val();
                        data.year_to_date = $('#year_to_date').val();
                    }
                },
                "order": [[0, "asc"]],
                "columns": [
                    {data: 'checkboxes', name: 'checkboxes', orderable: false, searchable: false},
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name'},
                    {data: 'email', name: 'email'},
                    {data: 'role', name: 'role'},
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
               format: 'mm/dd/yyyy'
           });

           $('#to').datepicker({
               format: 'mm/dd/yyyy'
           });


       });

</script>
<script type="text/javascript">
  $(document).on('click', '.delete', function(e) {
            var $deleteModal = $('#deleteModal');
                    e.preventDefault();
                    const url = $(this).data('url');
                  $('#deleteForm').attr('action', url);
                    })
</script>

    <div class="modal fade" id="deleteModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete User</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form method="POST" action="" id="deleteForm">
                    {{ csrf_field() }}
                <div class="modal-body">
                    <p>Do you really want to delete the user ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
                </form>
            </div>
        </div>
    </div>
@endsection

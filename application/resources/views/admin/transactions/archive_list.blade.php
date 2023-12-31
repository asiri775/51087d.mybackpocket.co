@extends('admin.layouts.newMaster')

@section('title', 'Archived List')

@section('page-css')
    <style>
        .dataTables_filter {
            display: none;
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
        <div class="card card-default">
            <div class="card-header separator">
                <div class="card-title">
                    <h5><strong>Archived Transactions</strong></h5>
                </div>
            </div>
            <div class="card-body p-t-20">
                <form action="">
                    <div class="row justify-content-left">
                        <div class="col-lg-3 col-md-12">
                            <div class="form-group" style="display: inline-block">
                                <div class="row">
                                    <div class="col-md-5">
                                        <label>Order No</label>
                                    </div>
                                    <div class="col-md-7">
                                        <input type="text" class="form-control" id="filter_order_no">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-12">
                            <div class="form-group" style="display: inline-block">
                                <div class="row">
                                    <div class="col-md-5">
                                        <label>Vendor</label>
                                    </div>
                                    <div class="col-md-7">
                                        <input type="text" class="form-control" id="filter_vendor">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="from" class="control-label">From</label>
                            <input type="date" id="from" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="to" class="control-label">To</label>
                            <input type="date" id="to" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="year_to_date" class="control-label">Year to date</label>
                            <input type="date" id="year_to_date" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
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
                </div>
                <table class="table table-hover table-condensed table-responsive-block table-responsive" id="transactionsTable">
                    <thead>
                    <tr>
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
                    "url": "{{ route('ArchivedTransactions.datatable') }}",
                    "method": "POST",
                    'data': function(data){
                        data.order_no = $('#filter_order_no').val();
                        data.vendor_name = $('#filter_vendor').val();
                        data.from = $('#from').val();
                        data.to = $('#to').val();
                        data.date_option = $('#date_options').val();
                        data.year_to_date = $('#year_to_date').val();
                    }
                },
                "order": [[ 0, "asc" ]],
                "columns": [
                    // {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'id', name: 'id'},
                    {data: 'transaction_date', name: 'transaction_date'},
                    {data: 'transaction_time', name: 'transaction_time'},
                    {data: 'order_no', name: 'order_no', fnCreatedCell: function (nTd, sData, oData, iRow, iCol) {
                        $(nTd).html("<a style='color: #0090d9' href='/admin/transactions/"+oData.id+"'>"+oData.order_no+"</a>");
                    }},
                    {data: '', name: ''},
                    {data: '', name: ''},
                    {data: '', name: ''},
                    {data: '', name: ''},
                    {data: 'vendor_name', name: 'vendor_name'},
                    {data: 'vendor_email', name: 'vendor_email'},
                    {data: 'total',fnCreatedCell: function (nTd, sData, oData, iRow, iCol) {
                                $(nTd).html("$"+oData.total);
                            }},
                ]
            });
            $(document).on('keyup', '#filter_order_no', function () {
               trans_datatable.draw();
            });
            $('#filter_vendor').keyup( function() {
                trans_datatable.draw();
            });
            $('#from').change( function() {
                trans_datatable.draw();
            });
            $('#to').change( function() {
                trans_datatable.draw();
            });
            $('#date_options').change( function() {
                trans_datatable.draw();
            });
            $('#year_to_date').change( function() {
                trans_datatable.draw();
            });
            $('#transactionsTable thead tr').clone(true).appendTo('#transactionsTable thead');
            $('#transactionsTable thead tr:eq(1) th').each( function (i) {
                $(this).removeClass('sorting');
                var title = $(this).text();
                $(this).html('<input type="text" class="form-control" placeholder="Search '+title+'" />');
                $('input', this).on('keyup change click', function(e) {
                    e.stopPropagation();
                    if (trans_datatable.column(i).search() !== this.value) {
                        trans_datatable
                            .column(i)
                            .search(this.value)
                            .draw();
                    }
                });
            });

            //Date Pickers
            $('#daterangepicker').daterangepicker({
                timePicker: true,
                timePickerIncrement: 30,
                format: 'MM/DD/YYYY h:mm A'
            }, function (start, end, label) {
                console.log(start.toISOString(), end.toISOString(), label);
            });
        });

        function modalSend(trans_id) {
            $('#trans_id').val(trans_id);
        }
    </script>
    <script>
        $(document).ready(function()
        {
            $('#sendInvoice').on('click', function (e) {
                $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
                $.ajax({
                    type: "POST",
                    url: '{{ route('transactions.notify')}}',
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
    <div id="send" class="modal fade" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ __('Send Invoice Email') }}</h4>
                </div>
                <div class="modal-body">
                    <p>{{ __('Are sure you want to send email invoice to customer?') }}</p>
                    <div id="successMessage" style="display:none;" class="alert alert-success" role="alert"> Invoice successfully sent.</div>
                </div>
                    <div class="modal-footer">
                        {{csrf_field()}}
                        {{ method_field('POST') }}
                        <input type="hidden" name="trans_id" value="" id="trans_id">
                        <button type="submit" class="btn btn-danger" id="sendInvoice">{{ __('Send') }}</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    </div>
            </div>
        </div>
    </div>
@endsection

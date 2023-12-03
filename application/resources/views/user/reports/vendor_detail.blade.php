@extends('user.layouts.master')

@section('title', 'Purchases By Marchant Report')

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

        #tableTransactions_info {
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

        .switch {
            position: relative;
            display: inline-block;
            width: 48px;
            height: 26px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 21px;
            width: 21px;
            left: 4px;
            bottom: 3px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked+.slider {
            background-color: #2196F3;
        }

        input:focus+.slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked+.slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }

        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
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
                    <h5><strong>purchases by vendor reports</strong></h5>
                </div>
                <div class="pull-right">
                    <a href="{{ route('user.reports.purchaseByVendor', ['time' => 'all_time']) }}"> <button
                            class="btn btn-info"
                            style="margin-top: 1rem; background: #880638 !important; text-transform: uppercase; color: #fff; width: 100%;">
                            Back
                        </button></a>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="col-md-12 row">
                        <div class="col-md-2">
                            <?php
                            $logo = isset($vendor->logo_vendor) ? $vendor->logo_vendor : $vendor->logo;
                            
                            ?>
                            <img class="transaction-logo"
                                style="width: 200px; position: relative; left: 32%; transform: translateX(-50%)" alt="Logo"
                                data-src-retina="{{ asset('admin/assets/img/vendor-logos/' . $logo) }}"
                                data-src="{{ asset('admin/assets/img/vendor-logos/' . $logo) }}"
                                src="{{ asset('admin/assets/img/vendor-logos/' . $logo) }}">

                        </div>
                        <div class="col-md-4">
                            <span>
                                @if (isset($vendor->street_name))
                                    {{ $vendor->street_name }}
                                @endif
                                @if (isset($vendor->city)) {{ $vendor->city }} @endif
                                @if (isset($vendor->state)){{ $vendor->state }} @endif
                                {{ $vendor->zip_code }} <br>
                                @if (isset($vendor->phone)) {{ $vendor->phone }} @endif
                                @if (isset($vendor->HST))  HST#{{ $vendor->HST }} @endif

                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <h5 id="success-message" style="color: #green !important;"><strong></strong></h5>
            <h5 id="error-message" style="color: #red !important;"><strong></strong></h5>
            <div class="p-b-10">
                <div class="col md-12  p-t-10 p-l-30 row">

                    <div class="col-md-8 p-t-10 card card-default">
                        <div class="card-title">
                            <div class="pull-left">
                                <h5>Transactions</strong></h5>
                            </div>
                            <div class="pull-right">
                                <h5 style="color: #00238C !important;">
                                    <strong>${{ number_format((float) $total, 2, '.', '') }} </strong></h5>
                            </div>
                        </div>

                        <div class="card-body p-t-10 searchFilters">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="filter_order_no" class="control-label">Order No</label>
                                        <input type="text" class="form-control" id="filter_order_no">
                                    </div>
                                </div>
                                <input type="hidden" name="vendor" id="vendor" value="{{ $id }}">

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="date_options" class="control-label">Quick Date</label>
                                        <select name="date_options" id="date_options" class="form-control">
                                            <option value="">Pick an option</option>
                                            <option value="this_month">This Month</option>
                                            <option value="this_year">This Year</option>
                                            <option value="all_time">All Time</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="ActivateAdvanceSerach" class="control-label">&nbsp;</label>
                                        <button type="button" class="btn btn-info form-control"
                                            id="ActivateAdvanceSerach">Advance Search</button>
                                        <button type="button" class="btn btn-info form-control"
                                            id="HideActivateAdvanceSerach" style="display: none;">Hide Advance
                                            Search</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body searchFilters m-b-10" id="AdvanceFilters" style="display: none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="from" class="control-label">From</label>
                                        <input type="text" id="from" placeholder="mm-dd-yyyy" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="to" class="control-label">To</label>
                                        <input type="text" id="to" placeholder="mm-dd-yyyy" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <table
                            class=" table table-hover table-condensed table-responsive-block
                    table-responsive"
                            id="tableTransactions">
                            <thead>
                                <tr>
                                    <th style="width:5%"></th>
                                    <th style="width:5%;">ID</th>
                                    <th style="width: 10%;">Date</th>
                                    <th style="width: 10%;">Order#</th>
                                    <th style="width: 10%;">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-4  card card-default p-r-1">
                        <div class="card-title">
                            <h5><strong>GET MORE FROM THIS STORE</strong> </h5>

                        </div>
                        <div class="card-body">
                            <div class="row">

                                <div class="col-md-8 ">
                                    <div class="pull-left">
                                        <h6><strong>ADD TO FAVOURITES</strong> </h6>
                                    </div>
                                </div>

                                <div class="col-md-4 pull-right">
                                    <label class="switch">
                                        @php
                                            $fav = App\Models\VendorAddToFavourite::where('vendor_id', $vendor->id)
                                                ->where('user_id', Auth::user()->id)
                                                ->first();
                                        @endphp
                                        <input type="checkbox" id="add_to_favourites" data-vendor_id="{{ $vendor->id }}"
                                            data-user_id="{{ Auth::user()->id }}" data-onstyle="success"
                                            data-offstyle="danger" @if (isset($fav)) checked @endif>

                                        <span class="slider round"></span>
                                    </label>
                                </div>


                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-8">
                                    <h6><strong>GET NOTIFIED OF OFFERS</strong> </h6>
                                </div>
                                <div class="col-md-4">
                                    <label class="switch">
                                        <input type="checkbox">
                                        <span class="slider round"></span>
                                    </label>
                                </div>

                            </div>
                            <hr>
                            <div class="row">
                                <h6><strong>ADD TO ENVELOPE</strong> </h6>
                            </div>
                            <hr>
                            <div class="row">
                                <h6><strong>ADD TO BUDGET</strong> </h6>
                            </div>
                            <hr>
                            <div class="row">
                                <h6><strong>REWARDS PROGRAM</strong> </h6>
                            </div>
                            <hr>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
    <!-- END card -->
    </div>
    <!-- END CONTAINER FLUID -->

@endsection
@section('page-js')
    <script>
        $(function() {
            $('#add_to_favourites').change(function() {
                var status = $(this).prop('checked') == true ? 1 : 0;
                var vendor_id = $(this).data('vendor_id');
                var user_id = $(this).data('user_id');

                $.ajax({
                    type: "POST",
                    url: '/user/reports/vendor-add-to-favourites',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "user_id": user_id,
                        "vendor_id": vendor_id,
                        "status": status
                    },
                    success: function(response) {
                        console.log(response);
                        if (response) {
                            $('#success-message').text(response.success);
                        }
                    },
                    error: function(response) {
                        ('#error-message').text(response.error);
                    }
                });
            })
        })
    </script>
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


            function savePDF() {
                var newWin = window.open();
                var ids = [];
                $.each($("input[name='pdr_checkbox[]']:checked"), function() {
                    ids.push($(this).val());
                });
                if (ids.length > 0) {
                    var CSRF_TOKEN = $('input[name="_token"]').attr('value');
                    $.ajax({
                        type: "POST",
                        dataType: 'html',
                        url: '<?php echo route('user.transactions.printList'); ?>',
                        data: {
                            _token: CSRF_TOKEN,
                            transaction_ids: ids
                        },
                        success: function(html) {}
                    });
                }
            }

            function printAllTransactions() {
                var newWin = window.open();
                var ids = [];
                $.each($("input[name='pdr_checkbox[]']:checked"), function() {
                    ids.push($(this).val());
                });
                if (ids.length > 0) {
                    var CSRF_TOKEN = $('input[name="_token"]').attr('value');
                    $.ajax({
                        type: "POST",
                        dataType: 'html',
                        url: '<?php echo route('user.transactions.printAll'); ?>',
                        data: {
                            _token: CSRF_TOKEN,
                            transaction_ids: ids
                        },
                        success: function(html) {
                            newWin.document.write(html);
                            newWin.document.close();
                            newWin.focus();
                            newWin.print();
                            newWin.close();
                        }
                    });
                }
            }

            function bulkXLSTransactions() {
                var download = function(content, fileName, mimeType) {
                    var a = document.createElement('a');
                    mimeType = mimeType || 'application/octet-stream';

                    if (navigator.msSaveBlob) { // IE10
                        navigator.msSaveBlob(new Blob([content], {
                            type: mimeType
                        }), fileName);
                    } else if (URL && 'download' in a) { //html5 A[download]
                        a.href = URL.createObjectURL(new Blob([content], {
                            type: mimeType
                        }));
                        a.setAttribute('download', fileName);
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                    } else {
                        location.href = 'data:application/octet-stream,' + encodeURIComponent(
                            content); // only this mime type is supported
                    }
                }
                var newWin = window.open();
                var ids = [];
                $.each($("input[name='pdr_checkbox[]']:checked"), function() {
                    ids.push($(this).val());
                });
                if (ids.length > 0) {
                    var CSRF_TOKEN = $('input[name="_token"]').attr('value');
                    $.ajax({
                        type: "POST",
                        dataType: 'html',
                        url: '<?php echo route('user.transactions.exportAll'); ?>',
                        data: {
                            _token: CSRF_TOKEN,
                            transaction_ids: ids
                        },
                        success: function(html) {
                            download(html, 'dowload.csv', 'text/csv;encoding:utf-8');
                        }
                    });
                }
            }

            $(document).ready(function() {
                var array = [];
                $("#selectAllTransactions").on("click", function(e) {
                    var table = $("#tableTransactions");
                    var boxes = $('input:checkbox', table);
                    $.each($('input:checkbox', table), function() {

                        $(this).parent().addClass('checked');
                        $(this).prop('checked', 'checked');

                    });

                    $('#hideTransactions').prop('disabled', false);
                    $('#deselectAllTransactions').prop('disabled', false);
                });

                $("#deselectAllTransactions").on("click", function(e) {
                    var table = $("#tableTransactions");
                    var boxes = $('input:checkbox', table);
                    $.each($('input:checkbox', table), function() {

                        $(this).parent().removeClass('checked');
                        $(this).prop('checked', false);

                    });
                    $('#hideTransactions').prop('disabled', true);
                });

            });

            var table = $('#tableTransactions');
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
                    "url": "{{ route('user.reports.vendor.detail.datatable') }}",
                    "method": "POST",
                    'data': function(data) {
                        data.order_no = $('#filter_order_no').val();
                        data.vendor_id = $('#filter_vendor').val();
                        data.vendor = $('#vendor').val();
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
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'order_no',
                        name: 'order_no',
                        fnCreatedCell: function(nTd, sData, oData, iRow, iCol) {
                            $(nTd).html("<a style='color: #0090d9' href='/user/user-transactions/" +
                                oData.id + "'>" + oData.order_no + "</a>");
                        }
                    },
                    {
                        data: 'total',
                        fnCreatedCell: function(nTd, sData, oData, iRow, iCol) {
                            $(nTd).html("$" + oData.total);
                        }
                    },
                ],
                "language": {
                    "info": "Showing _START_ to _END_ of _TOTAL_ records",
                }
            });
            $(document).on('keyup', '#filter_order_no', function() {
                trans_datatable.draw();
            });

            $("#filter_vendor").select2();
            $(document).on('change', '#filter_vendor', function() {
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
                format: 'mm/dd/yyyy'
            });

            $('#to').datepicker({
                format: 'mm/dd/yyyy'
            });

        });
    </script>

@endsection

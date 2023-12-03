@extends('admin.layouts.newMaster')
@section('title', 'All Bank Statements')
@section('content')


    <style>
        .akDataTable .dataTables_filter {
            display: inline-block;
        }

        .akDataTable .tableHead {
            display: flex;
            align-items: center;
            margin: 15px 0;
        }

        .akDataTable div.dataTables_info {
            padding: 0 !important;
            margin: 0 !important;
            font-size: 19px !important;
            line-height: 28px;
            font-weight: 600;
            margin-top: -4px !important;
        }

        .akDataTable div.dataTables_wrapper div.dataTables_filter label {
            margin: 0;
            padding: 0;
        }

        .akDataTable div.dataTables_wrapper div.dataTables_length label {
            margin: 0;
            padding: 0;
        }

        .akDataTable .tableHeadRight {
            display: flex;
            margin-right: 0;
            margin-left: auto;
            align-items: center;
        }

        .akDataTable .tableHeadRight .dataTables_filter {
            margin-right: 15px;
        }

        .akDataTable .form-control {
            border-color: #ccc;
        }

        .tableFilters .form-group {
            width: 100%;
        }

        .p-0, .card .card-body.p-0 {
            padding: 0 !important;
        }

        .reports-overview ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: block;
        }

        .reports-overview ul li {
            display: inline-block;
        }

        .reports-overview ul li:first-child a {
            padding-left: 0 !important;
        }

        .reports-overview ul li a {
            display: block;
            padding: 0 15px;
            position: relative;
        }

        .reports-overview ul li a:after {
            content: "|";
            position: absolute;
            right: 0;
        }

        @media only screen and (max-width: 767px) {
            .akDataTable .tableHead {
                display: block;
            }

            .reports-overview {
                border-top: 1px solid #ccc;
                padding-top: 10px;
                margin-top: 10px;
            }
        }

    </style>

    <div class="page-content-wrapper ">

        <div class="content noPadTop">

            <div class=" container-fluid   container-fixed-lg">

                @if (Session::has('success'))
                    <div class="alert alert-success">{{ Session::get('success') }}</div>
                @elseif(Session::has('error'))
                    <div class="alert alert-danger">{{ Session::get('error') }}</div>
                @endif


                <div id="tableFltersGhost" style="display: none;">
                    <div class="p-b-10 mt-3 searchFiltersContainer">
                        <div class="card-body p-t-10 searchFilters">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="bank_id" class="control-label">Bank</label>
                                        <select name="bank_id" id="bank_id" class="form-control filterField">
                                            <option value="">Select Bank</option>
                                            @foreach (\App\Models\Bank::orderBy('name', 'ASC')->get() as $bank)
                                                <option
                                                    {{  $bank->id === old('bank_id') ? 'selected="selected"': ''}} value="{{$bank->id}}">{{$bank->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="bank_account_id" class="control-label">Bank Account</label>
                                        <select name="bank_account_id" id="bank_account_id"
                                                class="form-control filterField">
                                            <option value="">Select Bank Account</option>
                                            @foreach (\App\Models\BankAccount::orderBy('name', 'ASC')->get() as $bankAccount)
                                                <option
                                                    {{  $bankAccount->id === old('bank_account_id') ? 'selected="selected"': ''}} value="{{$bankAccount->id}}">{{$bankAccount->displayName()}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="status_options" class="control-label">Status</label>
                                        <select name="status" id="status_options" class="form-control filterField">
                                            <option value="">All Status</option>
                                            <option value="{{\App\Constants\StatementConstants::TRANSACTION_PENDING}}">
                                                Pending
                                            </option>
                                            <option
                                                value="{{\App\Constants\StatementConstants::TRANSACTION_CONFIRMED}}">
                                                Confirmed
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 px-0">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="">&nbsp;</label>
                                                <a class="btn mr-0 ml-auto btn-block text-capitalize btn-primary"
                                                   href="{{route('bankStatements.allTransactions')}}">All
                                                    Transactions</a>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="">&nbsp;</label>
                                                <a class="btn mr-0 ml-auto btn-block text-capitalize btn-success newRecord"
                                                   href="javascript:;">Add New</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="card card-default akDataTable p-l-20  p-r-20">

                    <table class="table table-hover table-condensed table-responsive-block table-responsive"
                           id="transactionsTable">
                        <thead>
                        <tr>
                            <th style="width: 2%" ;></th>
                            <th style="width: 10%" ;>Bank</th>
                            <th style="width: 10%" ;>Bank Account</th>
                            <th style="width: 10%" ;>Name</th>
                            <th style="width: 10%" ;>Status</th>
                            <th style="width: 10%" ;>Created On</th>
                            <th style="width: 10%" ;>Updated On</th>
                            <th style="width: 10%" ;>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr></tr>
                        </tbody>
                    </table>


                    <div class="col-xs-2 select-all-button p-b-10">
                        <div class="p-t-10">
                            <div style="float: left;">
                                <button type="button" class="btn btn-info" id="selectAllTransactions">Select All
                                </button>
                                <button type="button" class="btn btn-info" id="deselectAllTransactions">De-Select All
                                </button>
                                <button type="button" class="btn btn-primary" id="bulkUpdateBtn">Bulk Update</button>
                                <button type="button" class="btn btn-info" id="viewData">View</button>
                                <button type="button" class="btn btn-danger" id="deleteData">Delete</button>
                                <button type="button" class="btn btn-success" id="exportXLS">Export XLS</button>
                                <button type="button" class="btn btn-success" id="downloadPDF">Download PDF</button>
                                <button type="button" class="btn btn-success" id="doTablePrint">Print</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>


    <div class="modal fade" id="bulkUpdateModal" tabindex="-1" role="dialog"
         aria-labelledby="main_cat_modelLabel">
        <div class="modal-dialog" role="dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="main_cat_modelLabel"><strong>Bulk Update</strong></h4>
                </div>
                <div id="editFormData">
                    <form enctype="multipart/form-data"
                          action="{{ route('bankStatements.bulkUpdateStatements') }}"
                          id="main_cat" method="POST" class="mt-3">
                        {{ csrf_field() }}
                        <div class="modal-body">
                            <div class="row equalPad">

                                <input type="hidden" name="keys" value="" id="selectionKeys">

                                <div class="col-md-12 col-12">
                                    <div class="form-group text-left @error('status') is-invalid @enderror">
                                        <label for="status">{{ __('Status') }}</label>
                                        <select name="status" id="status"
                                                class="form-control @error('status') is-invalid @enderror">
                                            <option value="">Select Status</option>
                                            <option
                                                value="{{\App\Constants\StatementConstants::PENDING}}">
                                                Pending
                                            </option>
                                            <option
                                                value="{{\App\Constants\StatementConstants::COMPLETED}}">
                                                Completed
                                            </option>
                                        </select>
                                        @error('status')
                                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                                        @enderror
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="newRecordModal" tabindex="-1" role="dialog"
         aria-labelledby="main_cat_modelLabel">
        <div class="modal-dialog" role="dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="main_cat_modelLabel"><strong>Add New Statement</strong></h4>
                </div>

                <form enctype="multipart/form-data" action="{{ route('bankStatements.addNewStatement') }}"
                      id="main_cat" method="POST" class="mt-3">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="row equalPad">

                            <div class="col-md-12 col-12">
                                <div
                                    class="form-group text-left @error('bank_account_id') is-invalid @enderror">
                                    <label for="bank_account_id">{{ __('Bank Account') }}</label>
                                    <select name="bank_account_id" id="bank_account_id"
                                            class="form-control @error('bank_account_id') is-invalid @enderror">
                                        <option value="">Select Bank Account</option>
                                        @foreach (\App\Models\BankAccount::orderBy('name', 'ASC')->get() as $bankAccount)
                                            <option
                                                {{  $bankAccount->id === old('bank_account_id') ? 'selected="selected"': ''}} value="{{$bankAccount->id}}">{{$bankAccount->displayName()}}</option>
                                        @endforeach
                                    </select>
                                    @error('bank_account_id')
                                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12 col-12">
                                <div class="form-group text-left @error('name') is-invalid @enderror">
                                    <label for="name">{{ __('Statement Name') }}</label>
                                    <input id="name" type="text"
                                           class="form-control @error('name') is-invalid @enderror"
                                           name="name" placeholder="{{ __('Enter Statement Name') }}"
                                           value="{{old('name')}}">
                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12 col-12">
                                <div class="form-group">
                                    <label for="statement">Statement File</label>
                                    <input type="file" class="form-control" name="statement" id="statement"
                                           required>
                                    @error('statement')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <div class="modal fade" id="oldRecordModal" tabindex="-1" role="dialog"
         aria-labelledby="main_cat_modelLabel">
        <div class="modal-dialog" role="dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="main_cat_modelLabel"><strong>Update Statement</strong></h4>
                </div>
                <div id="editFormData">
                    <form enctype="multipart/form-data" action="{{ route('bankStatements.editStatement') }}"
                          id="main_cat" method="POST" class="mt-3">
                        {{ csrf_field() }}
                        <div class="modal-body">
                            <div class="row equalPad">

                                <div class="col-md-12 col-12">
                                    <div
                                        class="form-group text-left @error('bank_account_id') is-invalid @enderror">
                                        <label for="bank_account_id">{{ __('Bank Account') }}</label>
                                        <select name="bank_account_id" id="bank_account_id"
                                                class="form-control @error('bank_account_id') is-invalid @enderror">
                                            <option value="">Select Bank Account</option>
                                            @foreach (\App\Models\BankAccount::orderBy('name', 'ASC')->get() as $bankAccount)
                                                <option
                                                    {{  $bankAccount->id === old('bank_account_id') ? 'selected="selected"': ''}} value="{{$bankAccount->id}}">{{$bankAccount->name}}</option>
                                            @endforeach
                                        </select>
                                        @error('bank_account_id')
                                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12 col-12">
                                    <div class="form-group text-left @error('name') is-invalid @enderror">
                                        <label for="name">{{ __('Statement Name') }}</label>
                                        <input id="name" type="text"
                                               class="form-control @error('name') is-invalid @enderror"
                                               name="name" placeholder="{{ __('Enter Statement Name') }}"
                                               value="{{old('name')}}">
                                        @error('name')
                                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                                        @enderror
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

<?php
$fileName = date('mdY') . "_" . $title;
$fileName = \App\Helpers\Helper::slugifyText($fileName);
?>

@section('page-js')

    <script>

        $(document).on("click", ".newRecord", function () {
            $("#newRecordModal .form-control").removeClass('is-invalid').val('');
            $("#newRecordModal").modal("show");
        });

        $(document).on("click", ".editRecord", function () {
            let obj = $(this);
            let bankAccountId = obj.attr("data-bank_account_id");
            let name = obj.attr("data-name");
            let url = obj.attr("data-href");
            $("#oldRecordModal").find("form").attr("action", url);
            $("#oldRecordModal").find("#bank_account_id").val(bankAccountId).trigger("change");
            $("#oldRecordModal").find("#name").val(name).trigger("change");
            $("#oldRecordModal").modal("show");
        });

    </script>

    @if (Session::has('popup'))
        <script>
            $("#{{Session::get('popup')}}").modal("show");
        </script>
    @endif


    <script>
        var trans_datatable = null;
        $(document).on("click", ".ActivateAdvanceSerach", function () {
            $(".tableFilters #AdvanceFilters").show();
            $(".tableFilters .HideActivateAdvanceSerach").show();
            $(".tableFilters .ActivateAdvanceSerach").hide()
        });
        $(document).on("click", ".HideActivateAdvanceSerach", function () {
            $(".tableFilters #AdvanceFilters").hide();
            $(".tableFilters .HideActivateAdvanceSerach").hide();
            $(".tableFilters .ActivateAdvanceSerach").show();
        });

        $(document).on("click", "#selectAllTransactions", function (e) {
            var table = $("#transactionsTable");
            $.each($('input:checkbox', table), function () {
                $(this).parent().addClass('checked');
                $(this).prop('checked', 'checked').trigger("change");
            });
            $('#hideTransactions').prop('disabled', false);
            $('#deselectAllTransactions').prop('disabled', false);
        });

        $(document).on("click", "#deselectAllTransactions", function (e) {
            var table = $("#transactionsTable");
            $.each($('input:checkbox', table), function () {
                $(this).parent().removeClass('checked');
                $(this).prop('checked', false).trigger("change");
            });
            $('#hideTransactions').prop('disabled', true);
        });

        $(document).on("click", "#viewData", function (e) {
            var ids = [];
            $.each($("input[name='pdr_checkbox[]']:checked"), function () {
                ids.push($(this).val());
            });
            if (ids.length > 0) {
                ids = ids.join(",");
                window.location.replace("{{url('/admin/bank-statements/list-transactions')}}/" + ids);
            }
        });

        $(document).on("click", "#deleteData", function (e) {
            var ids = [];
            $.each($("input[name='pdr_checkbox[]']:checked"), function () {
                ids.push($(this).val());
            });
            if (ids.length > 0) {
                ids = ids.join(",");
                window.location.replace("{{url('/admin/bank-statements/delete-statements')}}/" + ids);
            }
        });

        $(document).on("click", "#bulkUpdateBtn", function (e) {
            var ids = [];
            $.each($("input[name='pdr_checkbox[]']:checked"), function () {
                ids.push($(this).val());
            });
            if (ids.length > 0) {
                $("#selectionKeys").val(JSON.stringify(ids));
                $("#bulkUpdateModal").modal("show");
            }
        });

        $(document).on("click", "#exportXLS", function () {
            let btn = $("#transactionsTable_wrapper .dt-buttons").find(".dt-button.buttons-excel");
            if (btn != null) {
                btn.click();
            }
        });

        $(document).on("click", "#downloadPDF", function () {
            let btn = $("#transactionsTable_wrapper .dt-buttons").find(".dt-button.buttons-pdf");
            if (btn != null) {
                btn.click();
            }
        });

        $(document).on("click", "#doTablePrint", function () {
            let btn = $("#transactionsTable_wrapper .dt-buttons").find(".dt-button.buttons-print");
            if (btn != null) {
                btn.click();
            }
        });

        function markSelected() {
            if (trans_datatable != null) {
                trans_datatable.rows().deselect();
                setTimeout(e => {
                    $.each($("input[name='pdr_checkbox[]']:checked"), function () {
                        let obj = $(this);
                        let tableRow = obj.closest("tr");
                        let indexOf = tableRow.index();
                        let isChecked = obj.is(":checked");
                        if (isChecked) {
                            trans_datatable.row(':eq(' + indexOf + ')', {page: 'current'}).select();
                        } else {
                            trans_datatable.row(':eq(' + indexOf + ')', {page: 'current'}).deselect();
                        }
                    });
                }, 200);
            }
        }

        $(document).on("click", "#transactionsTable", function () {
            markSelected();
        });

        $(document).on("change", "#transactionsTable .pdr_checkbox", function () {
            markSelected();
        });

        let buttonCommon = {
            exportOptions: {
                columns: [1, 2, 3, 4, 5, 6],
                rows: {selected: true},
            }
        };

        $(document).ready(function (e) {
            var table = $('#transactionsTable');
            $.fn.dataTable.ext.errMode = 'none';
            trans_datatable = table.DataTable({
                "serverSide": true,
                "sDom": '<"H"<"tableHead"<"tableHeadLeft"i><"tableHeadRight"flr>>><"tableFilters">t<"F"p>B',
                select: true,
                "oLanguage": {
                    "sInfo": "{{$title}}"
                },
                buttons: [
                    $.extend(true, {}, buttonCommon, {
                        extend: 'excel',
                        filename: '{{$fileName}}',
                    }),
                    $.extend(true, {}, buttonCommon, {
                        extend: 'pdf',
                        filename: '{{$fileName}}',
                    }),
                    $.extend(true, {}, buttonCommon, {
                        extend: 'print',
                        customize: function (win) {
                            $(win.document.body)
                                .prepend(
                                    '<link href="{{ asset('akDataTablePrint.css') }}" rel="stylesheet"  media="print"/>'
                                );
                        }
                    }),
                ],
                "destroy": true,
                "pageLength": 50,
                "sPaginationType": "full_numbers",
                "lengthMenu": [[10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "All"]],
                "ajax": {
                    "url": "{{ route('bankStatements.statementsDataTable') }}",
                    "method": "POST",
                    'data': function (data) {
                        $(".tableFilters .filterField").each(function () {
                            let obj = $(this);
                            data[obj.attr("name")] = obj.val();
                        });
                    },
                    "dataSrc": function (json) {
                        let data = json.stats;
                        return json.data;
                    }
                },
                "order": [[1, "asc"]],
                "columns": [
                    {data: 'checkboxes', name: 'checkboxes', orderable: false, searchable: false},
                    {data: 'bank', name: 'bank'},
                    {data: 'bankAccount', name: 'bankAccount'},
                    {data: 'name', name: 'name'},
                    {
                        data: 'status',
                        name: 'status',
                        fnCreatedCell: function (nTd, sData, oData, iRow, iCol) {
                            if (oData.status == "{{\App\Constants\StatementConstants::PENDING}}") {
                                $(nTd).html('<span class="text-primary">Pending</span>');
                            } else if (oData.status == "{{\App\Constants\StatementConstants::COMPLETED}}") {
                                $(nTd).html('<span class="text-success">Completed</span>');
                            }
                        }
                    },
                    {data: 'created_on', name: 'created_on'},
                    {data: 'updated_on', name: 'updated_on'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                "language": {
                    "info": "Showing _START_ to _END_ of _TOTAL_ records",
                },
            });

            $("#transactionsTable_wrapper .tableFilters").html($("#tableFltersGhost").html());

            $(".tableFilters #status_options").select2();
            $(".tableFilters #bank_id").select2();
            $(".tableFilters #bank_account_id").select2();


            trans_datatable.draw();
            $(document).on('keyup change', '.filterField', function () {
                trans_datatable.draw();
            });


        });

    </script>

@endsection

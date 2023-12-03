@extends('admin.layouts.newMaster')
@section('title', $title)
@section('content')
    <?php
    $bankAccountId = '';
    $bankAccountStatementId = '';
    if (isset($bankStatement)) {
        $bankAccountId = $bankStatement->bank_account_id;
        $bankAccountStatementId = $bankStatement->id;
    }
    ?>
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

        #transactionsTable_filter {
            display: none;
        }

        .modal-open .select2-container {
            z-index: 9999999;
        }

        .fieldsWrapper {
            width: 100%;
            margin: 0 0 15px;
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
    <style>
        .align-left {
            float: left;
        }

        .full-width {
            width: 100%;
        }

        #editFormData .select2-container {
            width: 100% !important;
        }

        #message-vendor, #message-budget, #message-envelope {
            padding-top: 10px;
        }

        #vendor_id, #budget_id, #envelope_id, .vendor-text-name, .budget-text-name, .envelope-text-name {
            margin-bottom: 10px;
        }
    </style>
    <script>
        let envlopmentElementSelect2 = null;
    </script>
    <!-- START CONTAINER FLUID -->
    <div class="container-fluid container-fixed-lg">
        @if (Session::has('success'))
            <div class="alert alert-success">{{ Session::get('success') }}</div>
        @elseif(Session::has('error'))
            <div class="alert alert-danger">{{ Session::get('error') }}</div>
        @endif


        @if($id == 'all')
            <div class="card card-default">
                <div class="card-body p-0">
                    <div class="row">
                        <div class="col-md-12 col-12">
                            <div class="statement-details statement-overview">
                                <h4>Summary of All Transactions</h4>
                                <h5 style="color:#000;font-size:14px;">{{$statementDate}}</h5>

                                <div class="row mt-1">
                                    <div class="col-md-3 col-12">
                                        <div class="row">
                                            <div class="col-7">
                                                <p>Payments & Credits ({{$stats['totalCredits']}}):</p>
                                            </div>
                                            <div class="col-5">
                                                <p class="text-right">
                                                    -{{\App\Helpers\Helper::printAmount($stats['paymentsAndCredits'])}}</p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-7">
                                                <p>Purchases & Debits ({{$stats['totalDebits']}}):</p>
                                            </div>
                                            <div class="col-5">
                                                <p class="text-right">{{\App\Helpers\Helper::printAmount($stats['purchaseAndDebits'])}}</p>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-7">
                                                <h5>Net Balance
                                                    ({{$stats['totalCredits'] + $stats['totalDebits']}}):</h5>
                                            </div>
                                            <div class="col-5">
                                                <h5 class="text-right">{{\App\Helpers\Helper::printAmount((($stats['paymentsAndCredits']) - $stats['purchaseAndDebits']))}}</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-9 col-12">
                                        <div class="reports-overview pl-0 pl-md-5">
                                            <h5>Quick Reports</h5>
                                            <ul>
                                                <li><a href="javascript:;">By Category</a></li>
                                                <li><a href="javascript:;">By Accounts</a></li>
                                                <li><a href="javascript:;">By Vendor</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif


        @if(isset($bankStatement))
            <div class="card card-default">
                <div class="card-body p-0">
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="statement-details">
                                <h4>Statement Summary</h4>
                                <h5>{{$statementDate}}</h5>
                                <table class="table">
                                    <tr>
                                        <td><strong>Account#:</strong></td>
                                        <td>{{$bankStatement->bankAccount->account_number}}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Account Name#:</strong></td>
                                        <td>{{$bankStatement->bankAccount->name}}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Transactions#:</strong></td>
                                        <td>
                                            <p>Debits: {{$stats['totalDebits']}}</p>
                                            <p>Credits: {{$stats['totalCredits']}}</p>
                                            <hr>
                                            <p>
                                                <strong>Total: {{$stats['totalDebits'] + $stats['totalCredits']}}</strong>
                                            </p>
                                        </td>
                                    </tr>
                                </table>

                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="statement-overview">
                                <div class="row">
                                    <div class="col-7">
                                        <h5>Previous Statement Balance</h5>
                                    </div>
                                    <div class="col-5">
                                        <h5 class="text-right">{{\App\Helpers\Helper::printAmount($stats['previousBalance'])}}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-7">
                                        <p>Payments & Credits:</p>
                                    </div>
                                    <div class="col-5">
                                        <p class="text-right">
                                            -{{\App\Helpers\Helper::printAmount($stats['paymentsAndCredits'])}}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-7">
                                        <p>Purchases & Debits:</p>
                                    </div>
                                    <div class="col-5">
                                        <p class="text-right">{{\App\Helpers\Helper::printAmount($stats['purchaseAndDebits'])}}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-7">
                                        <p>Cash Advances:</p>
                                    </div>
                                    <div class="col-5">
                                        <p class="text-right">{{\App\Helpers\Helper::printAmount($stats['cashAdvances'])}}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-7">
                                        <p>Interest:</p>
                                    </div>
                                    <div class="col-5">
                                        <p class="text-right">{{\App\Helpers\Helper::printAmount($stats['interest'])}}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-7">
                                        <p>Fee:</p>
                                    </div>
                                    <div class="col-5">
                                        <p class="text-right">{{\App\Helpers\Helper::printAmount($stats['fee'])}}</p>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-7">
                                        <h3>New Balance</h3>
                                    </div>
                                    <div class="col-5">
                                        <h3 class="text-right">{{\App\Helpers\Helper::printAmount($stats['newBalance'])}}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @endif

        <div id="tableFltersGhost" style="display: none;">
            <div class="p-b-10 mt-3 searchFiltersContainer">
                <div class="card-body p-t-10 searchFilters">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="filter_category" class="control-label">Category</label>
                                <select id="filter_category" name="category_id" class="form-control filterField">
                                    <option value="">- Select Category -</option>
                                    @php
                                        $accountCategories = \App\Models\Category::where('type', 'accounting')->where('role','main')->get();
                                    @endphp
                                    @foreach($accountCategories AS $key=>$account)
                                        <option value="{{$account->id }}"
                                                style="font-weight: bold;font-size:15px;">{{ $account->name }}</option>
                                        @foreach (\App\Models\Category::where('role', 'sub')->where('mainid', $account->id)->orderBy('name', 'ASC')->get()
                                        as $subCategory)
                                            <option value="{{ $subCategory->id }}"
                                                    style="font-weight: bold;font-size:14px;">
                                                &nbsp;&nbsp;{{ $subCategory->name }}</option>
                                            @foreach (\App\Models\Category::where('role', 'child')->where('mainid', $subCategory->id)->orderBy('name', 'ASC')->get()
                                    as $childCategory)
                                                <option value="{{ $childCategory->id }}" style="font-size:13px;">&nbsp;&nbsp;&nbsp;{{ $childCategory->name }}</option>
                                            @endforeach
                                        @endforeach
                                    @endforeach

                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="filter_vendor" class="control-label">Vendor</label>
                                <select id="filter_vendor" name="vendor_id" class="form-control filterField">
                                    <option value="">Select Vendor</option>
                                    @foreach(\App\Models\Vendor::orderBy('name', 'ASC')->get() as $vendor)
                                        <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="year_options" class="control-label">Year</label>
                                <select name="year_options" id="year_options"
                                        class="form-control filterField search-filter-year_options">
                                    <option value="">Choose Year</option>
                                    <?php
                                    for ($i = date('Y'); $i >= 2010; $i--){
                                    $yearName = $i;
                                    ?>
                                    <option value="<?=$i?>"><?=$yearName?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="from" class="control-label">From</label>
                                <input type="text" name="from" id="from"
                                       class="form-control datePicker filterField search-filter-from"
                                       placeholder="Select From">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="to" class="control-label">To</label>
                                <input type="text" name="to" id="to"
                                       class="form-control datePicker filterField search-filter-to"
                                       placeholder="Select To">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="ActivateAdvanceSerach" class="control-label">&nbsp;</label>
                                <button type="button"
                                        class="btn btn-info form-control advanceNonAdvanceSearch ActivateAdvanceSerach">
                                    Advance Search
                                </button>
                                <button type="button"
                                        class="btn btn-info form-control advanceNonAdvanceSearch HideActivateAdvanceSerach"
                                        style="display: none;">Hide Advance Search
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body searchFilters m-b-10" id="AdvanceFilters" style="display: none;">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="bank_account_id" class="control-label">Account</label>
                                <select name="bank_account_id" id="bank_account_id" class="form-control filterField">
                                    <option value="">Select Account</option>
                                    @foreach (\App\Models\BankAccount::orderBy('name', 'ASC')->get() as $bankAccount)
                                        <option
                                            {{  $bankAccount->id === old('bank_account_id') ? 'selected="selected"': ''}} value="{{$bankAccount->id}}">{{$bankAccount->displayName()}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="type_options" class="control-label">Type</label>
                                <select name="is_debit" id="type_options" class="form-control filterField">
                                    <option value="">All Type</option>
                                    <option value="{{\App\Constants\StatementConstants::DEBIT}}">Debit</option>
                                    <option value="{{\App\Constants\StatementConstants::CREDIT}}">Credit</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="status_options" class="control-label">Status</label>
                                <select name="status" id="status_options" class="form-control filterField">
                                    <option value="">All Status</option>
                                    <option value="{{\App\Constants\StatementConstants::TRANSACTION_PENDING}}">Pending
                                    </option>
                                    <option value="{{\App\Constants\StatementConstants::TRANSACTION_CONFIRMED}}">
                                        Confirmed
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_particular" class="control-label">Particular</label>
                                <input type="text" placeholder="Search Particular" name="particular"
                                       class="form-control filterField" id="filter_particular">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_id" class="control-label">ID</label>
                                <input type="text" placeholder="Enter ID" name="id"
                                       class="form-control filterField" id="filter_id">
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
                    <th style="width: 10%" ;>ID</th>
                    <th style="width: 10%" ;>Transaction Date</th>
                    <th style="width: 10%" ;>Account</th>
                    <th style="width: 10%" ;>Particular</th>
                    <th style="width: 10%" ;>Status</th>
                    <th style="width: 10%" ;>Category</th>
                    <th style="width: 10%" ;>Vendor</th>
                    <th style="width: 10%" ;>Type</th>
                    <th style="width: 10%" ;>Amount</th>
                </tr>
                </thead>
                <tbody>
                <tr></tr>
                </tbody>
                <tfoot>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th style="text-align:right"># of Items:</th>
                    <th class="itemsCount"></th>
                    <th style="text-align:right">Total:</th>
                    <th class="totalVal"></th>
                </tr>
                </tfoot>
            </table>

            <div class="belowDataTableExtra">
                <table id="thisPageStats" class="table transactionTotal table-bordered">
                    <tr>
                        <td><h4>This Page</h4></td>
                        <td>
                            <h4 class="text-danger">Debits</h4>
                            <h6>
                                <span id="thisPageTotalDebit">-</span>
                                | <span id="thisPageTotalDebitAmount">-</span>
                            </h6>
                        </td>
                        <td>
                            <h4 class="text-success">Credits</h4>
                            <h6>
                                <span id="thisPageTotalCredit">-</span>
                                | <span id="thisPageTotalCreditAmount">-</span>
                            </h6>
                        </td>
                    </tr>
                </table>


                <table id="totalPageStats" class="table transactionTotal table-bordered">
                    <tr>
                        <td><h4>Grand Total</h4></td>
                        <td>
                            <h4 class="text-danger">Debits</h4>
                            <h6>
                                <span id="allPageTotalDebit">-</span>
                                | <span id="allPageTotalDebitAmount">-</span>
                            </h6>
                        </td>
                        <td>
                            <h4 class="text-success">Credits</h4>
                            <h6>
                                <span id="allPageTotalCredit">-</span>
                                | <span id="allPageTotalCreditAmount">-</span>
                            </h6>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="col-xs-2 select-all-button p-b-10">
                <div class="p-t-10">
                    <div style="float: left;">
                        <button type="button" class="btn btn-info" id="selectAllTransactions">Select All</button>
                        <button type="button" class="btn btn-info" id="deselectAllTransactions">De-Select All</button>
                        <button type="button" class="btn btn-primary" id="bulkUpdateBtn">Bulk Update</button>
                        <button type="button" class="btn btn-success" id="exportXLS">Export XLS</button>
                        <button type="button" class="btn btn-success" id="downloadPDF">Download PDF</button>
                        <button type="button" class="btn btn-success" id="doTablePrint">Print</button>
                        <button type="button" class="btn btn-success" id="bulkExport">Transactions By Category</button>
						<button type="button" class="btn btn-success" id="bulkSheetExport">Bulk Sheets Export</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="bulkUpdateModal" tabindex="-1" role="dialog" aria-labelledby="main_cat_modelLabel">
        <div class="modal-dialog" role="dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="main_cat_modelLabel"><strong>Bulk Update</strong></h4>
                </div>
                <div id="editFormData">
                    <form enctype="multipart/form-data" action="{{ route('bankStatements.bulkUpdateStatement') }}"
                          id="main_cat" method="POST" class="mt-3">
                        {{ csrf_field() }}
                        <div class="modal-body">
                            <div class="row equalPad">

                                <input type="hidden" name="keys" value="" id="selectionKeys">

                                <div class="col-md-12 col-12">
                                    <div class="form-group text-left @error('transaction_date') is-invalid @enderror">
                                        <label for="transaction_date">{{ __('Transaction Date') }}</label>
                                        <input id="transaction_date" type="date"
                                               class="form-control @error('transaction_date') is-invalid @enderror"
                                               name="transaction_date" placeholder="{{ __('Select Transaction Date') }}"
                                               value="{{old('transaction_date')}}">
                                        @error('transaction_date')
                                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12 col-12">
                                    <div class="form-group text-left @error('category_id') is-invalid @enderror">
                                        <label for="category_id">{{ __('Category') }}</label>
                                        <select name="category_id" id="category_id"
                                                class="form-control @error('category_id') is-invalid @enderror">
                                            <option value="">- Select Category -</option>
                                            @php
                                                $accountCategories = \App\Models\Category::where('type', 'accounting')->where('role','main')->get();
                                            @endphp
                                            @foreach($accountCategories AS $account)
                                                <option value="{{$account->id }}"
                                                        style="font-weight: bold;font-size:15px;">{{ $account->name }}</option>
                                                @foreach (\App\Models\Category::where('role', 'sub')->where('mainid', $account->id)->orderBy('name', 'ASC')->get()
                                                as $subCategory)
                                                    <option value="{{ $subCategory->id }}"
                                                            style="font-weight: bold;font-size:14px;">
                                                        &nbsp;&nbsp;{{ $subCategory->name }}</option>
                                                    @foreach (\App\Models\Category::where('role', 'child')->where('mainid', $subCategory->id)->orderBy('name', 'ASC')->get()
                as $childCategory)
                                                        <option value="{{ $childCategory->id }}"
                                                                style="font-size:13px;">
                                                            &nbsp;&nbsp;&nbsp;{{ $childCategory->name }}</option>
                                                    @endforeach
                                                @endforeach
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12 col-12">
                                    <div class="form-group text-left @error('bank_account_id') is-invalid @enderror">
                                        <label for="bank_account_id">{{ __('Bank Account') }}</label>
                                        <select name="bank_account_id" id="bank_account_id"
                                                class="form-control @error('bank_account_id') is-invalid @enderror">
                                            <option value="">Select Bank Account</option>
                                            @foreach (\App\Models\BankAccount::orderBy('name', 'ASC')->get() as $bankAccount)
                                                <option
                                                    {{  $bankAccount->id === $bankAccountId ? 'selected="selected"': ''}} value="{{$bankAccount->id}}">{{$bankAccount->displayName()}}</option>
                                            @endforeach
                                        </select>
                                        @error('status')
                                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12 col-12">
                                    <div
                                        class="form-group text-left @error('bank_account_statement_id') is-invalid @enderror">
                                        <label
                                            for="bank_account_statement_id">{{ __('Bank Account Statement') }}</label>
                                        <select name="bank_account_statement_id" id="bank_account_statement_id"
                                                class="form-control @error('bank_account_statement_id') is-invalid @enderror">
                                            <option value="">Select Bank Account Statement</option>
                                            @foreach (\App\Models\BankAccountStatement::orderBy('name', 'ASC')->get() as $bankAccountStatment)
                                                <option data-parent-id="{{$bankAccountStatment->bank_account_id}}"
                                                        {{  $bankAccountStatment->id === $bankAccountStatementId ? 'selected="selected"': ''}} value="{{$bankAccountStatment->id}}">{{$bankAccountStatment->name.' ( '.$bankAccountStatment->bankAccount->displayName().' )'}}</option>
                                            @endforeach
                                        </select>
                                        @error('status')
                                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12 col-12">
                                    <div class="form-group text-left @error('status') is-invalid @enderror">
                                        <label for="status">{{ __('Status') }}</label>
                                        <select name="status" id="status"
                                                class="form-control @error('status') is-invalid @enderror">
                                            <option value="">Select Status</option>
                                            <option value="{{\App\Constants\StatementConstants::TRANSACTION_PENDING}}">
                                                Pending
                                            </option>
                                            <option
                                                value="{{\App\Constants\StatementConstants::TRANSACTION_CONFIRMED}}">
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
                                <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"
                                      integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p"
                                      crossorigin="anonymous"/>
                                <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
                                        integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
                                        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
                                <div class="col-md-12 col-12">
                                    <div class="form-group text-left @error('vendor_id') is-invalid @enderror">
                                        <label for="vendor_id" class="full-width">{{ __('Vendor') }}</label>
                                        <select name="vendor_id" id="vendor_id"
                                                class="align-left col-md-10 form-control @error('vendor_id') is-invalid @enderror">
                                            <option value="">Select Vendor</option>
                                            @foreach (\App\Models\Vendor::orderBy('name', 'ASC')->get() as $vendor)
                                                <option value="{{$vendor->id}}">{{$vendor->name}}</option>
                                            @endforeach
                                        </select>
                                        <button id="btn-vendor-add" type="button" style="width: 40px;
                                        margin-left: 10px !important;" class="align-left col-md-02 btn btn-primary"><i
                                                class="fas fa-plus"></i></button>
                                        @error('vendor_id')
                                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div col-md-12>
                                    <div id="message-vendor" class="alert alert-success" style="display: none"></div>
                                    <div id="message-vendor-error" class="alert alert-danger"
                                         style="display: none"></div>
                                    <div id="form-vendor"></div>
                                    <hr/>
                                    <script>
                                        function appendVendorRow(id, user) {
                                            var html = "<div id=\"opt-row." + id + "\" class=\"form-group row\">\n" +
                                                " <div class=\"col-12\">\n" +
                                                " <input required type=\"text\" class=\"form-control vendor-text-name\" id=\"opt-vendorname." + id + "\" name=\"opt-vendorname." + id + "\" placeholder=\"Vendor Name\" value=\"" + user.vendorname + "\">\n" +
                                                " </div><br/>\n" +
                                                "  <button type=\"button\" onclick=\"vendorSave(" + id + ")\" class=\"btn btn-info\">Save</button>\n" +
                                                "  &nbsp;\n" +
                                                "  <button type=\"button\" onclick=\"delVendorRow(" + id + ")\" class=\"btn btn-danger\">Cancel</button>\n" +
                                                "        </div>";
                                            $("#form-vendor").append(html);
                                        }

                                        function delVendorRow(id) {
                                            var element = document.getElementById("opt-row." + id);
                                            element.parentNode.removeChild(element);
                                            $('#btn-vendor-add').show();
                                        }

                                        function vendorSave(id) {
                                            var vendorname = document.getElementById("opt-vendorname." + id).value;
                                            var CSRF_TOKEN = $('input[name="_token"]').attr('value');
                                            if ((vendorname == '')) {
                                                $('#message-vendor-error').html("Vendor name can't be empty.");
                                                $('#message-vendor-error').show("slow").delay(2000).hide("slow");
                                                return false;
                                            } else {
                                                $.ajax({
                                                    type: "POST",
                                                    dataType: 'html',
                                                    url: "{{ route('store.vendor') }}",
                                                    data: {
                                                        _token: CSRF_TOKEN,
                                                        name: vendorname,
                                                        email: 'noemail@backpocket.ca',
                                                        address: 'Add An Address',
                                                        zip_code: 'Add A Postal Code',
                                                        store_no: 'Add A Store Number',
                                                        phone: 'Add A Phone',
                                                        HST: 'Add A HST',
                                                        QST: 'Add A QST'
                                                    },
                                                    success: function (html) {
                                                        $('#message-vendor').html('You have successfully added the vendor - ' + vendorname);
                                                        $('#message-vendor').show("slow").delay(3000).hide("slow");
                                                        $.ajax({
                                                            type: "GET",
                                                            dataType: 'html',
                                                            url: "{{ route('get.all.vendors')}}",
                                                            success: function (html) {
                                                                $('#vendor_id').html(html);
                                                                delVendorRow(id);
                                                            }
                                                        });
                                                    }
                                                });
                                            }

                                        }

                                        $(document).ready(function () {
                                            var count = 0;
                                            $("#btn-vendor-add").click(function () {
                                                $('#btn-vendor-add').hide();
                                                appendVendorRow(count++, {
                                                    vendorname: "",
                                                })
                                            });
                                        });
                                    </script>
                                </div>

                                <div id="envelopeWrapper" class="fieldsWrapper">

                                    <div class="col-md-12 col-12">
                                        <div class="form-group text-left @error('envelope_id') is-invalid @enderror">
                                            <label for="envelope_id" class="full-width">{{ __('Envelope') }}</label>
                                            <div class="w-100 d-block">
                                                <select name="envelope_id[]" multiple id="envelope_id"
                                                        class="align-left col-md-10 form-control @error('envelope_id') is-invalid @enderror">
                                                    <option value="">Select Envelope</option>
                                                    @foreach (\App\Models\Envelope::orderBy('name', 'ASC')->get() as $envelope)
                                                        <option value="{{$envelope->id}}">{{$envelope->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @error('envelope_id')
                                            <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                                            @enderror
                                        </div>
                                        <button id="btn-envelope-add" type="button"
                                                class="align-left col-md-02 btn btn-primary">Add New Envelope
                                        </button>
                                    </div>

                                    <div col-md-12>
                                        <div id="message-envelope" class="alert alert-success"
                                             style="display: none;margin-top: 52px;"></div>
                                        <div id="message-envelope-error" class="alert alert-danger"
                                             style="display: none;margin-top: 52px;"></div>
                                        <div id="form-envelope"></div>
                                        <script>
                                            function appendEnvelopeRow(id, user) {
                                                var html = "<div id=\"opt-row." + id + "\" class=\"form-group row\">\n" +
                                                    " <div class=\"col-12\">\n" +
                                                    " <input required type=\"text\" class=\"form-control envelope-text-name\" id=\"opt-envelopename." + id + "\" name=\"opt-envelopename." + id + "\" placeholder=\"Envelope Name\" value=\"" + user.envelopename + "\">\n" +
                                                    " </div><br/>\n" +
                                                    "  <button type=\"button\" onclick=\"saveEnvelope(" + id + ")\" class=\"btn btn-info\">Save</button>\n" +
                                                    "  &nbsp;\n" +
                                                    "  <button type=\"button\" onclick=\"delEnvelopeRow(" + id + ")\" class=\"btn btn-danger\">Cancel</button>\n" +
                                                    "        </div>";
                                                $("#form-envelope").append(html);
                                            }

                                            function delEnvelopeRow(id) {
                                                var element = document.getElementById("opt-row." + id);
                                                element.parentNode.removeChild(element);
                                                $('#btn-envelope-add').show();
                                            }

                                            function saveEnvelope(id) {
                                                var envelopename = document.getElementById("opt-envelopename." + id).value;
                                                var CSRF_TOKEN = $('input[name="_token"]').attr('value');
                                                if ((envelopename == '')) {
                                                    $('#message-envelope-error').html("Envelope name can't be empty.");
                                                    $('#message-envelope-error').show("slow").delay(2000).hide("slow");
                                                    return false;
                                                } else {
                                                    $.ajax({
                                                        type: "POST",
                                                        dataType: 'html',
                                                        url: "{{ route('create-envelope')}}",
                                                        data: {_token: CSRF_TOKEN, name: envelopename},
                                                        success: function (html) {
                                                            $('#message-envelope').html('You have successfully added the envelope - ' + envelopename);
                                                            $('#message-envelope').show("slow").delay(3000).hide("slow");
                                                            $.ajax({
                                                                type: "GET",
                                                                dataType: 'html',
                                                                url: "{{ route('get.all.envelopes')}}",
                                                                success: function (html) {
                                                                    $('#envelope_id').html(html);
                                                                    delEnvelopeRow(id);
                                                                    if (envlopmentElementSelect2 != null) {
                                                                        $("#envelope_id").select2('destroy');
                                                                    }
                                                                    envlopmentElementSelect2 = $("#envelope_id").select2({
                                                                        placeholder: 'Select Envelope'
                                                                    });
                                                                }
                                                            });
                                                        }
                                                    });
                                                }

                                            }

                                            $(document).ready(function () {
                                                var count = 0;
                                                $("#btn-envelope-add").click(function () {
                                                    $('#btn-envelope-add').hide();
                                                    appendEnvelopeRow(count++, {
                                                        envelopename: "",
                                                    })
                                                });
                                            });
                                        </script>
                                    </div>

                                </div>

                                <div class="col-md-12 col-12">
                                    <div class="form-group text-left @error('budget_id') is-invalid @enderror">
                                        <label for="budget_id" class="full-width">{{ __('Budget') }}</label>
                                        <select name="budget_id" id="budget_id"
                                                class="align-left col-md-10 form-control @error('budget_id') is-invalid @enderror">
                                            <option value="">Select Budget</option>
                                            @foreach (\App\Models\Budget::orderBy('name', 'ASC')->get() as $budget)
                                                <option value="{{$budget->id}}">{{$budget->name}}</option>
                                            @endforeach
                                        </select>
                                        <button id="btn-budget-add" type="button" style="width: 40px;
                                        margin-left: 10px !important;" class="align-left col-md-02 btn btn-primary"><i
                                                class="fas fa-plus"></i></button>
                                        @error('budget_id')
                                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div col-md-12>
                                    <div id="message-budget" class="alert alert-success" style="display: none"></div>
                                    <div id="message-budget-error" class="alert alert-danger"
                                         style="display: none"></div>
                                    <div id="form-budget"></div>
                                    <hr/>
                                    <script>
                                        function appendBudgetRow(id, user) {
                                            var html = "<div id=\"opt-row." + id + "\" class=\"form-group row\">\n" +
                                                " <div class=\"col-6\">\n" +
                                                " <input required type=\"text\" class=\"form-control budget-text-name\" id=\"opt-budgetname." + id + "\" name=\"opt-budgetname." + id + "\" placeholder=\"Budget Name\" value=\"" + user.budgetname + "\">\n" +
                                                " </div><br/>\n" +
                                                " <div class=\"col-6\">\n" +
                                                " <input required type=\"number\" class=\"form-control budget-text-value\" id=\"opt-budgetvalue." + id + "\" name=\"opt-budgetvalue." + id + "\" placeholder=\"Budget Value\" value=\"" + user.budgetvalue + "\">\n" +
                                                " </div><br/>\n" +
                                                "  <button type=\"button\" onclick=\"saveBudget(" + id + ")\" class=\"btn btn-info\">Save</button>\n" +
                                                "  &nbsp;\n" +
                                                "  <button type=\"button\" onclick=\"delBudgetRow(" + id + ")\" class=\"btn btn-danger\">Cancel</button>\n" +
                                                "        </div>";
                                            $("#form-budget").append(html);
                                        }

                                        function delBudgetRow(id) {
                                            var element = document.getElementById("opt-row." + id);
                                            element.parentNode.removeChild(element);
                                            $('#btn-budget-add').show();
                                        }

                                        function saveBudget(id) {
                                            var budgetname = document.getElementById("opt-budgetname." + id).value;
                                            var budget_value = document.getElementById("opt-budgetvalue." + id).value;
                                            var CSRF_TOKEN = $('input[name="_token"]').attr('value');
                                            if ((budgetname == '') || (budget_value == '')) {
                                                $('#message-budget-error').html("Please enter valid data.");
                                                $('#message-budget-error').show("slow").delay(2000).hide("slow");
                                                return false;
                                            } else if (!$.isNumeric(budget_value)) {
                                                $('#message-budget-error').html('Please enter valid budget amount.');
                                                $('#message-budget-error').show("slow").delay(2000).hide("slow");
                                                return false;
                                            } else {
                                                $.ajax({
                                                    type: "POST",
                                                    dataType: 'html',
                                                    url: "{{ route('create-budget')}}",
                                                    data: {
                                                        _token: CSRF_TOKEN,
                                                        name: budgetname,
                                                        target_budget_value: budget_value
                                                    },
                                                    success: function (html) {
                                                        $('#message-budget').html('You have successfully added the budget - ' + budgetname);
                                                        $('#message-budget').show("slow").delay(3000).hide("slow");
                                                        $.ajax({
                                                            type: "GET",
                                                            dataType: 'html',
                                                            url: "{{ route('get.all.budgets')}}",
                                                            success: function (html) {
                                                                $('#budget_id').html(html);
                                                                delBudgetRow(id);
                                                            }
                                                        });
                                                    }
                                                });
                                            }

                                        }

                                        $(document).ready(function () {
                                            var count = 0;
                                            $("#btn-budget-add").click(function () {
                                                $('#btn-budget-add').hide();
                                                appendBudgetRow(count++, {
                                                    budgetname: "",
                                                    budgetvalue: "",
                                                })
                                            });
                                        });
                                    </script>
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

    <div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="main_cata_modelLabel">
        <div class="modal-dialog" role="dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="main_cata_modelLabel"><strong>Update Category</strong></h4>
                </div>
                <div id="editFormData">
                    <form enctype="multipart/form-data" action="{{ route('bankStatements.updateInvoiceCategory') }}"
                          id="main_cat" method="POST" class="mt-3">
                        {{ csrf_field() }}
                        <div class="modal-body">
                            <div class="row equalPad">
                                <input type="hidden" name="invoice_id" value="" id="category_invoice_id">
                                <div class="col-md-12 col-12">
                                    <div class="form-group text-left @error('category_id') is-invalid @enderror">
                                        <label for="category_id">{{ __('Category') }}</label>
                                        <select name="category_id" id="category_id"
                                                class="form-control @error('category_id') is-invalid @enderror">
                                            <option value="">- Select Category -</option>
                                            @php
                                                $accountCategories = \App\Models\Category::where('type', 'accounting')->where('role','main')->get();
                                            @endphp
                                            @foreach($accountCategories AS $account)
                                                <option value="{{$account->id }}"
                                                        style="font-weight: bold;font-size:15px;">{{ $account->name }}</option>
                                                @foreach (\App\Models\Category::where('role', 'sub')->where('mainid', $account->id)->orderBy('name', 'ASC')->get()
                                                as $subCategory)
                                                    <option value="{{ $subCategory->id }}"
                                                            style="font-weight: bold;font-size:14px;">
                                                        &nbsp;&nbsp;{{ $subCategory->name }}</option>
                                                    @foreach (\App\Models\Category::where('role', 'child')->where('mainid', $subCategory->id)->orderBy('name', 'ASC')->get()
                as $childCategory)
                                                        <option value="{{ $childCategory->id }}"
                                                                style="font-size:13px;">
                                                            &nbsp;&nbsp;&nbsp;{{ $childCategory->name }}</option>
                                                    @endforeach
                                                @endforeach
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                                        @enderror
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

        $(document).on("click", "#bulkUpdateBtn", function (e) {
            var ids = [];
            $.each($("input[name='pdr_checkbox[]']:checked"), function () {
                ids.push($(this).val());
            });
            if (ids.length > 0) {
                $("#selectionKeys").val(JSON.stringify(ids));
                $("#bulkUpdateModal").modal("show");
                setTimeout(e => {
                    if (envlopmentElementSelect2 != null) {
                        $("#envelope_id").select2('destroy');
                    }
                    envlopmentElementSelect2 = $("#envelope_id").select2({
                        placeholder: 'Select Envelope'
                    });
                }, 250);
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

        $(document).on("change", "#bulkUpdateModal #bank_account_id", function () {
            var value = $(this).val();
            var statmentSelect = $("#bulkUpdateModal #bank_account_statement_id");
            statmentSelect.find("option").hide();
            if (value != '') {
                statmentSelect.find('option').first().text("Select Bank Account Statement").show();
                statmentSelect.find('option[data-parent-id="' + value + '"]').show();
            } else {
                statmentSelect.find('option').first().text("Select Bank Account First").show();
            }
            statmentSelect.val('').trigger("change");
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
                    tableFooterSummaryCal();
                }, 200);
            }
        }

        function updateCategory(id) {
            $("#category_invoice_id").val(id);
            $("#categoryModal").modal("show");
        }

        $(document).on("click", "#bulkExport", function () {
            var from = $('#transactionsTable_wrapper .search-filter-from').val();
            var to = $('#transactionsTable_wrapper .search-filter-to').val();
            var year = $('#transactionsTable_wrapper .search-filter-year_options').val();

            var url = '{{route('bankStatements.allTransactions.export')}}' + "?from=" + from + "&to=" + to + "&year=" + year;
            window.open(url, '_blank');
        });
		
        $(document).on("click", "#bulkSheetExport", function () {
            var from = $('#transactionsTable_wrapper .search-filter-from').val();
            var to = $('#transactionsTable_wrapper .search-filter-to').val();
            var year = $('#transactionsTable_wrapper .search-filter-year_options').val();

            var url = '{{route('bankStatements.allTransactions.export.byYear')}}' + "?from=" + from + "&to=" + to + "&year=" + year;
            window.open(url, '_blank');
        });

        $(document).on("click", "#transactionsTable", function () {
            markSelected();
        });

        $(document).on("change", "#transactionsTable .pdr_checkbox", function () {
            markSelected();
        });

        function tableFooterSummaryCal() {
            let total = 0;
            let totalItems = 0;
            $.each($("input[name='pdr_checkbox[]']:checked"), function () {
                let obj = $(this);
                let tableRow = obj.closest("tr");
                let isChecked = obj.is(":checked");
                if (isChecked) {
                    totalItems++;
                    let lastTdValue = tableRow.find("td").last().html();
                    lastTdValue = lastTdValue.replace(/[\$,]/g, '').toString().trim();
                    lastTdValue = lastTdValue * 1;
                    total = total + (lastTdValue);
                }
            });
            $("#transactionsTable_wrapper tfoot").find("th.totalVal").html("$" + total.toFixed(2));
            $("#transactionsTable_wrapper tfoot").find("th.itemsCount").html(totalItems);
        }

        let buttonCommon = {
            footer: true,
            exportOptions: {
                format: {
                    body: function (data, row, column, node) {
                        if (column === 7) {
                            return node.innerText;
                        }
                        return data;
                    }
                }
            }
        };


        $(document).ready(function (e) {
            // $.fn.dataTable.moment( 'MM-DD-YYYY' );
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
                        exportOptions: {
                            columns: ':gt(0)',
                            rows: {selected: true},
                        }
                    }),
                    $.extend(true, {}, buttonCommon, {
                        extend: 'pdf',
                        filename: '{{$fileName}}',
                        exportOptions: {
                            columns: ':gt(0)',
                            rows: {selected: true},
                        }
                    }),
                    $.extend(true, {}, buttonCommon, {
                        extend: 'print',
                        exportOptions: {
                            columns: ':gt(0)',
                            rows: {selected: true}
                        },
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
                    "url": "{{ route('bankStatements.transactionDataTable', $id) }}",
                    "method": "POST",
                    'data': function (data) {
                        $(".tableFilters .filterField").each(function () {
                            let obj = $(this);
                            data[obj.attr("name")] = obj.val();
                        });
                        data['pt'] = "{{isset($_GET['particular'])?trim($_GET['particular']):''}}";
                        data['current_page'] = "{{Request::url()}}";
                    },
                    "dataSrc": function (json) {
                        let data = json.stats;
                        let amountPrefix = "$";
                        $("#thisPageTotalDebit").html(data.this_page.debits);
                        $("#thisPageTotalDebitAmount").html(data.this_page.debits_total);
                        $("#thisPageTotalCredit").html(data.this_page.credits);
                        $("#thisPageTotalCreditAmount").html(data.this_page.credits_total);
                        $("#allPageTotalDebit").html(data.total.debits);
                        $("#allPageTotalDebitAmount").html(data.total.debits_total);
                        $("#allPageTotalCredit").html(data.total.credits);
                        $("#allPageTotalCreditAmount").html(data.total.credits_total);
                        return json.data;
                    }
                },
                "order": [[2, "asc"]],
                "columns": [
                    {data: 'checkboxes', name: 'checkboxes', orderable: false, searchable: false},
                    {data: 'id', name: 'id'},
                    {
                        data: 'transaction_date',
                        name: 'transaction_date',
                        fnCreatedCell: function (nTd, sData, oData, iRow, iCol) {
                            $(nTd).html(oData.transaction_date_mdy);
                        }
                    },
                    {data: 'bank_account', name: 'bank_account'},
                    {data: 'particularWithLink', name: 'particularWithLink'},
                    {
                        data: 'status',
                        name: 'status',
                        fnCreatedCell: function (nTd, sData, oData, iRow, iCol) {
                            if (oData.status == "{{\App\Constants\StatementConstants::TRANSACTION_PENDING}}") {
                                $(nTd).html('<span class="text-warning">Pending</span>');
                            } else {
                                $(nTd).html('<span class="text-success">Confirmed</span>');
                            }
                        }
                    },
                    {
                        data: 'category', name: 'category', fnCreatedCell: function (nTd, sData, oData, iRow, iCol) {
                            $(nTd).html('<a href="#" onclick="updateCategory(' + oData.id + ');">' + oData.category + '</a>');
                        }
                    },
                    {data: 'vendor', name: 'vendor'},
                    {
                        data: 'type',
                        name: 'type',
                        fnCreatedCell: function (nTd, sData, oData, iRow, iCol) {
                            if (oData.type == "{{\App\Constants\StatementConstants::DEBIT}}") {
                                $(nTd).html('<span class="badge badge-warning">Debit</span>');
                            } else {
                                $(nTd).html('<span class="badge badge-success">Credit</span>');
                            }
                        }
                    },
                    {data: 'total_formatted', name: 'total'},
                ],
                "language": {
                    "info": "Showing _START_ to _END_ of _TOTAL_ records",
                },
                "footerCallback": function (row, data, start, end, display) {
                    tableFooterSummaryCal();
                }
            });


            $("#transactionsTable_wrapper .tableFilters").html($("#tableFltersGhost").html());

            $(".tableFilters #year_options").select2();
            $(".tableFilters #filter_vendor").select2();
            // $(".tableFilters #filter_category").select2({
            //     placeholder: "Select Category"
            // });

            $.fn.datepicker.defaults.format = "mm/dd/yyyy";
            $(".datePicker").datepicker();

            trans_datatable.draw();
            $(document).on('keyup change', '.filterField', function () {
                trans_datatable.draw();
            });


        });

    </script>


@endsection

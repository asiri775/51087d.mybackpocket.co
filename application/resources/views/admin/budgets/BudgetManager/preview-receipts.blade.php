@extends('admin.layouts.masterToPreviewReceipts')
@section('content')
    <?php
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Input;
    ?>
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
                    <h5><strong>Preview add to Budget</strong></h5>
                </div>
            </div>

            <div class="card-body p-t-20">
                <table style="border:1px dashed black;">
                    <tbody>

                    <tr style="align: center;">
                        <td
                            style=" font-family: 'Montserrat';font-size: 15px; color:#626262; font-weight: bold; text-align: right; border:1px dashed black;">
                            Budget ID # : &nbsp;&nbsp;&nbsp;
                        </td>
                        <td style=" border:1px dashed black; width:20px;"></td>
                        <td
                            style=" font-family: 'Montserrat';font-size: 15px; color:#626262; text-align: left; border:1px dashed black;">
                            &nbsp;&nbsp;{{ $envId }}&nbsp;&nbsp;
                        </td>
                    </tr>
                    <tr style="align: center;">
                        <td
                            style=" font-family: 'Montserrat';font-size: 15px; color:#626262; font-weight: bold; text-align: right; border:1px dashed black;">
                            Budget Name : &nbsp;&nbsp;&nbsp;
                        </td>
                        <td style=" border:1px dashed black; width:20px;"></td>
                        <td
                            style=" font-family: 'Montserrat';font-size: 15px; color:#626262; text-align: left; border:1px dashed black;">
                            &nbsp;&nbsp;{{ $envName }}&nbsp;&nbsp;
                        </td>
                    </tr>
                    <tr style="align: center;">
                        <td
                            style=" font-family: 'Montserrat';font-size: 15px; color:#626262; font-weight: bold; text-align: right; border:1px dashed black;">
                            Category : &nbsp;&nbsp;&nbsp;
                        </td>
                        <td style=" border:1px dashed black; width:20px;"></td>
                        <td
                            style=" font-family: 'Montserrat';font-size: 15px; color:#626262; text-align: left; border:1px dashed black;">
                            &nbsp;&nbsp;{{ $categoryName }}&nbsp;&nbsp;
                        </td>
                    </tr>


                    </tbody>
                </table>
                <form id="store-budget" action="{{ route('add-to-budget') }}" method="POST">
                    <div class="widget-11-2-table p-t-20">
                        <table class="table table-hover table-condensed table-responsive-block table-responsive"
                               id="previewBudgetReceiptTable">
                            <thead>
                            <tr>
                                <th class="text-center" width="1%"></th>
                                <th class="text-center" width="1%">Transaction#</th>
                                <th class="text-center">Client</th>
                                <th class="text-center">Reference#</th>
                                <th class="text-center">Method</th>
                                <th class="text-center">Amount</th>
                                <th class="text-center">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($transactions as $transaction)
                                <tr>
                                    <td><input type="checkbox" name="transId[]" value="{{ $transaction->id }}"
                                               style="text-align: center;"></td>
                                    <td class="text-center"
                                        style="text-align: center;">{{ $transaction->transaction_no }}</td>
                                    <td style="text-align: center;"> {{ $transaction->vendor!=null?$transaction->vendor->name:'-' }}</td>
                                    <td style="text-align: center;">{{ $transaction->order_no }}</td>
                                    <td style="text-align: center;">{{ $transaction->payment_method }}</td>
                                    <td style="text-align: center;">${{ $transaction->total }}</td>
                                    <td style="text-align: center;"><a class="btn btn-danger"
                                                                       href="{{ url('admin/budgets/AddReceipts/delete-receipt/' . $transaction->id) }}">Delete</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="content-group "
                             style="float: right !important; padding-right: 100px; padding-right: 100px; padding-left:20px;  ">
                            <br>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <label style="font-size: 14px;"><b>Summary :
                                                    {{ $transactions->count() }}
                                                    &nbsp;Items&nbsp;|&nbsp;${{ $grandTotal }}</b></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="padding: 25px 20px 20px 20px; ">
                        <div class="pull-left">
                            <button type="button" class="btn btn-default" id="selectAllBudgets">Select All</button>
                            <button type="button" class="btn btn-default" id="deselectAllBudgets">De-Select
                                All
                            </button>
                        </div>
                        <div class="pull-right">
                            @csrf

                            <input type="submit" class="btn btn-info save-confirmation" name="save" disabled
                                   value="Save">
                            <input type="submit" class="btn btn-danger cancel-confirmation" name="cancel"
                                   value="Cancel">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')

@endsection

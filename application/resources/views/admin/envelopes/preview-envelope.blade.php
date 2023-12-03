@extends('admin.layouts.master2')
@section('title', 'Envelope Preview')

@section('page-css')
    <style>
        .dataTables_filter {
            display: none;
        }
    </style>
@endsection
@section('content')
    <?php

    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Input;
    ?>
    <div class="container-fluid container-fixed-lg">
        <!-- START card -->
        @if(Session::has('success'))
            <div class="alert alert-success">{{Session::get('success')}}</div>
        @elseif(Session::has('error'))
            <div class="alert alert-danger">{{Session::get('error')}}</div>
        @endif
        <div class="card card-default">
            <div class="card-header separator">
                <div class="card-title">
                    <h5><strong>Envelope List</strong></h5>
                </div>
            </div>
            <div class="card-body p-t-20">
                <div class="row justify-content-left">
                    <div class="form-group">
                        <div class="col-md-12">
                            <div class="row sales-by client-header-top small-inputs">
                                <div class="row">
                                    <div id="DataTables_Table_0_filter" class="dataTables_filter">
                                        <label class="none-icon">
                                            <span>Envelope To </span>
                                        </label>
                                    </div>
                                    <div id="DataTables_Table_0_filter" class="dataTables_filter">
                                        <label class="none-icon">
                                            <div class="col-xs-11 detaile-box-2">
                                                <select type="select" name="account" id="account"
                                                        data-placeholder="Select" style="width:180px;"
                                                        class="form-control">
                                                    <option value="novalue">Select Bank Account</option>
                                                </select>
                                            </div>
                                        </label>
                                    </div>
                                    <div id="DataTables_Table_0_filter" class="dataTables_filter">
                                        <label class="calender-icon">
                                            <span>Date :</span>
                                            <div class="col-xs-6 detaile-box-2">
                                                <?php
                                                if (!Request::exists('txt_to_date')) {
                                                    $to_date = date('m/d/Y');
                                                } else if (Input::get('txt_to_date') != "") {
                                                    $to_date = Input::get('txt_to_date');
                                                } else {
                                                    $to_date = "";
                                                }
                                                ?>
                                                <input type="text" name="txt_to_date" id="txt_to_date"
                                                       class="text-center form-control daterange-single"
                                                       value="{{ $to_date }}" placeholder="mm/dd/yyyy">
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="datatable-scroll">
                <table class="table table-hover table-condensed table-responsive-block table-responsive"
                       id="envelopeTable">
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
                    @if($transactions)
                        @foreach($transactions as $transaction)
                            <tr>
                                <td><input type="checkbox" name="check[]" value="{{$transaction->id}}"></td>
                                <td class="text-center">{{$transaction->transaction_no}}</td>
                                <td>{{$transaction->vendor!=null ? $transaction->vendor->name:'-'}}</td>
                                <td>{{$transaction->payment_ref}}</td>
                                <td>{{$transaction->payment_method}}</td>
                                <td>{{$transaction->total}}</td>
                                <td><a class="btn btn-danger"
                                       href="{{url('admin/envelopes/previewEnvelope/delete/'.$transaction->id)}}"
                                       style="vertical-align: middle;">Delete</a></td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><p>NO Records</p></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
            <div class="content-group " style="float: right !important; padding-right: 100px; padding-right: 100px;  ">
                <br>
                <div class="form-group">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-xs-12">
                                <label style="font-size: 20px;">No. of Items :
                                    <b>{{($transactions)?$transactions->count():0}}</b></label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <label style="font-size: 20px;">Envelope Total : <b>${{$grandTotal}}</b> </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="padding-left: 20px;padding-right: 20px;">
                <div class="pull-left">
                    <button type="button" class="btn btn-default" id="selectAllInvoices">Select All</button>
                    <button type="button" class="btn btn-default" id="deselectAllInvoices"
                            @if(!Session::has('transaction_id')) disabled @endif>De-Select All
                    </button>
                </div>
                <div class="pull-right">
                    <form id="store-deposit" action="{{ route('envelope-store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="account">
                        <input type="hidden" name="txt_to_date">
                        <input type="submit" class="btn btn-info save-confirmation" name="save" disabled value="Save">
                        <input type="submit" class="btn btn-danger cancel-confirmation" name="cancel" value="Cancel">
                    </form>
                    <br>
                </div>
            </div>
        </div>
    </div>
@endsection




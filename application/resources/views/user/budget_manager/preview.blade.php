@extends('user.layouts.master')
@section('title', 'Budget Manager')
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
    {{-- Modal --}}
    <div class="page-content-wrapper ">
        <!-- START PAGE CONTENT -->
        <div class="content ">
            <!-- START JUMBOTRON -->

            @if (Session::has('success'))
                <div class="alert alert-success">{{ Session::get('success') }}</div>
            @elseif(Session::has('error'))
                <div class="alert alert-danger">{{ Session::get('error') }}</div>
            @endif


            <div class="container-fluid container-fixed-lg">
                <div class="card card-default">
                    <div class="card-header separator">
                        <div class="card-title">
                            <h5><strong>Budget Details&nbsp;&nbsp;:&nbsp; {{ $budgets->name }}</strong></h5>
                        </div>
                        <div class="pull-right">
                            <div class="card-title align-items-center d-flex justify-content-center content-group"
                                style="background-color: #f7efc9;width: 250px;height: 231;height: 50px;font-size: 40px; color:#626262; font-family: Montserrat; margi">
                                <b>${{ $grandTotal }}</b>
                            </div>
                        </div>

                    </div>
                    <div class="card-body p-t-20">
                        <div class="row justify-content-left">

                        </div>

                        <form action="{{ route('user.budget.manager.edit' , $budgets->id) }}" id="user_budget"
                            method="POST">
                            {{ csrf_field() }}
                            <div class="row justify-content-left">
                                <div class="col-md-8">
                                    <div class="form-group" style="float:none;">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label>Budget Name</label>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="text" class="form-control" name="name" id="name"
                                                    value="{{ $budgets->name }}">
                                            </div>
                                            <div class="col-md-1"></div>
                                            <div class="col-md-2">
                                                <label>Budget Category</label>
                                            </div>
                                            <div class="col-md-3">
                                                <select name="category" id="category" class="form-control">
                                                    @foreach ($categories as $category)
                                                        @if ($category->role == 'main')
                                                            <option value="{{ $category->id }}" @if ($budgets->category_id == $category->id) selected @endif>
                                                                {{ $category->name }}</option>
                                                        @else
                                                            <option value="{{ $category->id }}" @if ($budgets->category_id == $category->id) selected @endif>
                                                                &nbsp;&nbsp;&nbsp;&nbsp;{{ $category->name }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label>Target Budget Value $</label>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="number" class="form-control" name="value" id="value"
                                                    value="{{ $budgets->target_budget_value }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="row col-md-12">
                                        <a class="btn btn-primary col-md-5" type="button" style="font-size:17px;"
                                        href="{{route('user.budget.add.reciepts' , $budgets->id) }}" id="sessionSave"
                                        name="add"><i class="fa fa-plus-circle"></i>&nbsp;&nbsp;Add Receipts</a>
                                        <div class="col-md-2"></div>
                                    <a class="btn btn-success col-md-5" type="button" style="font-size:17px;"
                                        href="{{ route('user.budget.add.envelopes' , $budgets->id) }}" id="sessionSave"
                                        name="add"><i class="fa fa-plus-circle"></i>&nbsp;&nbsp;Add Envelopes</a>
                                    </div>

                                </div>
                            </div>

                            <div class="widget-11-2-table p-t-20">
                                <table class="table table-hover table-condensed table-responsive" id="previewTable">

                                    <thead>
                                        <tr>

                                            <th class="text-center" width="1%" colspan="2"
                                                style="background-color: #3b4752; color:#fff">Transaction#</th>
                                            <th class="text-center">Vendor</th>
                                            <th class="text-center">Reference#</th>
                                            <th class="text-center">Method</th>
                                            <th class="text-center">Amount</th>
                                            <th class="text-center" style="width: 215px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($transactions as $transaction)
                                            <tr>
                                                <td><input type="checkbox" name="chk_budget[]"
                                                        value="{{ $transaction->id }}"
                                                        id="chk_budget_{{ $transaction->id }}"></td>
                                                <td class="text-center">{{ $transaction->id }}</td>
                                                <td class="text-center">{{ $transaction->vendor->name }}</td>
                                                <td class="text-center">{{ $transaction->order_no }}</td>
                                                <td class="text-center">{{ $transaction->payment_method }}</td>
                                                <td class="text-center">
                                                    ${{ number_format((float)$transaction->getAmount($transaction->id), 2, '.', '')  }}
                                                </td>
                                                <td class="text-center">

                                                    <a href="{{ route('user.budget.delete.receipt' , $transaction->id) }}"
                                                        onclick="return confirm('Are you sure you want to remove transaction Ref # {{ $transaction->id }} ?')"
                                                        class="btn btn-danger btn-sm "
                                                        style="float:left;margin-right: 5px;">
                                                        Delete
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>



                            <div style="padding-top: 20px;">
                                {{-- <button type="button" class="btn btn-default" id="selectAllReceipts">Select All</button>
                                <button type="button" class="btn btn-default" id="deselectAllReceipts">De-Select All
                                </button> --}}
                                {{-- <form onsubmit="confirmDelete()" id="form-delete" style="display:inline-block;"
                                    action="{{ url('admin/budgets/bulk-delete/' . $budgets->id) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-danger" id="bulkDelete" disabled>Bulk Delete</button>
                                </form> --}}
                            </div>

                            <div class="widget-11-2-table p-t-20">
                                <table class="table table-hover table-condensed table-responsive" id="previewTable">

                                    <thead>
                                        <tr>

                                            <th class="text-center" width="10%" colspan="2"
                                                style="background-color: #3b4752; color:#fff">Envelope#</th>
                                            <th class="text-center">Date</th>
                                            <th class="text-center">Receipts</th>
                                            <th class="text-center">Amount</th>
                                            <th class="text-center" style="width: 215px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($envelopes as $envelope)
                                            <tr>
                                                <td><input type="checkbox" name="chk_budget[]"
                                                        value="{{ $envelope->id }}"
                                                        id="chk_budget_{{ $envelope->id }}"></td>
                                                <td class="text-center"><a target="_blank" href="{{ route('user.preview.envelope', ['Envelope' => $envelope->id]) }}"> {{ $envelope->name }}</a></td>
                                                <td class="text-center">
                                                    {{ date('m/d/Y', strtotime($envelope->envelope_date)) }}</td>
                                                <td class="text-center">
                                                    {{ $envelope->transactionCount($envelope->id)}}
                                                </td>
                                                <td class="text-center">${{ number_format((float)$envelope->EnvelopAmount($envelope->id), 2, '.', '') }}
                                                </td>
                                                <td class="text-center">

                                                    <a href="{{ route('user.budget.delete.envelope' , $envelope->id) }}"
                                                        onclick="return confirm('Are you sure you want to remove envelope Ref # {{ $envelope->id }} ?')"
                                                        class="btn btn-danger btn-sm "
                                                        style="float:left;margin-right: 5px;">
                                                        Delete
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div style="padding-top: 20px;">
                                {{-- <button type="button" class="btn btn-default" id="selectAllReceipts">Select All</button>
                                <button type="button" class="btn btn-default" id="deselectAllReceipts">De-Select All
                                </button> --}}
                                {{-- <form onsubmit="confirmDelete()" id="form-delete" style="display:inline-block;"
                                    action="{{ url('admin/budgets/bulk-delete/' . $budgets->id) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-danger" id="bulkDelete" disabled>Bulk Delete</button>
                                </form> --}}
                            </div>


                            <div class="content-group "
                                style="float: right !important; padding-right: 100px; padding-right: 100px;  ">
                                <br>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <label style="font-size: 14px;"><b>Summary :
                                                    <?php
                                                        $count = $transactions->count() + $envelopes->count();
                                                        ?>
                                                        {{  $count }}&nbsp;Items&nbsp;|&nbsp;${{ number_format((float)$grandTotal, 2, '.', '')  }}</b></label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <label style="font-size: 14px;"><b>Target Budget Value :
                                                        ${{ number_format((float)$transaction->getAmount($transaction->id), 2, '.', '')  }}</$> </label>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-lg btn-success"
                                            style="width: 200px; background-color: green; font-size:17px;">Update and
                                            Save</button>
                                    </div>
                                </div>
                        </form>
                    </div>
                    <div class="pull-left p-t-20">
                        <a class="btn btn-lg btn-primary" style="font-size:17px;" type="button"
                            href="{{ route('user.budget.add.reciepts' , $budgets->id) }}" id="sessionSave" name="add"><i
                                class="fa fa-plus-circle"></i>&nbsp;&nbsp;Add Receipts</a>
                        <a class="btn btn-lg btn-success" style="font-size:17px;" type="button"
                            href="{{ route('user.budget.add.reciepts' , $budgets->id) }}" id="sessionSave" name="add"><i
                                class="fa fa-plus-circle"></i>&nbsp;&nbsp;Add Envelopes</a>
                        <a href="{{ url('user/budget-manager') }}" class="btn btn-info btn-md">Back to Budgets</a>
                        <a href="{{ route('user.dashboard') }}" class="btn btn-primary btn-md">Back to Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

@endsection

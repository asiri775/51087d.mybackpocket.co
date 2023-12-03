@extends('admin.layouts.newMaster')
@section('title', $title)
@section('content')
    <style>
        .wire-grid-box {
            margin-top: 0;
            padding-top: 0;
        }
        button[id$="bulkActions"] {
            display: none !important;
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
                                                <p><strong>Total: {{$stats['totalDebits'] + $stats['totalCredits']}}</strong></p>
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
                                            <p class="text-right">-{{\App\Helpers\Helper::printAmount($stats['paymentsAndCredits'])}}</p>
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

                <div class="card card-default">
                    <div class="card-body">
                        <div class="wire-grid-box">
                            @livewire('bank-account-transactions-component', compact('bankStatement'))
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

@endsection

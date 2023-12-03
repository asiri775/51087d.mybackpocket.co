@extends('admin.layouts.masterToExistingEnvelope')

@section('title', 'Existing Envelope List')


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
    <div class="content">
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
                        <h5><strong>Select Envelope</strong></h5>
                    </div>

                    <div class="card-body p-t-20">
                        <form id="filter-form">
                            @csrf
                            {{--                    <fieldset class="content-group">--}}
                            {{--                        <div class="form-group">--}}
                            {{--                            <div class="col-md-12">--}}
                            {{--                                <div class="row sales-by client-header-top small-inputs">--}}
                            {{--                                    <div class="row">--}}
                            {{--                                        <div id="DataTables_Table_0_filter" class="dataTables_filter">--}}
                            {{--                                            <label class="none-icon">--}}
                            {{--                                                <span>Bank Account:</span>--}}
                            {{--                                                <div class="col-xs-4 detaile-box-2">--}}
                            {{--                                                    <select type="select" name="account"  id="account"  data-placeholder="Select" style="width:180px;" class="form-control">--}}
                            {{--                                                        <option value="novalue">Select Bank Account</option>--}}
                            {{--                                                        @foreach($accounts as $account)--}}
                            {{--                                                            <option value="{{$account->id}}">{{$account->name}} - {{$account->account_number}}</option>--}}
                            {{--                                                        @endforeach--}}
                            {{--                                                    </select>--}}
                            {{--                                                </div>--}}
                            {{--                                            </label>--}}
                            {{--                                        </div>--}}
                            {{--                                        <div id="DataTables_Table_0_filter" class="dataTables_filter">--}}
                            {{--                                            <label class="none-icon">--}}
                            {{--                                                <span>Show:</span>--}}
                            {{--                                                <div class="col-xs-11 detaile-box-2">--}}
                            {{--                                                    <select type="select" name="quickdate" id="quickdate"--}}
                            {{--                                                            data-placeholder="Select" class="form-control input-sm"--}}
                            {{--                                                            data-column="0">--}}
                            {{--                                                        <option value="">Quick Date</option>--}}
                            {{--                                                        <option value="yesterday">Yesterday</option>--}}
                            {{--                                                        <option value="today">Today</option>--}}
                            {{--                                                        <option value="tomorrow">Tomorrow</option>--}}
                            {{--                                                        <option value="weekday">This Weekdays</option>--}}
                            {{--                                                        <option value="wholeweek">This Whole Week</option>--}}
                            {{--                                                        <option value="nextweek">Next Weekdays</option>--}}
                            {{--                                                        <option value="thismonth">This Month</option>--}}
                            {{--                                                        <option value="nextmonth">Next Month</option>--}}
                            {{--                                                        <option value="thisyear">This Year</option>--}}
                            {{--                                                        <option value="yeartodate">Year to Date</option>--}}
                            {{--                                                        <option value="alltime">All Time</option>--}}
                            {{--                                                    </select>--}}
                            {{--                                                </div>--}}
                            {{--                                            </label>--}}
                            {{--                                        </div>--}}
                            {{--                                        <div id="DataTables_Table_0_filter" class="dataTables_filter">--}}
                            {{--                                            <label class="none-icon">--}}
                            {{--                                                <div class="col-xs-11 detaile-box-2">--}}
                            {{--                                                    <select type="select" name="payment_method" style="width:180px;" data-column="3" id="col3_filter" class="form-control">--}}
                            {{--                                                        <option value="">Select Method</option>--}}
                            {{--                                                        @foreach ($methods as $method)--}}
                            {{--                                                            <option value="{{$method->UID}}">{{$method->NAME}}</option>--}}
                            {{--                                                        @endforeach--}}
                            {{--                                                    </select>--}}
                            {{--                                                </div>--}}
                            {{--                                            </label>--}}
                            {{--                                        </div>--}}
                            {{--                                    </div>--}}
                            {{--                                    <div id="DataTables_Table_0_filter" class="dataTables_filter">--}}
                            {{--                                        <label class="none-icon">--}}
                            {{--                                            <div class="col-xs-11 detaile-box-2">--}}
                            {{--                                                <select type="select" name="deposit_status" style="width:120px;" data-column="5" id="col5_filter" class="form-control">--}}
                            {{--                                                    <option value="">Deposit Status</option>--}}
                            {{--                                                    <option value="1">Pending</option>--}}
                            {{--                                                    <option value="2">Completed</option>--}}
                            {{--                                                </select>--}}
                            {{--                                            </div>--}}
                            {{--                                        </label>--}}
                            {{--                                    </div>--}}
                            {{--                                    <div class="row">--}}
                            {{--                                        <div id="DataTables_Table_0_filter" class="dataTables_filter">--}}
                            {{--                                            <label class="calender-icon">--}}
                            {{--                                                <span>FROM :</span>--}}
                            {{--                                                <div class="col-xs-6 detaile-box-2">--}}
                            {{--                                                    <input type="text" name="start_date" id="txt_from_date"--}}
                            {{--                                                           class="text-center form-control new-picker"--}}
                            {{--                                                           placeholder="mm/dd/yyyy">--}}
                            {{--                                                </div>--}}
                            {{--                                            </label>--}}
                            {{--                                        </div>--}}
                            {{--                                        <div id="DataTables_Table_0_filter" class="dataTables_filter">--}}
                            {{--                                            <label class="calender-icon">--}}
                            {{--                                                <span>TO :</span>--}}
                            {{--                                                <div class="col-xs-6 detaile-box-2">--}}
                            {{--                                                    <input type="text" name="end_date" id="txt_to_date"--}}
                            {{--                                                           class="text-center form-control new-picker"--}}
                            {{--                                                           placeholder="mm/dd/yyyy">--}}
                            {{--                                                </div>--}}
                            {{--                                            </label>--}}
                            {{--                                        </div>--}}
                            {{--                                        <div id="DataTables_Table_0_filter" class="dataTables_filter">--}}
                            {{--                                            <label class="none-icon">--}}
                            {{--                                                <div class="col-xs-12 detaile-box-2">--}}
                            {{--                                                    <button type="button" name="btn_generate" id="btn_generate"--}}
                            {{--                                                            class="btn btn-primary">Filter--}}
                            {{--                                                    </button>--}}
                            {{--                                                    <button type="button" name="btn_clear" id="btn_clear"--}}
                            {{--                                                            style="margin-left:25px;" class="btn btn-danger">Clear--}}
                            {{--                                                        Filter--}}
                            {{--                                                    </button>--}}

                            {{--                                                </div>--}}
                            {{--                                            </label>--}}
                            {{--                                        </div>--}}
                            {{--                                    </div>--}}

                            {{--                                </div>--}}
                            {{--                            </div>--}}
                            {{--                        </div>--}}
                            {{--                    </fieldset>--}}
                        </form>
                    </div>

                    <div class="sales-by-clent-middle">
                    </div>
                    <div class="datatable-scroll">
                        <table class="table table-hover table-condensed table-responsive-block table-responsive"
                               id="existingEnvelopeTable">
                            <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="15%" class="text-center"><a>Envelope Id#</a></th>
                                <th width="12%" class="text-center"><a>Envelope Date</a></th>
                                <th width="18%" class="text-center"><a>Account Number</a></th>
                                <th width="20%" class="text-center"><a>Account Name</a></th>
                                <th width="15%" class="text-center"><a>Bank Name</a></th>
<!--                                <th width="15%" class="text-center"><a>Envelope Status</a></th>-->
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                @foreach($allEnvelopes as $envelope)
                                    <td><input type="radio" name="check[]" id="chk_envelope_{{$envelope->id}}" value="{{$envelope->id}}"></td>
                                    <td>{{$envelope->id}}</td>
                                    <td>{{$envelope->envelope_date}}</td>
                                    <td>account number</td>
                                    <td>account name</td>
                                    <td>bank name</td>
<!--                                    <td>@if($envelope->envelope_status == 2)
                                            <p>Done</p>
                                        @else
                                            <p>Pending</p>
                                        @endif</td>-->
<!--                                    <td></td>-->
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="left-pagination">
                </div>
                <div style="padding-left: 20px;padding-right: 20px; ">
                    <div class="pull-left">
                    </div>
                    <div class="pull-right" style="padding-bottom: 20px;">
                        <form id="store-deposit" action="{{ route('add-to-exsisting-envelope') }}" method="POST">
                            @csrf
                            <input type="hidden" name="envelope_id" id="envelope_id" value="">
                            <input type="submit" class="btn btn-info save-continue" id="save-continue"
                                   name="continue" disabled value="Next">
                            <input type="submit" class="btn btn-danger cancel-confirmation" name="cancel"
                                   value="Cancel">
                        </form>
                    </div>
                </div>
            </div>
        </div>
@endsection
@section('script')
@endsection

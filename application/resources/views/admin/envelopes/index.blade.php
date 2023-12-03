@extends('admin.layouts.masterToEnvelope')

@section('title', 'Envelope Preview')


@section('content')
    <?php

    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Input;
    ?>
    <div class="content">
        @if(Session::has('success'))
            <div class="alert alert-success">{{Session::get('success')}}</div>
        @elseif(Session::has('error'))
            <div class="alert alert-danger">{{Session::get('error')}}</div>
        @endif

        <div class="container-fluid container-fixed-lg">
            <!-- START card -->
            <div class="card card-default">
                <div class="card-header separator">
                    <div class="card-title">
                        <h5><strong>Envelopes</strong></h5>
                    </div>
                </div>
                <div class="card-body p-t-20">
                    <div class="panel-body sales-by-client">
                        <fieldset class="content-group">
                            <form id="filter-form">
                                @csrf
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <div class="row sales-by client-header-top small-inputs">
                                            <div class="row"> 
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </fieldset>
                    </div>
                    <div class="sales-by-clent-middle">
                        <div class="sales-by-clent-record">
                            <div class="record-border">
                            </div>
                        </div>
                    </div>
                    <div class="datatable-scroll">
                        <table class="table table-hover table-condensed table-responsive-block table-responsive"
                               id="envelopesTable">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th class="text-center">Env Id</th>
                                <th class="text-center">Date</th>
                                <th class="text-center">Amount</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($envelopes as $envelope)
                                <tr>
                                    <td><input type="checkbox" name="check[]" value="{{$envelope->id}}"></td>
                                    <td>{{$envelope->id}}</td>
                                    <td>{{$envelope->envelope_date}}</td>
                                    <td> Amount</td>
                                    <td>{{$envelope->envelope_status}}</td>
                                    <td>
                                        <div class="btn-group">
{{--                                                        <a href="' . url('envelopes/Complete') . '/' . $envelope->id . '"style="margin-right:5px;" onclick="return confirm( 'Are you sure you want to complete deposit Ref#{$deposit_id} ?' )"--}}
{{--                                                                                                            class="btn btn-success btn-sm complete-confirmation">Complete</a>--}}
{{--                                                       <a style="margin-right:5px;" class="btn btn-info" href="' . url('banking/deposit/download') . '/' . $envelope->id . '" >Download</a>--}}
                                                        <a style="width:85px;" class="btn btn-primary" href="{{url('admin/envelopes/print')
                                                           . '/' .$envelope->id }}">Print</a>
                                            <a href= "{{url('admin/envelopes/preview') . '/' . $envelope->id}}" class="btn btn-success btn-sm">Preview</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div style="padding-bottom: 20px;">
                    <button type="button" class="btn btn-default" id="selectAllInvoices">Select All</button>
                    <button type="button" class="btn btn-default" id="deselectAllInvoices" disabled>De-Select All
                    </button>
                    <form id="form-complete" style="display:inline-block;"
                          action="{{route('envelope-bulkComplete')}}" method="POST">
                        @csrf
                        <button class="btn btn-success" id="bulkComplete" disabled>Bulk Complete</button>
                        <br>
                    </form>
                    <form id="form-download" style="display:inline-block;"
                          action="{{route('bulk-download')}}" method="POST">
                        @csrf
                        <button class="btn btn-info" id="bulkDownload" disabled>Bulk Download</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
  
@endsection

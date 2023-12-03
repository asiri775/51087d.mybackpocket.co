@extends('admin.layouts.masterToEnvelope')

@section('title', 'Envelope Details Report')

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
    <script type="text/javascript">
        function modalSend(envelop_id) {
            $('#envelop_id').val(envelop_id);
        }
    </script>
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
                            <h5><strong>Envelope Details Report</strong></h5>
                        </div>
                    </div>
                    <div class="card-body p-t-20">

                        <div class="row">
                            <div class="row justify-content-left p-l-50 col-md-8">
                                <table style="border:1px dashed black;">
                                    <tbody>

                                        <tr style="align: center;">
                                            <td
                                                style=" font-family: 'Montserrat';font-size: 15px; color:#626262; font-weight: bold; text-align: right; border:1px dashed black;">
                                                Envelope ID # : &nbsp;&nbsp;&nbsp;</td>
                                            <td style=" border:1px dashed black; width:20px;"></td>
                                            <td
                                                style=" font-family: 'Montserrat';font-size: 15px; color:#626262; text-align: left; border:1px dashed black;">
                                                &nbsp;&nbsp;{{ $envelopes->id }}&nbsp;&nbsp;</td>
                                        </tr>
                                        <tr style="align: center;">
                                            <td
                                                style=" font-family: 'Montserrat';font-size: 15px; color:#626262; font-weight: bold; text-align: right; border:1px dashed black;">
                                                Envelope Name : &nbsp;&nbsp;&nbsp;</td>
                                            <td style=" border:1px dashed black; width:20px;"></td>
                                            <td
                                                style=" font-family: 'Montserrat';font-size: 15px; color:#626262; text-align: left; border:1px dashed black;">
                                                &nbsp;&nbsp;{{ $envelopes->name }}&nbsp;&nbsp;</td>
                                        </tr>
                                        <tr style="align: center;">
                                            <td
                                                style=" font-family: 'Montserrat';font-size: 15px; color:#626262; font-weight: bold; text-align: right; border:1px dashed black;">
                                                Category : &nbsp;&nbsp;&nbsp;</td>
                                            <td style=" border:1px dashed black; width:20px;"></td>
                                            <td
                                                style=" font-family: 'Montserrat';font-size: 15px; color:#626262; text-align: left; border:1px dashed black;">
                                                &nbsp;&nbsp;{{ $categoryName }}&nbsp;&nbsp;</td>
                                        </tr>


                                    </tbody>
                                </table>
                            </div>

{{--                            <div class="pull-right col-md-4">--}}
{{--                                <div class="pull-right" style="">--}}
{{--                                    <a class="btn btn-primary"--}}
{{--                                        href="{{ url('admin/reports/print') . '/' . $envelopes->id }}"--}}
{{--                                        data-toggle="tooltip" data-placement="bottom" onclick="window.print()" title="Print"><i--}}
{{--                                            class="fa fa-print fa-10x"></i></a>--}}
{{--                                    <a class="btn btn-danger"--}}
{{--                                        href="{{ url('admin/reports/download') . '/' . $envelopes->id }}"--}}
{{--                                        data-toggle="tooltip" data-placement="bottom" title="Download"><i--}}
{{--                                            class="fa fa-download"></i></a>--}}
{{--                                    <a data-toggle="modal" data-target="#link" class="btn btn-success btn-md" onclick="modalSend(<?=$envelopes->id?>)"><span--}}
{{--                                            style="color: aliceblue;">Share Link</span></a>--}}
{{--                                </div>--}}

{{--                            </div>--}}
                        </div>

                        <div class="widget-11-2-table p-t-20">
                            <table class="table table-hover table-condensed table-responsive" id="previewTable">

                                <thead>
                                    <tr>
                                        <th class="text-center" width="1%">Transaction#</th>
                                        <th class="text-center">Vendor</th>
                                        <th class="text-center">Reference#</th>
                                        <th class="text-center">Method</th>
                                        <th class="text-center">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>


                                    @foreach ($transactions as $transaction)
                                        <tr>
                                            <td class="text-center">{{ $transaction->id }}</td>
                                            <td class="text-center">{{ $transaction->vendor->name }}</td>
                                            <td class="text-center">{{ $transaction->order_no }}</td>
                                            <td class="text-center">{{ $transaction->payment_method }}</td>
                                            <td class="text-center">${{ $transaction->getAmount($transaction->id) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="content-group"
                            style="float: right !important; padding-right: 100px; padding-right: 100px;  ">
                            <br>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <label style="font-size: 14px;"><b>Summary :
                                                    {{ $transactions->count() }}&nbsp;Items&nbsp;|&nbsp;${{ $grandTotal }}</b></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="left-pagination" style="padding-top: 20px;">
                            <a href="{{ url('admin/reports/users/').'/'.$envelopes->enveloped_by }}" class="btn btn-info btn-md">Back to User Report</a>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-md">Back to Home</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div id="link" class="modal fade" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button><br>
                    <h4 class="modal-title"><strong>Share Report</strong></h4>
                </div>
                <form method="post" action="{{route('preview.notify.report')}}">
                 {{ csrf_field() }}
                 {{ method_field('POST') }}
                <div class="modal-body">
                    <br>
                    <p style="font-size: 20px;">Enter Your Email</p>
                    <input type="text" placeholder="email@domain.com" class="form-control" name="send_email">
                    <input type="hidden" name="envelop_id" value="" id="envelop_id">
                </div>
                <div class="modal-footer">
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-danger">Send</button>
                            <button type="button" class="btn btn-primary" data-dismiss="modal">No</button>
                        </div>
                </div>
                </form>
            </div>
        </div>
    </div>



@endsection

@section('script')


    <script type="text/javascript">
        function dipositMove(id) {
            var urlpost = "{{ url('banking/session/addInvoice') }}" + "/" + id;
            $.ajax({
                type: 'POST',
                url: urlpost,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data) {
                    window.location.href = "{{ url('banking/home/selectExistingDeposit') }}";
                }
            });
        }

    </script>

    <script type="text/javascript">
        var array = [];
        $(document).ready(function() {
            //handling select all deselect process
            $('#selectAllInvoices').click(function() {
                var isChecked = $(this).prop("checked");
                $('#list tr:has(td)').find('input[type="checkbox"]').prop('checked', isChecked);
                var msg = "";
                $('#list tr:has(td)').find('input[type="checkbox"]').each(function() {
                    var id = $(this).val();
                    window.array.push(id);

                });
                $('#bulkDelete').prop('disabled', false);

            });
            $('#deselectAllInvoices').click(function() {
                var isChecked = $(this).prop("checked");
                $('#list tr:has(td)').find('input[type="checkbox"]').prop('checked', isChecked);
                var msg = "";
                $('#list tr:has(td)').find('input[type="checkbox"]').each(function() {
                    var id = $(this).val();
                    window.array.splice(window.array.indexOf(id), 1);
                });
                $('#bulkDelete').prop('disabled', true);

            });

            $('#list').on('click', 'input', function() {
                // console.log(this.is(':checked'));
                var isChecked = $(this).prop('checked');

                var id = $(this).val();
                if ($(this).is(':checked')) {
                    window.array.push(id);
                } else {
                    window.array.splice(window.array.indexOf(id), 1);
                }

                if (window.array.length > 0) {
                    if (window.array.length > 1) {
                        $('#bulkDelete').prop('disabled', false);
                    }
                    if (window.array.length < 2) {
                        $('#bulkDelete').prop('disabled', true);

                    }
                    $('#deselectAllInvoices').prop('disabled', false);
                } else {
                    $('#bulkDelete').prop('disabled', true);
                    $('#deselectAllInvoices').prop('disabled', true);

                }

            });


            $('#bulkDelete').click(function() {
                for ($i = 0; $i < window.array.length; $i++) {
                    $('#form-delete').append(
                        $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', 'deposit_id[]')
                        .val(window.array[$i])
                    );
                }
            });
            $("#selectAllInvoices").on("click", function(e) {
                var table = $("#list");
                var boxes = $('input:checkbox', table);
                $.each($('input:checkbox', table), function() {

                    $(this).attr('checked', true);
                    $(this).parent().attr('class', 'checked');

                });
                $('#deselectAllInvoices').prop('disabled', false);
                $('#selectAllInvoices').prop('disabled', true);
            });

            $("#deselectAllInvoices").on("click", function(e) {
                var table = $("#list");
                var boxes = $('input:checkbox', table);
                $.each($('input:checkbox', table), function() {

                    $(this).attr('checked', false);
                    $(this).parent().removeAttr('class');

                });
                $('#selectAllInvoices').prop('disabled', false);
                $('#deselectAllInvoices').prop('disabled', true);
            });


            //end of bulk complete / bulk download

        });

        function confirmDelete() {

            return confirm('Are you sure you want to remove invoice Ref# ' + array + ' ?');
        }

    </script>

@endsection

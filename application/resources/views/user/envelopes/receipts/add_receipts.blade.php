@extends('user.layouts.master')
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
                    <h5><strong>Preview add to Envelope</strong></h5>
                </div>
            </div>

            <div class="card-body p-t-20">

                <table  style="border:1px dashed black;">
                    <tbody>

                    <tr style="align: center;">
                        <td style=" font-family: 'Montserrat';font-size: 15px; color:#626262; font-weight: bold; text-align: right; border:1px dashed black;">Envelope ID # : &nbsp;&nbsp;&nbsp;</td>
                        <td style=" border:1px dashed black; width:20px;"></td>
                        <td style=" font-family: 'Montserrat';font-size: 15px; color:#626262; text-align: left; border:1px dashed black;">&nbsp;&nbsp;{{ $envId }}&nbsp;&nbsp;</td>
                    </tr>
                    <tr style="align: center;">
                        <td style=" font-family: 'Montserrat';font-size: 15px; color:#626262; font-weight: bold; text-align: right; border:1px dashed black;">Envelope Name : &nbsp;&nbsp;&nbsp;</td>
                        <td style=" border:1px dashed black; width:20px;"></td>
                        <td style=" font-family: 'Montserrat';font-size: 15px; color:#626262; text-align: left; border:1px dashed black;">&nbsp;&nbsp;{{ $envName }}&nbsp;&nbsp;</td>
                    </tr>
                    <tr style="align: center;">
                        <td style=" font-family: 'Montserrat';font-size: 15px; color:#626262; font-weight: bold; text-align: right; border:1px dashed black;">Category : &nbsp;&nbsp;&nbsp;</td>
                        <td style=" border:1px dashed black; width:20px;"></td>
                        <td style=" font-family: 'Montserrat';font-size: 15px; color:#626262; text-align: left; border:1px dashed black;">&nbsp;&nbsp;{{$categoryName}}&nbsp;&nbsp;</td>
                    </tr>


                    </tbody>
                </table>
                <form id="store-envelope" action="{{ route('user.add.receipt.addToEnvelope') }}" method="POST">
                    <div class="widget-11-2-table p-t-20">
                        <table class="table table-hover table-condensed table-responsive-block table-responsive"
                               id="previewExistingEnvelopeTable">
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
                                    <td class="text-center"><input type="checkbox" name="transId[]" value="{{ $transaction->id }}" ></td>
                                    <td class="text-center">{{ $transaction->transaction_no }}</td>
                                    <td class="text-center">{{ $transaction->vendor->name }}</td>
                                    <td class="text-center">{{ $transaction->order_no }}</td>
                                    <td class="text-center">{{ $transaction->payment_method }}</td>
                                    <td class="text-center">${{ number_format((float)$transaction->total, 2, '.', '') }}</td>
                                    <td class="text-center"><a class="btn btn-danger"
                                                               href="{{ route('user.receipt.delete', $transaction->id) }}">Delete</a>
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
                                                    {{ $transactions->count() }}&nbsp;Items&nbsp;|&nbsp;${{ $grandTotal }}</b></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="padding: 25px 20px 20px 20px; ">
                        <div class="pull-left">
                            <button type="button" class="btn btn-default" id="selectAllEnvelopes">Select All</button>
                            <button type="button" class="btn btn-default" id="deselectAllEnvelopes" @if (!Session::has('envelope_id')) disabled @endif>De-Select
                                All
                            </button>
                        </div>
                        <div class="pull-right">
                            @csrf
                            <input type="hidden" name="envelope_id" >
                            <input type="submit" class="btn btn-info save-confirmation" name="save" disabled value="Save">
                            <input type="submit" class="btn btn-danger cancel-confirmation" name="cancel" value="Cancel">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('page-js')
<script type="text/javascript">

    $('.cancel-confirmation').on('click', function () {
        return confirm('Do you really want to cancel envelope?');
    });
    $('.save-confirmation').on('click', function () {
        var name = $('#envName').val();
        return confirm('Do you really want to save envelope ?');
    });

    $(document).ready(function () {
        var array = [];
        $("#selectAllEnvelopes").on("click", function (e) {
            var table = $("#previewExistingEnvelopeTable");
            var boxes = $('input:checkbox', table);
            $.each($('input:checkbox', table), function () {

                $(this).parent().addClass('checked');
                $(this).prop('checked', 'checked');

            });
            $('#selectAllEnvelopes').prop('disabled', false);
            $('#bulkDelete').prop('disabled', false);
            $('.save-confirmation').prop('disabled', false);
        });

        $("#deselectAllEnvelopes").on("click", function (e) { 
            var table = $("#previewExistingEnvelopeTable");
            var boxes = $('input:checkbox', table);
            $.each($('input:checkbox', table), function () {

                $(this).parent().removeClass('checked');
                $(this).prop('checked', false);

            });
            $('#bulkDelete').prop('disabled', true);
            $('.save-confirmation').prop('disabled', true);
        });


        $('#previewExistingEnvelopeTable').on('click', 'input', function () {
            // console.log(this.is(':checked'));         
            var status = $(this).is(':checked');        
            if (status) {
                $('#bulkDelete').prop('disabled', false);
                $('.save-confirmation').prop('disabled', false);
               
            } else {
                $('#bulkDelete').prop('disabled', true);
                $('.save-confirmation').prop('disabled', true);
            }
        });
        $('#bulkDelete').click(function () {
            for ($i = 0; $i < array.length; $i++) {
                $('#form-delete').append(
                    $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', 'envelope_id[]')
                        .val(array[$i])
                );
            }
        });
        $('#envelope_id').on('change', function () {
            var deposit_id = $(this).val();
            $('#store-envelope input[name="envelope_id"]').val(envelope_id);
        });

        $(document).ready(function(){
            $("#selectAllEnvelopes").click();
        });
    });
</script>
@endsection

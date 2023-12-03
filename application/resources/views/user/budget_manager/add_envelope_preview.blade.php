@extends('user.layouts.master')
@section('title', 'Budget Manager')
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
                    <h5><strong>Preview add envelopes to Budget</strong></h5>
                </div>
            </div>

            <div class="card-body p-t-20">
                <table style="border:1px dashed black;">
                    <tbody>

                        <tr style="align: center;">
                            <td
                                style=" font-family: 'Montserrat';font-size: 15px; color:#626262; font-weight: bold; text-align: right; border:1px dashed black;">
                                Budget ID # : &nbsp;&nbsp;&nbsp;</td>
                            <td style=" border:1px dashed black; width:20px;"></td>
                            <td
                                style=" font-family: 'Montserrat';font-size: 15px; color:#626262; text-align: left; border:1px dashed black;">
                                &nbsp;&nbsp;{{ $budget->id }}&nbsp;&nbsp;</td>
                        </tr>
                        <tr style="align: center;">
                            <td
                                style=" font-family: 'Montserrat';font-size: 15px; color:#626262; font-weight: bold; text-align: right; border:1px dashed black;">
                                Budget Name : &nbsp;&nbsp;&nbsp;</td>
                            <td style=" border:1px dashed black; width:20px;"></td>
                            <td
                                style=" font-family: 'Montserrat';font-size: 15px; color:#626262; text-align: left; border:1px dashed black;">
                                &nbsp;&nbsp;{{ $budget->name }}&nbsp;&nbsp;</td>
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
                <form id="store-budget" action="{{ route('user.budget.add.envelope.store') }}" method="POST">
                    <div class="widget-11-2-table p-t-20">
                        <table class="table table-hover table-condensed table-responsive-block table-responsive"
                            id="previewBudgetReceiptTable">
                            <thead>
                                <tr>
                                    <th class="text-center" width="1%"></th>
                                    <th class="text-center" width="1%">Envelope#</th>
                                    <th class="text-center">Category</th>
                                    <th class="text-center">Vendor</th>
                                    <th class="text-center">Amount</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($envelopes as $envelope)
                                    <tr>
                                        <td><input type="checkbox" name="envId[]" value="{{ $envelope->id }}" style="text-align: center;"></td>
                                        <td class="text-center" style="text-align: center;">{{ $envelope->name }}</td>
                                        <td style="text-align: center;"> {{ $envelope->category->name }}</td>
                                        <td style="text-align: center;"> @if( $envelope->vendor){{ $envelope->vendor->name }}@else No vendor @endif</td>
                                        <td style="text-align: center;">${{ $envelope->EnvelopAmount($envelope->id) }}</td>
                                        <td style="text-align: center;"><a class="btn btn-danger"
                                                href="{{ route('user.budget.add.envelope.delete' , $envelope->id) }}">Delete</a>
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
                                                    {{ $envelopes->count() }}&nbsp;Items&nbsp;|&nbsp;${{ $grandTotal }}</b></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="padding: 25px 20px 20px 20px; ">
                        <div class="pull-left">
                            <button type="button" class="btn btn-default" id="selectAllBudgets">Select All</button>
                            <button type="button" class="btn btn-default" id="deselectAllBudgets" >De-Select
                                All
                            </button>
                        </div>
                        <div class="pull-right">
                            @csrf

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
        return confirm('Do you really want to cancel budget ?');
    });
    $('.save-confirmation').on('click', function () {
        var name = $('#envName').val();
        return confirm('Do you really want to save budget ?');
    });

    $(document).ready(function () {
        var array = [];
        $("#selectAllBudgets").on("click", function (e) {
            var table = $("#previewBudgetReceiptTable");
            var boxes = $('input:checkbox', table);
            $.each($('input:checkbox', table), function () {

                $(this).parent().addClass('checked');
                $(this).prop('checked', 'checked');

            });
            $('#selectAllBudgets').prop('disabled', false);
            $('#bulkDelete').prop('disabled', false);
            $('.save-confirmation').prop('disabled', false);
        });

        $("#deselectAllBudgets").on("click", function (e) {
            var table = $("#previewBudgetReceiptTable");
            var boxes = $('input:checkbox', table);
            $.each($('input:checkbox', table), function () {

                $(this).parent().removeClass('checked');
                $(this).prop('checked', false);

            });
            $('#bulkDelete').prop('disabled', true);
            $('.save-confirmation').prop('disabled', true);
        });


        $('#previewBudgetReceiptTable').on('click', 'input', function () {
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
            $("#selectAllBudgets").click();
        });
    });
</script>
@endsection

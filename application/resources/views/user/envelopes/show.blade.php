@extends('user.layouts.master')

@section('title', 'Edit Envelopes')

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

                            <div class="pull-left">
                                <h5><strong>Envelope Details&nbsp;&nbsp;:&nbsp; {{ $envelope->name }}</strong></h5>
                            </div>

                        </div>


                        <div class="pull-right">
                            <div class="card-title align-items-center d-flex justify-content-center content-group"
                                 style="background-color: #f7efc9;width: 250px;height: 231;height: 50px;font-size: 40px; color:#626262; font-family: Montserrat;">
                                <b>${{ $grandTotal }}</b>
                            </div>
                        </div>


                    </div>
                    <div class="card-body p-t-20">
                        <div class="row justify-content-left">

                        </div>

                        <form action="{{ route('user.envelope.edit' , $envelope->id) }}" id="user_envelope"
                              method="POST">
                            {{ csrf_field() }}
                            <div class="row justify-content-left">
                                <div class="col-md-12">
                                    <div class="form-group" style="float:none;">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label>Envelope Name</label>
                                                <input type="text" class="form-control" name="name" id="name"
                                                       value="{{ $envelope->name }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label>Envelope Category</label>
                                                <select name="category" id="category" class="form-control">
                                                      @foreach ($categories as $category)
                                                         @if($category->role=='main')
                                                           <option value="{{ $category->id }}" @if($envelope->category_id==$category->id) selected @endif >{{ $category->name }}</option>
                                                         @else
                                                           <option value="{{ $category->id }}" @if($envelope->category_id==$category->id) selected @endif >&nbsp;&nbsp;&nbsp;&nbsp;{{ $category->name }}</option>
                                                         @endif

                                                    @endforeach
                                                </select>
                                                <br>
                                            </div>
                                            {{-- <div class="col-md-3">
                                                <label>Vendor</label>
                                                <select name="vendor" id="vendor" class="form-control">
                                                    <option value="">Select a Vendor</option>
                                                    @foreach ($vendors as $vendor)
                                                        <option value="{{ $vendor->id }}" @if($envelope->vendor_id == $vendor->id) selected="selected"@endif>{{ $vendor->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div> --}}
                                            <div class="col-md-3">
                                                <br>
                                                <a class="btn btn-lg btn-primary" type="button" style="font-size:17px;"
                                                   href="{{route('user.envelope.add.reciepts', ['Envelope' => $envelope->id]) }}"
                                                   id="sessionSave" name="add"><i
                                                            class="fa fa-plus-circle"></i>&nbsp;&nbsp;Add Receipts</a>

                                            </div>
                                        </div>

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
                                        @if ($envelope->envelope_status == 1)
                                            <th class="text-center" style="width: 215px;">Action</th>
                                        @endif
                                    </tr>
                                    </thead>
                                    <tbody>


                                    @foreach ($transactions as $transaction)
                                        <tr>
                                            @if ($envelope->envelope_status == 1)
                                                {{-- <td><input type="checkbox" name="transId[]" value="{{ $transaction->id }}" ></td> --}}
                                                <td><input type="checkbox" name="chk_deposit[]"
                                                           value="{{ $transaction->id }}"
                                                           id="chk_deposit_{{ $transaction->id }}"></td>
                                            @endif
                                            <td class="text-center">{{ $transaction->id }}</td>
                                            <td class="text-center">{{ $transaction->vendor->name }}</td>
                                            <td class="text-center">{{ $transaction->order_no }}</td>
                                            <td class="text-center">{{ $transaction->payment_method }}</td>
                                            <td class="text-center">${{ number_format((float)$transaction->getAmount($transaction->id), 2, '.', '')  }}
                                            </td>

                                            <td class="text-center">
                                                <a type="submit"
                                                   href="{{ route('user-delete-envelope-item', ['envelope' => $envelope->id,  'id' => $transaction->id] ) }}"
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
                            <div class="content-group "
                                 style="float: right !important; padding-right: 100px; padding-right: 100px;  ">
                                <br>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <label style="font-size: 14px;"><b>Summary :
                                                        {{ count($transactions) }}&nbsp;Items&nbsp;|&nbsp;${{ number_format((float)$grandTotal, 2, '.', '')  }}</b></label>
                                            </div>
                                        </div>

                                    </div>
                                    <br>
                                    <div class="pull-right">
                                        <button type="submit" class="btn btn-lg btn-success"
                                                style="width: 200px; background-color: green; font-size:17px;">Update and
                                            Save</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="left-pagination" style="padding-top: 20px;">
                            <button type="button" class="btn btn-default" id="selectAllReceipts">Select All</button>
                            <button type="button" class="btn btn-default" id="deselectAllReceipts">De-Select
                                All
                            </button>
                           <form onsubmit="confirmDelete()" id="form-delete" style="display:inline-block;"
                                  action="{{ url('admin/envelopes/bulk-delete/' . $id) }}" method="POST">
                                @csrf
                                <button class="btn btn-danger" id="bulkDelete" disabled>Bulk Delete</button>
                            </form> 

                            <a href="{{ route('user.envelopes.index') }}" class="btn btn-info btn-md">Back to Envelope</a>
                            <a href="{{ route('user.dashboard') }}" class="btn btn-success btn-md">Back to Home</a>

                        </div>
                        <div class="pull-left p-t-20">
                            <a class="btn btn-lg btn-primary" style="font-size:17px;" type="button" href="{{route('user.envelope.add.reciepts', ['Envelope' => $envelope->id]) }}" id="sessionSave" name="add"><i class="fa fa-plus-circle"></i>&nbsp;&nbsp;Add Receipts</a>
                        </div>

                    </div>
                </div>
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
        $("#selectAllReceipts").on('click', function() {
                array = [];
                var table = $("#previewTable");
                var boxes = $('input:checkbox', table);
                $.each($('input:checkbox', table), function() {
                    $(this).parent().addClass('checked');
                    $(this).prop('checked', 'checked');
                    var id = $(this).val();
                    array.push(id);
                });
                var urlpost = "{{ route('user.bulkSession.envelope') }}";

                $.ajax({
                    type: 'POST',
                    url: urlpost,
                    data: {
                        fieldOne: array
                    },
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(data) {

                    }

                });

                $('#deselectAllEnvelopes').prop('disabled', false);
            $('#bulkDelete').prop('disabled', false);

                for ($i = 0; $i < array.length; $i++) {
                    $('#form-session').append(
                        $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', 'envelope_id[]')
                        .val(array[$i])
                    );
                }

            });

            $("#deselectAllReceipts").on('click', function() {
                var table = $("#previewTable");
                var boxes = $('input:checkbox', table);
                $.each($('input:checkbox', table), function() {
                    $(this).parent().removeClass('checked');
                    $(this).prop('checked', false);
                    var id = $(this).val();
                    array.splice(array.indexOf(id), 1);
                });
                var urlpost = "{{ route('user.clearSession.envelope') }}";

                $.ajax({
                    type: 'POST',
                    url: urlpost,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(data) {

                    }
                });
                $('#bulkDelete').prop('disabled', true);
            $('.save-confirmation').prop('disabled', true);
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
    });
</script>



@endsection

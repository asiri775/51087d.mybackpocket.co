@extends('admin.layouts.newMaster')
@section('title', 'All Banks')
@section('content')


    <div class="page-content-wrapper ">

        <div class="content noPadTop">

            <div class=" container-fluid   container-fixed-lg">

                @if (Session::has('success'))
                    <div class="alert alert-success">{{ Session::get('success') }}</div>
                @elseif(Session::has('error'))
                    <div class="alert alert-danger">{{ Session::get('error') }}</div>
                @endif

                <div class="card card-default">
                    <div class="card-header separator">
                        <div class="card-title flex-card-title">
                            <h5><strong>All Banks</strong></h5>
                            <a class="newRecord btn mr-0 ml-auto text-capitalize btn-complete" href="javascript:;">Add New</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="wire-grid-box">
                            @livewire('bank-list-component')
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>


    <div class="modal fade" id="newRecordModal" tabindex="-1" role="dialog" aria-labelledby="main_cat_modelLabel">
        <div class="modal-dialog" role="dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="main_cat_modelLabel"><strong>Create</strong></h4>
                </div>
                @include('banks.form', [
                    'bank' => $model,
                ])
            </div>
        </div>
    </div>

    <div class="modal fade" id="oldRecordModal" tabindex="-1" role="dialog" aria-labelledby="main_cat_modelLabel">
        <div class="modal-dialog" role="dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="main_cat_modelLabel"><strong>Update</strong></h4>
                </div>
                <div id="editFormData">
                    @include('banks.form', [
                    'bank' => $editModel,
                ])
                </div>
            </div>
        </div>
    </div>


@endsection



@section('page-js')

    <script>

        $(document).on("click", ".newRecord", function () {
            $("#newRecordModal .form-control").removeClass('is-invalid').val('');
            $("#newRecordModal").modal("show");
        });

        $(document).on("click", ".editRecord", function () {
            let obj = $(this);
            let url = obj.attr("data-href");
            $.get(url, function (r) {
                $("#oldRecordModal #editFormData").html(r);
                $("#oldRecordModal").modal("show");
            });
        });

    </script>

    @if (Session::has('popup'))
        <script>
            $("#{{Session::get('popup')}}").modal("show");
        </script>
    @endif

@endsection

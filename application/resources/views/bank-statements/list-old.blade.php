@extends('admin.layouts.newMaster')
@section('title', 'All Bank Statements')
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
                            <h5><strong>Bank Statements</strong></h5>
                            <div class="mr-0 ml-auto">
                                <a class="btn mr-0 ml-auto text-capitalize btn-primary"
                                   href="{{route('bankStatements.allTransactions')}}">All Transactions</a>
                                <a class="btn mr-0 ml-auto text-capitalize btn-success newRecord"
                                   href="javascript:;">Add New</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="wire-grid-box">
                            @livewire('bank-account-statements-component')
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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="main_cat_modelLabel"><strong>Add New Statement</strong></h4>
                </div>

                <form enctype="multipart/form-data" action="{{ route('bankStatements.addNewStatement') }}"
                      id="main_cat" method="POST" class="mt-3">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="row equalPad">

                            <div class="col-md-12 col-12">
                                <div class="form-group text-left @error('bank_account_id') is-invalid @enderror">
                                    <label for="bank_account_id">{{ __('Bank Account') }}</label>
                                    <select name="bank_account_id" id="bank_account_id"
                                            class="form-control @error('bank_account_id') is-invalid @enderror">
                                        <option value="">Select Bank Account</option>
                                        @foreach (\App\Models\BankAccount::orderBy('name', 'ASC')->get() as $bankAccount)
                                            <option
                                                {{  $bankAccount->id === old('bank_account_id') ? 'selected="selected"': ''}} value="{{$bankAccount->id}}">{{$bankAccount->displayName()}}</option>
                                        @endforeach
                                    </select>
                                    @error('bank_account_id')
                                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12 col-12">
                                <div class="form-group text-left @error('name') is-invalid @enderror">
                                    <label for="name">{{ __('Statement Name') }}</label>
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                           name="name" placeholder="{{ __('Enter Statement Name') }}"
                                           value="{{old('name')}}">
                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12 col-12">
                                <div class="form-group">
                                    <label for="statement">Statement File</label>
                                    <input type="file" class="form-control" name="statement" id="statement"
                                           required>
                                    @error('statement')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <div class="modal fade" id="oldRecordModal" tabindex="-1" role="dialog" aria-labelledby="main_cat_modelLabel">
        <div class="modal-dialog" role="dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="main_cat_modelLabel"><strong>Update Statement</strong></h4>
                </div>
                <div id="editFormData">
                    <form enctype="multipart/form-data" action="{{ route('bankStatements.editStatement') }}"
                          id="main_cat" method="POST" class="mt-3">
                        {{ csrf_field() }}
                        <div class="modal-body">
                            <div class="row equalPad">

                                <div class="col-md-12 col-12">
                                    <div class="form-group text-left @error('bank_account_id') is-invalid @enderror">
                                        <label for="bank_account_id">{{ __('Bank Account') }}</label>
                                        <select name="bank_account_id" id="bank_account_id"
                                                class="form-control @error('bank_account_id') is-invalid @enderror">
                                            <option value="">Select Bank Account</option>
                                            @foreach (\App\Models\BankAccount::orderBy('name', 'ASC')->get() as $bankAccount)
                                                <option
                                                    {{  $bankAccount->id === old('bank_account_id') ? 'selected="selected"': ''}} value="{{$bankAccount->id}}">{{$bankAccount->name}}</option>
                                            @endforeach
                                        </select>
                                        @error('bank_account_id')
                                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12 col-12">
                                    <div class="form-group text-left @error('name') is-invalid @enderror">
                                        <label for="name">{{ __('Statement Name') }}</label>
                                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                               name="name" placeholder="{{ __('Enter Statement Name') }}"
                                               value="{{old('name')}}">
                                        @error('name')
                                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                                        @enderror
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>

                    </form>
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
            let bankAccountId = obj.attr("data-bank_account_id");
            let name = obj.attr("data-name");
            let url = obj.attr("data-href");
            $("#oldRecordModal").find("form").attr("action", url);
            $("#oldRecordModal").find("#bank_account_id").val(bankAccountId).trigger("change");
            $("#oldRecordModal").find("#name").val(name).trigger("change");
            $("#oldRecordModal").modal("show");
        });

    </script>

    @if (Session::has('popup'))
        <script>
            $("#{{Session::get('popup')}}").modal("show");
        </script>
    @endif

@endsection

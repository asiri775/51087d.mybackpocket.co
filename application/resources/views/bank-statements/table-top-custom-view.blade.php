<div class="card-header mb-4 p-0">
    <div class="card-title flex-card-title">
        <h5><strong>Transactions</strong></h5>
        <a id="bulkUpdateRecordsLwx" class="btn mr-0 ml-auto text-capitalize btn-complete" data-target="#bulkUpdateModal" data-toggle="modal" href="javascript:;">Bulk Update</a>
    </div>
</div>

<div class="modal fade" id="bulkUpdateModal" tabindex="-1" role="dialog" aria-labelledby="main_cat_modelLabel">
    <div class="modal-dialog" role="dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="main_cat_modelLabel"><strong>Bulk Update</strong></h4>
            </div>
            <div id="editFormData">
                <form enctype="multipart/form-data" action="{{ route('bankStatements.bulkUpdateStatement') }}"
                      id="main_cat" method="POST" class="mt-3">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="row equalPad">

                            <input type="hidden" name="keys" value="{{json_encode($selectionKeys)}}">

                            <div class="col-md-12 col-12">
                                <div class="form-group text-left @error('transaction_date') is-invalid @enderror">
                                    <label for="transaction_date">{{ __('Transaction Date') }}</label>
                                    <input id="transaction_date" type="date" class="form-control @error('transaction_date') is-invalid @enderror"
                                           name="transaction_date" placeholder="{{ __('Select Transaction Date') }}"
                                           value="{{old('transaction_date')}}">
                                    @error('transaction_date')
                                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12 col-12">
                                <div class="form-group text-left @error('category_id') is-invalid @enderror">
                                    <label for="category_id">{{ __('Category') }}</label>
                                    <select name="category_id" id="category_id"
                                            class="form-control @error('category_id') is-invalid @enderror">
                                        <option value="">Select Category</option>
                                        @foreach (\App\Models\Category::where('role', 'main')->orderBy('name', 'ASC')->get() as $category)
                                            <option value="{{$category->id}}">{{$category->name}}</option>
                                            @foreach (\App\Models\Category::where('role', 'sub')->where('mainid', $category->id)->orderBy('name', 'ASC')->get() as $subCategory)
                                                <option value="{{$subCategory->id}}">----{{$subCategory->name}}</option>
                                            @endforeach
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12 col-12">
                                <div class="form-group text-left @error('status') is-invalid @enderror">
                                    <label for="status">{{ __('Status') }}</label>
                                    <select name="status" id="status"
                                            class="form-control @error('status') is-invalid @enderror">
                                        <option value="">Select Status</option>
                                        <option value="{{\App\Constants\StatementConstants::TRANSACTION_PENDING}}">Pending</option>
                                        <option value="{{\App\Constants\StatementConstants::TRANSACTION_CONFIRMED}}">Completed</option>
                                    </select>
                                    @error('status')
                                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12 col-12">
                                <div class="form-group text-left @error('vendor_id') is-invalid @enderror">
                                    <label for="vendor_id">{{ __('Vendor') }}</label>
                                    <select name="vendor_id" id="vendor_id"
                                            class="form-control @error('vendor_id') is-invalid @enderror">
                                        <option value="">Select Vendor</option>
                                        @foreach (\App\Models\Vendor::orderBy('name', 'ASC')->get() as $vendor)
                                            <option value="{{$vendor->id}}">{{$vendor->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('vendor_id')
                                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12 col-12">
                                <div class="form-group text-left @error('envelope_id') is-invalid @enderror">
                                    <label for="envelope_id">{{ __('Envelope') }}</label>
                                    <select name="envelope_id" id="envelope_id"
                                            class="form-control @error('envelope_id') is-invalid @enderror">
                                        <option value="">Select Envelope</option>
                                        @foreach (\App\Models\Envelope::orderBy('name', 'ASC')->get() as $envelope)
                                            <option value="{{$envelope->id}}">{{$envelope->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('envelope_id')
                                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                                    @enderror
                                </div>
                            </div>


                            <div class="col-md-12 col-12">
                                <div class="form-group text-left @error('budget_id') is-invalid @enderror">
                                    <label for="budget_id">{{ __('Budget') }}</label>
                                    <select name="budget_id" id="budget_id"
                                            class="form-control @error('budget_id') is-invalid @enderror">
                                        <option value="">Select Budget</option>
                                        @foreach (\App\Models\Budget::orderBy('name', 'ASC')->get() as $budget)
                                            <option value="{{$budget->id}}">{{$budget->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('budget_id')
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

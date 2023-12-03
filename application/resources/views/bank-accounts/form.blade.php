<form
    action="{{ $bankAccount->id ==null ? route('bank-accounts.store') : route('bank-accounts.update', $bankAccount) }}"
    method="POST">
    @csrf

    @if( $bankAccount->id != null )
        @method('PUT')
    @endif

    <div class="modal-body mt-3">


        <div class="row equalPad">

            <div class="col-md-12 col-12">
                <div class="form-group text-left @error('bank_id') is-invalid @enderror">
                    <label for="name">{{ __('Bank') }}</label>
                    <select name="bank_id" id="bank_id"
                            class="form-control @error('bank_id') is-invalid @enderror">
                        <option value="">Select Bank</option>
                        @foreach (\App\Models\Bank::orderBy('name', 'ASC')->get() as $bank)
                            <option
                                {{  $bank->id === old('bank_id', $bank->id) ? 'selected="selected"': ''}} value="{{$bank->id}}">{{$bank->name}}</option>
                        @endforeach
                    </select>
                    @error('bank_id')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="col-md-12 col-12">
                <div class="form-group text-left @error('name') is-invalid @enderror">
                    <label for="name">{{ __('Name') }}</label>
                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                           name="name" placeholder="{{ __('Enter Name') }}"
                           value="{{old('name', $bankAccount->name)}}">
                    @error('name')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="col-md-12 col-12">
                <div class="form-group text-left @error('account_number') is-invalid @enderror">
                    <label for="account_number">{{ __('Account No.') }}</label>
                    <input id="account_number" type="text"
                           class="form-control @error('account_number') is-invalid @enderror"
                           name="account_number" placeholder="{{ __('Enter Account No.') }}"
                           value="{{old('account_number', $bankAccount->account_number)}}">
                    @error('account_number')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="col-md-12 col-12">
                <div class="form-group text-left @error('alias') is-invalid @enderror">
                    <label for="alias">{{ __('Alias') }}</label>
                    <input id="alias" type="text" class="form-control @error('alias') is-invalid @enderror"
                           name="alias" placeholder="{{ __('Enter Alias') }}"
                           value="{{old('alias', $bankAccount->alias)}}">
                    @error('alias')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="col-md-12 col-12">
                <div class="form-group text-left @error('transit_number') is-invalid @enderror">
                    <label for="transit_number">{{ __('Transit No.') }}</label>
                    <input id="transit_number" type="text"
                           class="form-control @error('transit_number') is-invalid @enderror"
                           name="transit_number" placeholder="{{ __('Enter Transit No.') }}"
                           value="{{old('transit_number', $bankAccount->transit_number)}}">
                    @error('transit_number')
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

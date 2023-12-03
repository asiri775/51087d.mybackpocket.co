<form action="{{ $bank->id ==null ? route('banks.store') : route('banks.update', $bank) }}"
      method="POST">
    @csrf

    @if( $bank->id != null )
        @method('PUT')
    @endif

    <div class="modal-body mt-3">


        <div class="row equalPad">

            <div class="col-md-12 col-12">
                <div class="form-group text-left @error('name') is-invalid @enderror">
                    <label for="name">{{ __('Name') }}</label>
                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                           name="name" placeholder="{{ __('Enter Name') }}"
                           value="{{old('name', $bank->name)}}">
                    @error('name')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="col-md-12 col-12">
                <div class="form-group text-left @error('code') is-invalid @enderror">
                    <label for="code">{{ __('Bank Code') }}</label>
                    <input id="code" type="text" class="form-control @error('code') is-invalid @enderror"
                           name="code" placeholder="{{ __('Enter Bank Code') }}"
                           value="{{old('code', $bank->code)}}">
                    @error('code')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="col-md-12 col-12">
                <div class="form-group text-left @error('swift_code') is-invalid @enderror">
                    <label for="swift_code">{{ __('Swift Code') }}</label>
                    <input id="swift_code" type="text" class="form-control @error('swift_code') is-invalid @enderror"
                           name="swift_code" placeholder="{{ __('Enter Swift Code') }}"
                           value="{{old('swift_code', $bank->swift_code)}}">
                    @error('swift_code')
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

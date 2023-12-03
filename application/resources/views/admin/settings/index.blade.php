@extends('admin.layouts.newMaster')

@section('title', 'Settings')

@section('page-css')
    <style>



    </style>
@endsection
@section('content')
    <div class=" container-fluid   container-fixed-lg">
        @if (Session::has('success'))
            <div class="alert alert-success">{{ Session::get('success') }}</div>
        @elseif(Session::has('error'))
            <div class="alert alert-danger">{{ Session::get('error') }}</div>
        @endif
        <!-- START card -->
        <div class="card card-default  p-l-20  p-r-20">
            <div class="card-header separator">
                <div class="card-title">
                    <h5><strong>Settings</strong></h5>
                </div>

            </div>
            <div class="p-b-20">
                <form action="{{ route('admin.settings.edit') }}" method="post" class="form-control">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-1"></div>
                            <label for="phone" class="col-md-2">PHONE</label>
                            <input type="text" id="phone" name="phone" class="form-control col-md-6" value="@if(!empty($setting->phone)){{ $setting->phone }}@endif">
                        </div>
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-6">
                                @if ($errors->has('phone'))
                                    <span class="help-block" style="color: red">{!! $errors->first('phone') !!}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-1"></div>
                            <label for="address" class="col-md-2">ADDRESS</label>
                            <input type="text" id="address" name="address" class="form-control col-md-6" value="@if(!empty($setting->address)){{ $setting->address }}@endif">
                        </div>
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-6">
                                @if ($errors->has('address'))
                                    <span class="help-block" style="color: red">{!! $errors->first('address') !!}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-1"></div>
                            <label for="IMAP_HOST" class="col-md-2">IMAP HOST</label>
                            <input type="text" id="IMAP_HOST" name="IMAP_HOST" class="form-control col-md-6" value="@if(!empty($setting->IMAP_HOST)){{ $setting->IMAP_HOST }}@endif">
                        </div>
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-6">
                                @if ($errors->has('IMAP_HOST'))
                                    <span class="help-block" style="color: red">{!! $errors->first('IMAP_HOST') !!}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-1"></div>
                            <label for="IMAP_PORT" class="col-md-2">IMAP PORT</label>
                            <input type="text" id="IMAP_PORT" name="IMAP_PORT" class="form-control col-md-6" value="@if(!empty($setting->IMAP_PORT)){{ $setting->IMAP_PORT }}@endif">
                        </div>
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-6">
                                @if ($errors->has('IMAP_PORT'))
                                    <span class="help-block" style="color: red">{!! $errors->first('IMAP_PORT') !!}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-1"></div>
                            <label for="IMAP_ENCRYPTION" class="col-md-2">IMAP ENCRYPTION</label>
                            <input type="text" id="IMAP_ENCRYPTION" name="IMAP_ENCRYPTION" class="form-control col-md-6" value="@if(!empty($setting->IMAP_ENCRYPTION)){{ $setting->IMAP_ENCRYPTION }}@endif">
                        </div>
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-6">
                                @if ($errors->has('IMAP_ENCRYPTION'))
                                    <span class="help-block" style="color: red">{!! $errors->first('IMAP_ENCRYPTION') !!}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-1"></div>
                            <label for="IMAP_VALIDATE_CERT" class="col-md-2">IMAP VALIDADE CERT</label>
                            <input type="text" id="IMAP_VALIDATE_CERT" name="IMAP_VALIDATE_CERT" class="form-control col-md-6" value="@if(!empty($setting->IMAP_VALIDATE_CERT)){{ ($setting->IMAP_VALIDATE_CERT == 1) ?  "TRUE" : "FALSE"  }}@endif">
                        </div>
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-6">
                                @if ($errors->has('IMAP_VALIDATE_CERT'))
                                    <span class="help-block" style="color: red">{!! $errors->first('IMAP_VALIDATE_CERT') !!}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-1"></div>
                            <label for="IMAP_USERNAME" class="col-md-2">IMAP USER NAME</label>
                            <input type="text" id="IMAP_USERNAME" name="IMAP_USERNAME" class="form-control col-md-6" value="@if(!empty($setting->IMAP_USERNAME)){{ $setting->IMAP_USERNAME }}@endif">
                        </div>
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-6">
                                @if ($errors->has('IMAP_USERNAME'))
                                    <span class="help-block" style="color: red">{!! $errors->first('IMAP_USERNAME') !!}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-1"></div>
                            <label for="IMAP_PASSWORD" class="col-md-2">IMAP PASSWORD</label>
                            <input type="text" id="IMAP_PASSWORD" name="IMAP_PASSWORD" class="form-control col-md-6" value="@if(!empty($setting->IMAP_PASSWORD)){{ $setting->IMAP_PASSWORD }}@endif">
                        </div>
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-6">
                                @if ($errors->has('IMAP_PASSWORD'))
                                    <span class="help-block" style="color: red">{!! $errors->first('IMAP_PASSWORD') !!}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-1"></div>
                            <label for="IMAP_DEFAULT_ACCOUNT" class="col-md-2">IMAP DEFAULT ACCOUNT</label>
                            <input type="text" id="IMAP_DEFAULT_ACCOUNT" name="IMAP_DEFAULT_ACCOUNT" class="form-control col-md-6" value="@if(!empty($setting->IMAP_DEFAULT_ACCOUNT)){{ $setting->IMAP_DEFAULT_ACCOUNT }}@endif">
                        </div>
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-6">
                                @if ($errors->has('IMAP_DEFAULT_ACCOUNT'))
                                    <span class="help-block" style="color: red">{!! $errors->first('IMAP_DEFAULT_ACCOUNT') !!}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-1"></div>
                            <label for="IMAP_PROTOCOL" class="col-md-2">IMAP PROTOCOL</label>
                            <input type="text" id="IMAP_PROTOCOL" name="IMAP_PROTOCOL" class="form-control col-md-6" value="@if(!empty($setting->IMAP_PROTOCOL)){{ $setting->IMAP_PROTOCOL }}@endif">
                        </div>
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-6">
                                @if ($errors->has('IMAP_PROTOCOL'))
                                    <span class="help-block" style="color: red">{!! $errors->first('IMAP_PROTOCOL') !!}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-1"></div>
                            <label for="MAIL_DRIVER" class="col-md-2">MAIL DRIVER</label>
                            <input type="text" id="MAIL_DRIVER" name="MAIL_DRIVER" class="form-control col-md-6" value="@if(!empty($setting->MAIL_DRIVER)){{ $setting->MAIL_DRIVER }}@endif">
                        </div>
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-6">
                                @if ($errors->has('MAIL_DRIVER'))
                                    <span class="help-block" style="color: red">{!! $errors->first('MAIL_DRIVER') !!}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-1"></div>
                            <label for="MAIL_HOST" class="col-md-2">MAIL HOST</label>
                            <input type="text" id="MAIL_HOST" name="MAIL_HOST" class="form-control col-md-6" value="@if(!empty($setting->MAIL_HOST)){{ $setting->MAIL_HOST }}@endif">
                        </div>
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-6">
                                @if ($errors->has('MAIL_HOST'))
                                    <span class="help-block" style="color: red">{!! $errors->first('MAIL_HOST') !!}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-1"></div>
                            <label for="MAIL_PORT" class="col-md-2">MAIL PORT</label>
                            <input type="text" id="MAIL_PORT" name="MAIL_PORT" class="form-control col-md-6" value="@if(!empty($setting->MAIL_PORT)){{ $setting->MAIL_PORT }}@endif">
                        </div>
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-6">
                                @if ($errors->has('MAIL_PORT'))
                                    <span class="help-block" style="color: red">{!! $errors->first('MAIL_PORT') !!}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-1"></div>
                            <label for="MAIL_USERNAME" class="col-md-2">MAIL USER NAME</label>
                            <input type="text" id="MAIL_USERNAME" name="MAIL_USERNAME" class="form-control col-md-6" value="@if(!empty($setting->MAIL_USERNAME)){{ $setting->MAIL_USERNAME }}@endif">
                        </div>
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-6">
                                @if ($errors->has('MAIL_USERNAME'))
                                    <span class="help-block" style="color: red">{!! $errors->first('MAIL_USERNAME') !!}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-1"></div>
                            <label for="MAIL_PASSWORD" class="col-md-2">MAIL PASSWORD</label>
                            <input type="text" id="MAIL_PASSWORD" name="MAIL_PASSWORD" class="form-control col-md-6" value="@if(!empty($setting->MAIL_PASSWORD)){{ $setting->MAIL_PASSWORD }}@endif">
                        </div>
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-6">
                                @if ($errors->has('MAIL_PASSWORD'))
                                    <span class="help-block" style="color: red">{!! $errors->first('MAIL_PASSWORD') !!}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-1"></div>
                            <label for="MAIL_ENCRYPTION" class="col-md-2">MAIL ENCRYPTION</label>
                            <input type="text" id="MAIL_ENCRYPTION" name="MAIL_ENCRYPTION" class="form-control col-md-6" value="@if(!empty($setting->MAIL_ENCRYPTION)){{ $setting->MAIL_ENCRYPTION }}@endif">
                        </div>
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-6">
                                @if ($errors->has('MAIL_ENCRYPTION'))
                                    <span class="help-block" style="color: red">{!! $errors->first('MAIL_ENCRYPTION') !!}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class=" col-md-3"></div>
                        <button type="submit" class="btn btn-complete col-md-6" style="width: 200px;">UPDATE SETTINGS</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- END card -->
    </div>
    <!-- END CONTAINER FLUID -->

@endsection
@section('page-js')
    <script>


    </script>

@endsection

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
                <div class="pull-right">
                    <a href="{{ route('admin.users') }}" style="vertical-align: middle;"
                        class="btn btn-danger btn-md" id="back">Back</a>
                </div>
            </div>
            <div class="p-b-20">
                <form action="{{ route('admin.users.store') }}" method="post" class="form-control">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-1"></div>
                            <label for="host" class="col-md-2">Host</label>
                            <input type="text" id="host" name="host" class="form-control col-md-6" value="{{env("IMAP_HOST") }}">
                        </div>
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-6">
                                @if ($errors->has('host'))
                                    <span class="help-block" style="color: red">{!! $errors->first('host') !!}</span>
                                @endif
                            </div>
                        </div>

                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class=" col-md-1"></div>
                            <label for="port" class="col-md-2">Port</label>
                            <input type="port" id="port" name="port" class="form-control col-md-6" value="{{ env("IMAP_PORT")}}">
                        </div>
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-6">
                                @if ($errors->has('port'))
                                    <span class="help-block" style="color: red">{!! $errors->first('port') !!}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class=" col-md-1"></div>
                            <label for="encryption" class="col-md-2">Encryption</label>
                            <input type="encryption" id="encryption" name="encryption" class="form-control col-md-6" value="{{ env("IMAP_ENCRYPTION")}}">
                        </div>
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-6">
                                @if ($errors->has('encryption'))
                                    <span class="help-block" style="color: red">{!! $errors->first('encryption') !!}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class=" col-md-1"></div>
                            <label for="validate_cert" class="col-md-2">Validate cart</label>
                            <input type="validate_cert" id="validate_cert" name="validate_cert" class="form-control col-md-6" value="{{ env("IMAP_VALIDATE_CERT")}}">
                        </div>
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-6">
                                @if ($errors->has('validate_cert'))
                                    <span class="help-block" style="color: red">{!! $errors->first('validate_cert') !!}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class=" col-md-1"></div>
                            <label for="user_name" class="col-md-2">User Name</label>
                            <input type="user_name" id="user_name" name="user_name" class="form-control col-md-6" value="{{ env("IMAP_USERNAME")}}">
                        </div>
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-6">
                                @if ($errors->has('user_name'))
                                    <span class="help-block" style="color: red">{!! $errors->first('user_name') !!}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class=" col-md-1"></div>
                            <label for=" " class="col-md-2">Password</label>
                            <input type="user_name" id="user_name" name="user_name" class="form-control col-md-6" value="{{ env("IMAP_USERNAME")}}">
                        </div>
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-6">
                                @if ($errors->has('user_name'))
                                    <span class="help-block" style="color: red">{!! $errors->first('user_name') !!}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class=" col-md-3"></div>
                        <button type="submit" class="btn btn-complete col-md-6" style="width: 200px;">Add User</button>
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

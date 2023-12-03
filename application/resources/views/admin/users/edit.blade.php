@extends('admin.layouts.newMaster')

@section('title', 'User List')

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
                    <h5><strong>Add Users</strong></h5>
                </div>
                <div class="pull-right">
                    <a href="{{ route('admin.users') }}" style="vertical-align: middle;"
                        class="btn btn-danger btn-md" id="back">Back</a>
                </div>
            </div>
            <div class="p-b-20">
                <form action="{{ route('admin.users.update', $user->id) }}" method="post" class="form-control">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-1"></div>
                            <label for="name" class="col-md-2">Name</label>
                            <input type="text" id="name" name="name" class="form-control col-md-6" value="{{ $user->name }}">
                        </div>
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-6">
                                @if ($errors->has('name'))
                                    <span class="help-block" style="color: red">{!! $errors->first('name') !!}</span>
                                @endif
                            </div>
                        </div>

                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class=" col-md-1"></div>
                            <label for="email" class="col-md-2">Email</label>
                            <input type="email" id="email" name="email" class="form-control col-md-6" value="{{ $user->email }}">
                        </div>
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-6">
                                @if ($errors->has('email'))
                                    <span class="help-block" style="color: red">{!! $errors->first('email') !!}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class=" col-md-1"></div>
                            <label for="role" class="col-md-2">Role</label>
                            <select name="role" id="role" class="form-control col-md-6">
                                <option value="">Select a Role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" @if($user->role_id == $role->id) selected="selected"@endif>{{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-6">
                                @if ($errors->has('role'))
                                    <span class="help-block" style="color: red">{!! $errors->first('role') !!}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class=" col-md-1"></div>
                            <label for="password" class="col-md-2">Password</label>
                            <input type="password" id="password" name="password" class="form-control col-md-6">
                        </div>
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-6">
                                @if ($errors->has('password'))
                                    <span class="help-block" style="color: red">{!! $errors->first('password') !!}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class=" col-md-1"></div>
                            <label for="repassword" class="col-md-2">Re-Enter Password</label>
                            <input type="password" id="password" name="password_confirmation" class="form-control col-md-6">
                        </div>
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-6">
                                @if ($errors->has('repassword'))
                                    <span class="help-block" style="color: red">{!! $errors->first('repassword') !!}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class=" col-md-3"></div>
                        <button type="submit" class="btn btn-complete col-md-6" style="width: 200px;">Edit User</button>
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

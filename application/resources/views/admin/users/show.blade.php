@extends('admin.layouts.newMaster')

@section('title', 'User List')

@section('page-css')
    <style>

.table-head{
    font-weight: 700;
}

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
                    <h5><strong>User# {{ $user->name }}</strong></h5>
                </div>
                <div class="pull-right">
                    <a href="{{ route('admin.users.edit', ['user' => $user->id]) }}" style="vertical-align: middle;"
                    class="btn btn-complete btn-md" id="back">Edit</a>
                    <a href="{{ route('admin.users') }}" style="vertical-align: middle;"
                        class="btn btn-danger btn-md" id="back">Back</a>
                </div>
            </div>
            <div class="p-b-20">
                    <table class="table">
                        <tr>
                            <td class="text-center table-head" >
                                Name
                            </td>
                            <td>
                                {{ $user->name }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center table-head" >
                                Email
                            </td>
                            <td>
                                {{ $user->email }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center table-head" >
                                Role
                            </td>
                            <td>
                                {{ $user->getRoleName( $user->role_id) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center table-head" >
                                Number of Transactions
                            </td>
                            <td>
                                {{ $user->NumberOfTransacations( $user->id) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center table-head" >
                                Number of Envelopes
                            </td>
                            <td>
                                {{ $user->NumberOfEnvelopes( $user->id) }}
                            </td>
                        </tr>
                    </table>


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

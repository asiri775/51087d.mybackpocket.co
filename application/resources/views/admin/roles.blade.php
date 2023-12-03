@extends('admin.layouts.masterToManageNoDatatable')
@section('title', 'Manage Roles')

@section('page-css')

    <style>
        .dataTables_filter {
            display: none;
        }

    </style>
@endsection

@section('content')
    <div class="page-content-wrapper ">
        <!-- START PAGE CONTENT -->
        <div class="content ">
            <!-- START JUMBOTRON -->
            @if (Session::has('success'))
                <div class="alert alert-success">{{ Session::get('success') }}</div>
            @elseif(Session::has('error'))
                <div class="alert alert-danger">{{ Session::get('error') }}</div>
            @endif
            <!-- END JUMBOTRON -->
            <!-- START CONTAINER FLUID -->
            <div class=" container-fluid   container-fixed-lg">
                <!-- START card -->
                <div class="card card-default">
                    <div class="card-header separator">
                        <div class="card-title">
                            <h5><strong>Manage Roles</strong></h5>
                        </div>
                    </div>
                    <div class="card-body p-t-20">
                        <form action="{{ route('admin.roles.store') }}" id="add_roles" method="POST">
                            {{ csrf_field() }}
                            <div class="row justify-content-left">
                                <div class="col-md-7">
                                    <div class="form-group" style="float:none;">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label>New Role Name</label>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="name" id="name" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <button type="submit" class="btn btn-complete">Add Role</button>
                                </div>
                            </div>
                            </form>
                            <hr>
                            <div class="widget-11-2-table p-t-20">
                                <table class="table table-hover table-condensed table-responsive" id="tableRole">
                                    <thead>
                                        <tr>
                                            <th class="v-align-middle" style="width: 5%; text-align:center;">ID</th>
                                            <th class="v-align-middle" style="width: 30%; text-align:center;">Role Name</th>
                                            {{-- <th class="v-align-middle" style="width: 20%; text-align:center;">Count</th> --}}
                                            <th class="v-align-middle" style="width: 40%; text-align:center;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                     @foreach($roles as $role)
                                            <tr>
                                                <td class="v-align-middle" style="text-align:center;">{{ $role->id }}</td>
                                                <td class="v-align-middle" style="text-align:center;">{{ $role->name }}</td>
                                                {{-- <td class="v-align-middle" style="text-align:center;"></td> --}}
                                                <td class="v-align-middle" style="text-align:center;">
                                                    <div class="btn-group">
                                                        <button href="#!"
                                                                class="btn btn-complete subCat" data-role_id="{{ $role->id }}" data-role_name="{{ $role->name }}"  id="edit" title="Edit" data-target="#edit_model" data-toggle="modal"><i class="fa fa-edit"></i>
                                                            </button>
                                                            {{-- <a href="{{ url('admin/roles/delete/' . $role->id) }}"
                                                                class="btn btn-danger"
                                                                onclick="return confirm('Are you sure you want to remove Category {{ $role->name }} ?')"
                                                                data-toggle="tooltip" data-placement="bottom" title="Delete"><i
                                                                    class="fa fa-trash-o"></i></a> --}}
                                                    </div>
                                                </td>
                                            </tr>
                                       @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- END card -->
                </div>
                <!-- END CONTAINER FLUID -->
            </div>

            <!-- END COPYRIGHT -->
        </div>
        <!-- END PAGE CONTENT WRAPPER -->

        <div class="modal fade" id="edit_model" tabindex="-1" role="dialog" aria-labelledby="edit_modelLabel">
            <div class="modal-dialog" role="dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="edit_modellLabel"><strong>Edit Role</strong></h4>
                    </div>
                    <form action="{{ route('admin.roles.update') }}"  method="POST">
                        {{ csrf_field() }}
                    <div class="modal-body">
                        <div>
                            <label>Edit Role Name</label>
                            <input type="text" class="form-control" name="edit_name" id="edit_role_name" required>
                        </div>
                        <input type="hidden" name="id" id="id" value="" />

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>

                    </div>
                </form>
                </div>
            </div>
        </div>

        <!-- END PAGE CONTAINER -->
@endsection
@section('page-js')
        <!-- BEGIN VENDOR JS -->
        <script type="text/javascript">
            $(function () {
                $("#edit").click(function () {
                    var id_value = $(this).data('role_id');
                    var edit_name = $(this).data('role_name');
                    $(".modal-body #id").val(id_value);
                    $(".modal-body #edit_role_name").val(edit_name);
                })
            });

            $(document).ready(function(e) {
        //datatable
        var table = $('#tableRole');
        table.dataTable({
            "sDom": "<'top'f<'clear'>><t><'row'<p i>>",
            "destroy": true,
            "scrollCollapse": true,
            "oLanguage": {
                "sLengthMenu": "_MENU_ ",
                "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
            },
            "iDisplayLength": 5
        })
    });
        </script>
@endsection

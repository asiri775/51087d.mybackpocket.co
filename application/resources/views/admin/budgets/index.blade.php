@extends('admin.layouts.masterToBudgets')
@section('title', 'Manage Budgets')

@section('page-css')
    <style>
        .dataTables_filter {
            display: none;
        }

      

    </style>
@endsection


@section('content')
    <?php
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Input;
    ?>

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
                            <h5><strong>Manage Budgets</strong></h5>
                        </div>
                    </div>
                    <div class="card-body p-t-20">
                        <form action="{{ route('create-budget') }}" id="user_budget" method="POST">
                            {{ csrf_field() }}
                            <div class="row justify-content-left">
                                <div class="col-md-10">
                                    <div class="form-group" style="float:none;">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label>New Budget Name</label>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="text" class="form-control" name="name" id="name" required>
                                            </div>
                                            <div class="col-md-1"></div>
                                            <div class="col-md-2">
                                                <label>Budget Category</label>
                                            </div>
                                            <div class="col-md-3">
                                                <select name="category" id="category" class="form-control" required>
                                                    <option value="">Select a Category</option>
                                                   @foreach ($categories as $category)
                                                         @if($category->role=='main')
                                                           <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                         @else
                                                           <option value="{{ $category->id }}">&nbsp;&nbsp;&nbsp;&nbsp;{{ $category->name }}</option>
                                                         @endif
                                                      
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label>Target Budget Value $</label>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="number" class="form-control" name="value" id="value" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-complete">Add Budget</button>
                                </div>
                            </div>
                        </form>

                        <hr>
                        <div class="widget-11-2-table p-t-20">
                            <table class="table table-hover table-condensed table-responsive" id="tableBudget">
                                <thead>
                                    <tr>
                                        <th style="width: 5%; text-align:center;">ID</th>
                                        <th style="width: 15%; text-align:center;">Name</th>
                                        <th style="width: 20%; text-align:center;">Category</th>
                                        <th style="width: 20%; text-align:center;">User</th>
                                        <th style="width: 20%; text-align:center;">Target Value</th>
                                        <th style="width: 20%; text-align:center;">Current Value</th>
                                        <th style="width: 20%; text-align:center;">Variance </th>
                                        <th style="width: 30%; text-align:center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($budgets as $budget)
                                        <tr>
                                            <td class="v-align-middle" style="text-align:center;">{{ $budget->id }}</td>
                                            <td class="v-align-middle" style="text-align:center;"><a href="{{ url('admin/budgets/preview') . '/' . $budget->id  }}" >{{ $budget->name }}</a></td>
                                            <?php
                                            $categoryName = $budget->getCategoryName($budget->category_id);
                                            $categoryName = isset($categoryName['name'])
                                            ? $categoryName['name']
                                            : 'No
                                            Category';
                                            ?>
                                            <td class="v-align-middle" style="text-align:center;">{{ $categoryName }}
                                            </td>
                                            <?php
                                            $userName = $budget->getUserById($budget->created_by);
                                            $userName = isset($userName['name'])
                                            ? $userName['name']
                                            : 'No
                                            Category';
                                            ?>
                                            <td class="v-align-middle" style="text-align:center;">
                                                <a href="{{route('admin.users.show', ['user' => $budget->created_by])}}"><u>{{ $userName }}</u></a>
                                            </td>
                                            <td class="v-align-middle" style="text-align:center;">${{ $budget->target_budget_value }}
                                            </td>                                  
                                            <?php
                                            $value = $budget->BudgetAmount($budget->id);
                                            ?>
                                            <td class="v-align-middle" style="text-align:center;">${{ $budget->BudgetAmount($budget->id) }}
                                            </td>
                                           <?php
                                           $variance =  $budget->BudgetAmount($budget->id) - $budget->target_budget_value;
                                           ?>
                                           @if($variance > 0)
                                            <td class="v-align-middle" style="color: red; text-align:center;">${{ $variance }}</td>
                                            @else
                                            <td class="v-align-middle" style="color:  #00238C; text-align:center;">${{ abs($variance) }}</td>
                                            @endif
                                            <td class="v-align-middle">
                                                <div class="btn-group">
                                                    <a class="btn btn-primary" href="{{ url('admin/budgets/AddReceipts/' . $budget->id) }}" id="sessionSave" data-toggle="tooltip"
                                                        data-placement="bottom" title="Add reciept"
                                                        name="add"><i class="fa fa-plus"
                                                            aria-hidden="true"></i></a>
                                                            <input type="hidden" name="budget_id" id="budget_id" value="">    
                                                    <a href="{{ url('admin/budgets/preview') . '/' . $budget->id }}"
                                                        class="btn btn-complete" data-toggle="tooltip"
                                                        data-placement="bottom" title="Edit"><i class="fa fa-edit"></i>
                                                    </a>
                                                    <a href="{{ url('admin/budgets/delete/' . $budget->id) }}"
                                                        class="btn btn-danger"
                                                        onclick="return confirm('Are you sure you want to remove budget Ref#{{ $budget->id }} ?')"
                                                        data-toggle="tooltip" data-placement="bottom" title="Delete"><i
                                                            class="fa fa-trash-o"></i></a>
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
    </div>
    <div id="send" class="modal fade" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button><br>
                    <h4 class="modal-title"><strong>Send Envelope email to user</strong></h4>
                </div>
                <div class="modal-body">
                    <p>Are sure you want to send email envelope ?</p>
                </div>          
                <form method="post" action="">
                    <div class="modal-footer">
                        {{ csrf_field() }}
                        {{ method_field('POST') }}
                        <button type="submit" class="btn btn-danger">Send</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">No</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- END PAGE CONTAINER -->
@endsection

@section('script')
    <!-- BEGIN VENDOR JS -->

@endsection

<div class="tab-pane " id="subcat">
    <div class="card-title">
        <h5 class="text-center"><strong style="color: #626262 !important; padding-left:20px; text-align:center;">SUB
                CATEGORIES</strong> </h5>
        <div class="go-line"></div>
    </div>

    <div class="card-body p-t-20">
        <form action="{{ route('sub-category') }}" id="user_envelope" method="POST">
            {{ csrf_field() }}
            <div class="row justify-content-left">
                <div class="col-md-12">
                    <div class="form-group" style="float:none;">
                        <div class="row">
                            <div class="col-md-3">
                                <label>New Sub Category Name</label>
                                <input type="text" class="form-control" name="sub_cat_name" id="name" required>
                            </div>
                            <div class="col-md-2">
                                <label>Slug</label>
                                <input type="text" class="form-control" name="sub_cat_slug" id="name" required>
                            </div>
                            <div class="col-md-2">
                                <label>Main Category</label>
                                <select name="main_category" id="main_category" class="form-control" required>
                                    <option value=""> - Select a Category - </option>
                                    @foreach ($mainCategories as $mainCategory)
                                    <option value="{{ $mainCategory->id }}" <?php echo (isset($_GET['id'])&& $_GET['id']==$mainCategory->id)?'selected="selected"':'' ?>>
                                        <?php echo ($mainCategory->type=='accounting')?'Accounting':'Normal'; ?> :- {{ $mainCategory->name }}
                                    </option>
                                   @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-complete" style="width: 100%">Add Sub
                                    Category</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </form>
        <br>
        <hr>
        <div class="p-b-10  p-l-20  p-r-20">
            <div class="card-body p-t-10 searchFilters">
                <div class="row">

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="filter_sub_tab_id" class="control-label">ID</label>
                            <input type="text" id="filter_sub_tab_id" placeholder="id" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="filter_sub_tab_name" class="control-label">Name</label>
                            <input type="text" id="filter_sub_tab_name" placeholder="name" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="filter_sub_tab_slug" class="control-label">Slug</label>
                            <input type="text" id="filter_sub_tab_slug" placeholder="slug" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="filter_sub_tab_main_category" class="control-label">Main Category</label>
                        <select name="main_tab_category" id="filter_sub_tab_main_category" class="form-control" required>
                            <option value=""> - Select a Category - </option>
                            @foreach ($mainCategories as $mainCategory)
                                <option value="{{ $mainCategory->id }}" <?php echo (isset($_GET['id'])&& $_GET['id']==$mainCategory->id)?'selected="selected"':'' ?>>
                                    <?php echo ($mainCategory->type=='accounting')?'Accounting':'Normal'; ?> :- {{ $mainCategory->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

        </div>
        <hr>

        <div class="widget-11-2-table p-t-20">
            <table class="table table-hover table-condensed table-responsive" id="sub_category_table">
                <thead>
                    <tr>
                        <th style="width:  5%;"></th>
                        <th style="width:  5%;"> ID</th>
                        <th style="width: 30%;"> Name</th>
                        <th style="width: 30%;"> Type</th>
                        <th style="width: 30%;"> Slug</th>
                        <th style="width: 30%;"> Main Category</th>
                        <th style="width: 30%;"> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            <div class="col-xs-2 select-all-button p-b-10">
                <div class="p-t-10">
                    <div style="float: left;">
                        <button type="button" class="btn btn-info" id="selectAllSub">Select All</button>
                        <button type="button" class="btn btn-info" id="deselectAllSub"> De-Select
                            All</button>
                        <a href="{{ route('delete.categories') }}" style="vertical-align: middle;"
                            class="btn btn-info btn-md " id="delete-button">DELETE</a>
                    </div>
                </div>
            </div>

        </div>
    </br>

    </div>
</div>
<div id="delete-main-cat" class="modal fade" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Delete Category</h4>
            </div>
            <div class="modal-body">
                <p>Are sure you want to Delete this category?</p>
            </div>
            <form method="POST" action=" {{ url('admin/categories/delete') }} ">
                <div class="modal-footer">
                    {{ csrf_field() }}
                    <input type="hidden" name="main_cat_id" value="" id="main_cat_id">
                    <button type="submit" class="btn btn-danger">Delete</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
@section('page-js')
    
@endsection

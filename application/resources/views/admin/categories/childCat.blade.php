<div class="tab-pane " id="childcat">
    <div class="card-title">
        <h5 class="text-center"><strong style="color: #626262 !important; padding-left:20px; text-align:center;">CHILD
                CATEGORIES</strong> </h5>
        <div class="go-line"></div>
    </div>
    <div class="card-body p-t-20">
        <form action="{{ route('child-category') }}" id="user_envelope" method="POST">
            {{ csrf_field() }}
            <div class="row justify-content-left">
                <div class="col-md-12">
                    <div class="form-group" style="float:none;">
                        <div class="row">
                            <div class="col-md-3">
                                <label>New Child Category Name</label>
                                <input type="text" class="form-control" name="child_cat_name" id="name" required>
                            </div>
                            <div class="col-md-2">
                                <label>Slug</label>
                                <input type="text" class="form-control" name="child_cat_slug" id="name" required>
                            </div>
                            <div class="col-md-2">
                                <label>Sub Category</label>
                                <select name="sub_category" id="main_category" class="form-control" required>
                                    <option value=""> - Select a Category - </option>
                                    @foreach ($mainCategories as $mainCategory)
                                        <option value="{{ $mainCategory->id }}" disabled style="font-weight: bold;">
                                            {{ ucfirst($mainCategory->type) }} : {{ $mainCategory->name }}
                                        </option>
                                        @php
                                            $subCats = App\Models\Category::where('mainid', $mainCategory->id)->get();
                                        @endphp
                                        @foreach ($subCats as $subCat)
                                            <option value="{{ $subCat->id }}">{{ $subCat->name }}
                                            </option>
                                        @endforeach
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-complete" style="width: 100%">Add Child Catrgory</button>
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
                            <label for="filter_child_tab_id" class="control-label">ID</label>
                            <input type="text" id="filter_child_tab_id" placeholder="id" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="filter_child_tab_name" class="control-label">Name</label>
                            <input type="text" id="filter_child_tab_name" placeholder="name" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="filter_childtab_slug" class="control-label">Slug</label>
                            <input type="text" id="filter_child_tab_slug" placeholder="slug" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="filter_childtab_category" class="control-label">Sub Category</label>
                        <select name="filter_child_tab_category" id="filter_child_tab_category" class="form-control" required>
                            <option value=""> - Select a Category - </option>
                            @foreach ($mainCategories as $mainCategory)
                                <option value="{{ $mainCategory->id }}" disabled style="font-weight: bold;">
                                    {{ ucfirst($mainCategory->type) }} : {{ $mainCategory->name }}
                                </option>
                                @php
                                    $subCats = App\Models\Category::where('mainid', $mainCategory->id)->get();
                                @endphp
                                @foreach ($subCats as $subCat)
                                    <option value="{{ $subCat->id }}">{{ $subCat->name }}
                                    </option>
                                @endforeach

                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

        </div>
            <hr>

        <div class="widget-11-2-table p-t-20">
            <table class="table table-hover table-condensed table-responsive" id="child_category_table">
                <thead>

                    <tr>
                        <th style="width: 5%;"> #</th>
                        <th style="width: 5%;"> ID</th>
                        <th style="width: 30%;"> Name</th>
                        <th style="width: 30%;"> Type</th>
                        <th style="width: 30%;"> Slug</th>
                        <th style="width: 30%;"> Sub Category</th>
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
                        <button type="button" class="btn btn-info" id="selectAllChild">Select All</button>
                        <button type="button" class="btn btn-info" id="deselectAllChild"> De-Select
                            All</button>
                            <a href="{{ route('delete.categories') }}" style="vertical-align: middle;" class="btn btn-info btn-md"
                            id="delete-button">DELETE</a>
                    </div>
                </div>
            </div>
        </div>
    </br>
    </div>
</div>


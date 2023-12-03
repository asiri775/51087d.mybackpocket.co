<div class="tab-pane" id="maincat" role="tabpanel">
    <div class="card-title ">
        <h5 class="text-center"><strong style="color: #626262 !important; padding-left:20px; text-align:center;">MAIN
                CATEGORIES</strong> </h5>
        <div class="go-line"></div>
    </div>

    <div class="card-body p-t-20">
        <form action="{{ route('main-category') }}" id="main_cat" method="POST">
            {{ csrf_field() }}
            <div class="row justify-content-left">
                <div class="col-md-12">
                    <div class="form-group" style="float:none;">
                        <div class="row">
                            <div class="col-md-3">
                                <label>New Category Name</label>
                                <input type="text" class="form-control" name="main_cat_name" id="name" required>
                            </div>
                            <div class="col-md-3">
                                <label>Slug</label>
                                <input type="text" class="form-control" name="main_slug" id="name" required>
                            </div>
                            <div class="col-md-3">
                                <label>Category type</label>
                                <select name="category_type" id="category" class="form-control" required>
                                    <option value="">- Select Type -</option>
                                    <option value="normal">Normal</option>
                                    <option value="accounting">Accounting</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-complete" style="width: 100%">Add Main
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
                            <label for="filter_main_tab_id" class="control-label">ID</label>
                            <input type="text" id="filter_main_tab_id" placeholder="id" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="filter_main_tab_name" class="control-label">Name</label>
                            <input type="text" id="filter_main_tab_name" placeholder="name" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="filter_main_tab_slug" class="control-label">Slug</label>
                            <input type="text" id="filter_main_tab_slug" placeholder="slug" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="filter_main_tab_type" class="control-label">Type</label>
                            <select name="type" id="filter_main_tab_type" class="form-control">
                                <option value="">- Select Type -</option>
                                <option value="normal">Normal</option>
                                <option value="accounting">Accounting</option>
                            </select>
                        </div>
                    </div>
                  
                </div>
            </div>

        </div>

        <hr>

        <div class="widget-11-2-table p-t-20">
            <table class="table table-hover table-condensed table-responsive" id="main_category_table">
                <thead>

                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 5%;">ID</th>
                        <th style="width: 20%;"> Name</th>
                        <th style="width: 20%;"> Sub</th>
                        <th style="width: 15%;"> Child</th>
                        <th style="width: 15%;"> Slug</th>
                        <th style="width: 10%;"> Type</th>
                        <th style="width: 10%;"> Actions</th>
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
                        <button type="button" class="btn btn-info" id="selectAllMain">Select All</button>
                        <button type="button" class="btn btn-info" id="deselectAllMain"> De-Select
                            All</button>
                        <a href="{{ route('delete.categories') }}" style="vertical-align: middle;"
                            class="btn btn-info btn-md" id="delete-button">DELETE</a>
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
                    <input type="hidden" name="cat_id" value="" id="category_id">
                    <button type="submit" class="btn btn-danger">Delete</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
@section('page-js')
    <script>
        $(document).ready(function() {
            var array = [];
            $("#selectAllMain").on("click", function(e) {
                var table = $("#main_category_table");
                var boxes = $('input:checkbox', table);
                $.each($('input:checkbox', table), function() {

                    $(this).parent().addClass('checked');
                    $(this).prop('checked', 'checked');

                });
                $('#deselectAllMain').prop('disabled', false);
            });

            $("#deselectAllMain").on("click", function(e) {
                var table = $("#main_category_table");
                var boxes = $('input:checkbox', table);
                $.each($('input:checkbox', table), function() {

                    $(this).parent().removeClass('checked');
                    $(this).prop('checked', false);

                });
            });

        });
        $(document).ready(function() {
            var array = [];
            $("#selectAllSub").on("click", function(e) {
                var table = $("#sub_category_table");
                var boxes = $('input:checkbox', table);
                $.each($('input:checkbox', table), function() {

                    $(this).parent().addClass('checked');
                    $(this).prop('checked', 'checked');

                });
                $('#deselectAllSub').prop('disabled', false);
            });

            $("#deselectAllSub").on("click", function(e) {
                var table = $("#sub_category_table");
                var boxes = $('input:checkbox', table);
                $.each($('input:checkbox', table), function() {
                    $(this).parent().removeClass('checked');
                    $(this).prop('checked', false);

                });
            });

        });
        $(document).ready(function() {
            var array = [];
            $("#selectAllChild").on("click", function(e) {
                var table = $("#child_category_table");
                var boxes = $('input:checkbox', table);
                $.each($('input:checkbox', table), function() {

                    $(this).parent().addClass('checked');
                    $(this).prop('checked', 'checked');

                });
                $('#deselectAllChild').prop('disabled', false);
            });

            $("#deselectAllChild").on("click", function(e) {
                var table = $("#child_category_table");
                var boxes = $('input:checkbox', table);
                $.each($('input:checkbox', table), function() {
                    $(this).parent().removeClass('checked');
                    $(this).prop('checked', false);

                });
            });

        });
   
        $(document).ready(function(e) {
            $('#maincat').removeClass("active");
            <?php if(isset($_GET['type'])) {
        switch($_GET['type'])
        {
          case 'main':
          $main=1;
          $subcat=0;
          $childcat=0;
          break;
          case 'subcat':
          $main=0;
          $subcat=1;
          $childcat=0;
          break;
          case 'childcat':
          $main=0;
          $subcat=0;
          $childcat=1;
          break;
        }
        ?>

            $('#subcat').<?php echo $subcat ? 'addClass("active")' : 'removeClass("active")'; ?>;
            $('#maincat').<?php echo $main ? 'addClass("active")' : 'removeClass("active")'; ?>;
            $('#childcat').<?php echo $childcat ? 'addClass("active")' : 'removeClass("active")'; ?>;
            $('.tab-content #maincat').<?php echo $main ? 'addClass("active")' : 'removeClass("active")'; ?>;
            $('.tab-content #subcat').<?php echo $subcat ? 'addClass("active")' : 'removeClass("active")'; ?>;
            $('.tab-content #childcat').<?php echo $childcat ? 'addClass("active")' : 'removeClass("active")'; ?>;
            <?php } ?>
        });

        $(document).ready(function(e) {
            //datatable
            $.fn.dataTable.ext.errMode = 'none';
            var main_datatable = $('#main_category_table').DataTable({
                "serverSide": true,
                "processing": true,
                "sDom": "<'top'f<'clear'>><t><'row'<p i>>",
                "destroy": true,
                "scrollCollapse": true,
                "oLanguage": {
                    "sLengthMenu": "_MENU_ ",
                    "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
                },

                "iDisplayLength": 5,
                "ajax": {
                    "url": "{{ route('mainCat.datatable') }}",
                    "method": "POST",
                    "headers": {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    'data': function(data) {
                        data.main_tab_id = $('#filter_main_tab_id').val();
                        data.main_tab_name = $('#filter_main_tab_name').val();
                        data.main_tab_slug = $('#filter_main_tab_slug').val();
                        data.main_tab_type = $('#filter_main_tab_type').val();
                    }

                },
                "order": [
                    [0, "asc"]
                ],
                "columns": [{
                        data: 'checkbox',
                        name: 'checkbox',
                        searchable: false
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'sub',
                        name: 'sub'
                    },
                    {
                        data: 'child',
                        name: 'child'
                    },
                    {
                        data: 'slug',
                        name: 'slug'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'actions',
                        name: 'actions'
                    },
                ],
                "columnDefs": [{
                    "targets": 0,

                    "checkboxes": {
                        "selectRow": true
                    }
                }],

                "select": {
                    "style": "multi",
                    "selector": "td:first-child"
                },
            });
            $(document).on('keyup', '#filter_main_tab_id', function() {
                main_datatable.draw();
            });
            $(document).on('keyup', '#filter_main_tab_name', function() {
                main_datatable.draw();
            });
            $(document).on('keyup', '#filter_main_tab_slug', function() {
                main_datatable.draw();
            });
            $(document).on('change', '#filter_main_tab_type', function() {
                main_datatable.draw();
            });

        });

        $(document).ready(function(e) {
            //datatable
            $.fn.dataTable.ext.errMode = 'none';
            var sub_datatable = $('#sub_category_table').DataTable({
                "serverSide": true,
                "processing": true,
                "sDom": "<'top'f<'clear'>><t><'row'<p i>>",
                "destroy": true,
                "scrollCollapse": true,
                "oLanguage": {
                    "sLengthMenu": "_MENU_ ",
                    "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
                },

                "iDisplayLength": 5,
                "ajax": {
                    "url": "{{ route('subCat.datatable') }}",
                    "method": "POST",
                    "headers": {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    'data': function(data) {
                        data.main_tab_id = $('#filter_sub_tab_id').val();
                        data.main_tab_name = $('#filter_sub_tab_name').val();
                        data.main_tab_slug = $('#filter_sub_tab_slug').val();
                        data.main_tab_type = $('#filter_sub_tab_type').val();
                        data.main_tab_category = $('#filter_sub_tab_main_category').val();
                    }

                },
                "order": [
                    [0, "asc"]
                ],
                "columns": [{
                        data: 'checkbox',
                        name: 'checkbox',
                        searchable: false
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'slug',
                        name: 'slug'
                    },
                    {
                        data: 'main_category',
                        name: 'main_category'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    },
                ],
                "columnDefs": [{
                    "targets": 0,

                    "checkboxes": {
                        "selectRow": true
                    }
                }],

                "select": {
                    "style": "multi",
                    "selector": "td:first-child"
                },
            });
            $(document).on('keyup', '#filter_sub_tab_id', function() {
                sub_datatable.draw();
            });
            $(document).on('keyup', '#filter_sub_tab_name', function() {
                sub_datatable.draw();
            });
            $(document).on('keyup', '#filter_sub_tab_slug', function() {
                sub_datatable.draw();
            });
            $(document).on('change', '#filter_sub_tab_type', function() {
                sub_datatable.draw();
            });
            $(document).on('change', '#filter_sub_tab_main_category', function() {
                sub_datatable.draw();
            });

        });
        $(document).ready(function(e) {
            //datatable
            $.fn.dataTable.ext.errMode = 'none';
            var child_datatable = $('#child_category_table').DataTable({
                "serverSide": true,
                "processing": true,
                "sDom": "<'top'f<'clear'>><t><'row'<p i>>",
                "destroy": true,
                "scrollCollapse": true,
                "oLanguage": {
                    "sLengthMenu": "_MENU_ ",
                    "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
                },

                "iDisplayLength": 5,
                "ajax": {
                    "url": "{{ route('childCat.datatable') }}",
                    "method": "POST",
                    "headers": {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    'data': function(data) {
                        data.main_tab_id = $('#filter_child_tab_id').val();
                        data.main_tab_name = $('#filter_child_tab_name').val();
                        data.main_tab_slug = $('#filter_child_tab_slug').val();
                        data.main_tab_type = $('#filter_child_tab_type').val();
                        data.main_tab_category = $('#filter_child_tab_category').val();
                    }

                },
                "order": [
                    [0, "asc"]
                ],
                "columns": [{
                        data: 'checkbox',
                        name: 'checkbox',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'slug',
                        name: 'slug'
                    },
                    {
                        data: 'main_category',
                        name: 'main_category'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        searchable: false
                    },
                ],
                "columnDefs": [{
                    "targets": 0,

                    "checkboxes": {
                        "selectRow": true
                    }
                }],

                "select": {
                    "style": "multi",
                    "selector": "td:first-child"
                },
            });
            $(document).on('keyup', '#filter_child_tab_id', function() {
                child_datatable.draw();
            });
            $(document).on('keyup', '#filter_child_tab_name', function() {
                child_datatable.draw();
            });
            $(document).on('keyup', '#filter_child_tab_slug', function() {
                child_datatable.draw();
            });
            $(document).on('change', '#filter_child_tab_type', function() {
                child_datatable.draw();
            });
            $(document).on('change', '#filter_child_tab_category', function() {
                child_datatable.draw();
            });


        });


        function deleteCategory(cat_id) {
            $('#category_id').val(cat_id);
        }

        $("#filter_main").select2();
        $(document).on('change', '#filter_main', function() {
            sub_datatable.draw();
        });
    </script>


    <script>
        $('#main_category_table').on('click', 'input', function() {
            // console.log(this.is(':checked'));
            var isChecked = $(this).prop('checked');

            var id = $(this).val();

            if ($(this).is(':checked')) {
                var urlpost = "{{ route('Cat.addSession') }}";
                $.ajax({
                    type: 'POST',
                    url: urlpost,
                    data: {
                        id: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(data) {

                    }
                });
                // $('#delete-button').removeClass('disabled');
            } else {
                var urlpost = "{{ route('Cat.removeSession') }}";
                $.ajax({
                    type: 'POST',
                    url: urlpost,
                    data: {
                        id: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(data) {

                    }
                });
                array.splice(array.indexOf(id), 1);

                // $('#delete-button').addClass('disabled');
            }

            if (array.length > 0) {
                $('#deleteMain').removeClass('disabled');
            } else {
                $('#deleteMain').addClass('disabled');
            }

        });
    </script>
    <script>
        $('#sub_category_table').on('click', 'input', function() {
            var isChecked = $(this).prop('checked');

            var id = $(this).val();

            if ($(this).is(':checked')) {
                var urlpost = "{{ route('Cat.addSession') }}";
                $.ajax({
                    type: 'POST',
                    url: urlpost,
                    data: {
                        id: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(data) {

                    }
                });
                $('#delete-button').removeClass('disabled');
            } else {
                var urlpost = "{{ route('Cat.removeSession') }}";
                $.ajax({
                    type: 'POST',
                    url: urlpost,
                    data: {
                        id: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(data) {

                    }
                });
                array.splice(array.indexOf(id), 1);
                $('#delete-button').addClass('disabled');
            }


        });
    </script>
    <script>
        $('#child_category_table').on('click', 'input', function() {
            var isChecked = $(this).prop('checked');

            var id = $(this).val();

            if ($(this).is(':checked')) {
                var urlpost = "{{ route('Cat.addSession') }}";
                $.ajax({
                    type: 'POST',
                    url: urlpost,
                    data: {
                        id: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(data) {

                    }
                });
                $('#delete-button').removeClass('disabled');
            } else {
                var urlpost = "{{ route('Cat.removeSession') }}";
                $.ajax({
                    type: 'POST',
                    url: urlpost,
                    data: {
                        id: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(data) {

                    }
                });
                array.splice(array.indexOf(id), 1);
                $('#delete-button').addClass('disabled');
            }


        });
    </script>

@endsection

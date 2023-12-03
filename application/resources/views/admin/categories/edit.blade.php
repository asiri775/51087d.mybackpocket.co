@extends('admin.layouts.masterToCategories')
@section('title', 'Manage Categories')

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
                            <h5><strong>Edit Category - {{ $category->name }}</strong></h5><br>


                        </div>
                        <div class="pull-right">
                            <a href="{{ url('admin/categories') }}" class="btn btn-danger">Back</a>
                        </div>
                    </div>
                    <div class="card-body p-t-20">
                        <form action="{{ route('update.category.post', ['category' => $category->id]) }}" method="post"
                            class="form-control">
                            {{ csrf_field() }}
                            <br>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-1"></div>
                                    <label for="name" class="col-md-2">Name</label>
                                    <input type="text" id="name" name="name" class="form-control col-md-4"
                                        value="{{ $category->name }}">
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
                                    <div class="col-md-1"></div>
                                    <label for="slug" class="col-md-2">Slug</label>
                                    <input type="text" id="slug" name="slug" class="form-control col-md-4" value="{{ $category->slug }}">
                                </div>
                                <div class="row">
                                    <div class="col-md-3"></div>
                                    <div class="col-md-6">
                                        @if ($errors->has('slug'))
                                            <span class="help-block" style="color: red">{!! $errors->first('slug') !!}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                            <div class="row">
                                <div class="col-md-1"></div>
                                <label for="category" class="col-md-2">Category Type</label>
                                <select name="category_type" id="category" class="form-control col-md-4" required>
                                    <option value="">Select Category Type</option>
                                    <option value="normal" <?php echo ($category->type=='normal')?'selected="selected"':''?>>Normal</option>
                                    <option value="accounting" <?php echo ($category->type=='accounting')?'selected="selected"':''?>>Accounting</option>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-3"></div>
                                <div class="col-md-6">
                                    @if ($errors->has('category_type'))
                                        <span class="help-block" style="color: red">{!! $errors->first('category_type') !!}</span>
                                    @endif
                                </div>
                            </div>
                            </div>
                            @if ($category->role != 'main')
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-1"></div>
                                        <label class="col-md-2">Sub Category</label>
                                        <select name="main_category" id="main_category" class="form-control col-md-4"
                                            required>
                                            <option value="">Select a Category</option>
                                            @foreach ($mainCategories as $mainCategory)
                                                <option value="{{ $mainCategory->id }}"  @if ($category->role == "child") disabled @endif
                                                    style="font-weight: bold;" @if($category->mainid == $mainCategory->id) selected="selected" @endif>
                                                    {{ $mainCategory->name }}
                                                </option>
                                                @if ($category->role == "child")
                                                    @php
                                                        $subCats = App\Models\Category::where('mainid', $mainCategory->id)->get();
                                                    @endphp
                                                    @foreach ($subCats as $subCat)
                                                        <option value="{{ $subCat->id }}" @if($category->mainid == $subCat->id) selected="selected" @endif>
                                                            {{ $subCat->name }}
                                                        </option>
                                                    @endforeach
                                                @endif


                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3"></div>
                                        <div class="col-md-6">
                                            @if ($errors->has('slug'))
                                                <span class="help-block"
                                                    style="color: red">{!! $errors->first('slug') !!}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="form-group row">
                                <div class=" col-md-3"></div>
                                <button type="submit" class="btn btn-complete col-md-4" style="width: 200px;">UPDATE
                                    CATEGORY</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>







    <!-- END PAGE CONTENT WRAPPER -->
    <!-- END PAGE CONTAINER -->
@endsection

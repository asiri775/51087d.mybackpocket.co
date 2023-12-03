@extends('admin.layouts.masterToCategories')
@section('title', 'Manage Categories')

@section('page-css')
<style>
        .page-container .page-content-wrapper .content
        {
            padding-top: unset;
        }
        .dataTables_filter {
            display: none;
        }

        .card-header .nav-tabs .active
        {
            background-color: #10cfbd;
            
        }

        #ActivateAdvanceSerach, #HideActivateAdvanceSerach {
            color: #fff !important;
            background-color: #00238C !important;
            border-color: #3b475 !important;
        }

        .searchFilters {
            background-color: #eaebe6;
        }

        .card .card-body {
            padding: 5px 10px 5px 10px !important;
        }

        #transactionsTable_info {
            text-align: center;
            text-transform: uppercase;
            font-size: 14px;
        }

        .dataTables_wrapper .dataTables_paginate {
            margin-top: 15px !important;
            float: unset !important;
            text-align: center !important;
            margin-bottom: 20px !important;
        }

        .dataTables_wrapper .dataTables_paginate ul > li {
            font-size: 14px !important;
        }
       .batch-action{
           color: #fff !important;
           background-color: #010267 !important;
           border: 1px solid #f0f0f0 !important;
       }
        .batch-export{
            color: #fff !important;
            background-color: #248c01 !important;
            border: 1px solid #f0f0f0 !important;
        }

        .batch-export:hover,.batch-action:hover {
            background-color: #fafafa!important;
            border: 1px solid rgba(98, 98, 98, 0.27)!important;
            color: #333!important;
        }

        .batch-export.active,.batch-action.active {
            border-color: #e6e6e6!important;
            background: #fff!important;
            color: #333!important;
        }
        .form-control {
            font-family: Montserrat, sans-serif!important;
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
                            <h5><strong>Manage Categories</strong></h5><br>
                            <div class="col-md-12">
                                <ul class="nav nav-tabs tabs-left" role="tablist">
                                    <li  id="maincat">
                                        <a href="{{ route('category.list').'?type=main' }}" ><strong
                                                style="color: #000 !important; ">Main Category</strong></a>
                                    </li>
                                    <li id="subcat">
                                        <a href="{{ route('category.list').'?type=subcat' }}" ><strong
                                                style="color: #000 !important;">Sub Category</strong></a>

                                    </li>
                                    <li id="childcat">
                                        <a href="{{ route('category.list').'?type=childcat' }}" ><strong
                                                style="color: #000 !important;">Child Category</strong></a>

                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="tab-content">
                        @include('admin.categories.mainCat')

                        @include('admin.categories.subCat')

                        @include('admin.categories.childCat')
                    </div>

                </div>
                <!-- END card -->
            </div>
            <!-- END CONTAINER FLUID -->
        </div>

        <!-- END COPYRIGHT -->
    </div>

    <!-- END PAGE CONTENT WRAPPER -->
    <!-- END PAGE CONTAINER -->
@endsection

@section('page-js')




@endsection

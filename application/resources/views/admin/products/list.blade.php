@extends('admin.layouts.newMaster')

@section('title', 'Products List')

@section('page-css')
<style>
    .dataTables_filter {
        display: none;
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

    <!-- START CONTAINER FLUID -->
    <div class=" container-fluid   container-fixed-lg">
        @if (Session::has('success'))
            <div class="alert alert-success">{{ Session::get('success') }}</div>
        @elseif(Session::has('error'))
            <div class="alert alert-danger">{{ Session::get('error') }}</div>
    @endif
        <!-- START card -->
        <div class="card card-default p-l-20  p-r-20">
            <div class="card-header separator">
                <div class="card-title">
                    <h5><strong>Products</strong></h5>
                </div>
            </div>
            <div class="p-b-10">
                <div class="card-body p-t-10 searchFilters">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="from" class="control-label">Vendor</label>
                                <select id="vendor_filter" class="form-control">
                                    <option value="">Select Vendor</option>
                                    @foreach($vendors as $vendor)
                                        <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                          
                        </div>
                       <div class="col-md-2">
                        <div class="form-group">
                            <label for="from" class="control-label">From</label>
                            <input type="text" id="from" class="form-control" placeholder="mm-dd-yyyy">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="to" class="control-label">To</label>
                            <input type="text" id="to" class="form-control" placeholder="mm-dd-yyyy">
                        </div>
                    </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="date_options" class="control-label">Date Options</label>
                                <select name="date_options" id="date_options" class="form-control">
                                    <option value="">Pick an option</option>
                                    <option value="yesterday">Yesterday</option>
                                    <option value="today">Today</option>
                                    <option value="this_weekdays">This Weekdays</option>
                                    <option value="this_whole_week">This Whole Week</option>
                                    <option value="this_month">This Month</option>
                                    <option value="this_year">This Year</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="ActivateAdvanceSerach" class="control-label">&nbsp;</label>
                                <button type="button" class="btn btn-info form-control" id="ActivateAdvanceSerach">Advance Search</button>
                                <button type="button" class="btn btn-info form-control" id="HideActivateAdvanceSerach" style="display: none;">Hide Advance Search</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body searchFilters m-b-10" id="AdvanceFilters" style="display: none;">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="product_name" class="control-label">Product Name</label>
                                <select id="product_filter" class="form-control" style="width: 100%">
                                    <option value="">Select product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> 
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="sku" class="control-label">SKU</label>
                                <input class="form-control" type="text" id="sku">
                            </div>
                        </div>

                    </div>
                </div>
            </div>
                        <table class="table table-hover table-condensed table-responsive-block table-responsive"
                               id="tableProducts">
                            <thead>
                            <tr>
                                <!-- NOTE * : Inline Style Width For Table Cell is Required as it may differ from user to user
                                Comman Practice Followed
                                -->
                                <th></th>
                                <th style="width:10%;">ID</th>
                                <th style="width: 10%;">Vendor</th>
                                <th style="width: 10%;">Product Name</th>
                                <th style="width:10%;">SKU</th>
                                <th style="width: 10%;">Price</th>
                                <th style="width: 10%;">Created</th>
                                <th style="width: 20%;">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr></tr>
                            </tbody>
                        </table>
                        <div class="col-xs-2 select-all-button  p-b-10">
                            <div class="p-t-10">
                                <div style="float: left;">
                                    <button type="button" class="btn btn-default" id="selectAllProducts">Select All</button>
                                    <button type="button" class="btn btn-default" id="deselectAllProducts"> De-Select All</button>
                                    <a onclick="hideProducts()" href="javascript:void(0);" class="btn btn-success" id="hideProducts">Hide Products</a>
                                    <a  href="<?php echo url('/admin/products-visible'); ?>" class="btn btn-danger" id="showProducts">Show Products</a>
                                    <button type="button" target="_blank" class="btn btn-warning batch-action" id="footer-buttons-1" onclick="savePDF()">Batch Action1</button>
                                    <button type="button" target="_blank" class="btn btn-warning batch-action" id="footer-buttons-2" onclick="savePDF()">Batch Action2</button>
                                    <button type="button" onclick="bulkXLSTransactions()" id="footer-buttons-3" class="btn btn-success batch-export" ><i class="icon-file-excel" href="#"></i>Export XLS</button>
                                    <button type="button" onclick="printAllTransactions()" id="footer-buttons-4" class="btn btn-success batch-export" target="_blank "><i class="glyphicon glyphicon-print"></i> PRINT</button>
                                    <button type="button" onclick="savePDF()" id="footer-buttons-5" class="btn btn-success batch-export" target="_blank"><i class="icon-file-excel" href="#"></i>Save PDF</button>             
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
         
        <!-- END card -->

    <!-- END CONTAINER FLUID -->
    <div id="hide" class="modal fade" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Hide Product</h4>
                </div>
                <div class="modal-body">
                    <p>Are sure you want to Hide Product ?</p>
                </div>
                <form method="POST" action="{{url('admin/hide/products')}}">
                    <div class="modal-footer">
                        {{csrf_field()}}
                        <input type="hidden" name="product_id" value="" id="product_id">
                        <button type="submit" class="btn btn-danger">Hide</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('page-js')

    <script>
          $("#ActivateAdvanceSerach").click(function () {
            $("#AdvanceFilters").show();
            $("#HideActivateAdvanceSerach").show();
            $("#ActivateAdvanceSerach").hide()
        });
        $("#HideActivateAdvanceSerach").click(function () {
            $("#AdvanceFilters").hide();
            $("#HideActivateAdvanceSerach").hide();
            $("#ActivateAdvanceSerach").show();
        });
        function modalHide(product_id) {
            $('#product_id').val(product_id);
        }
        function hideProducts()
        {
            var ids = [];
            $.each($("input[name='pdr_checkbox[]']:checked"), function(){
                ids.push($(this).val());
            });
            if(ids.length > 0){
                var CSRF_TOKEN = $('input[name="_token"]').attr('value');
                $.ajax({
                    type: "POST",
                    dataType: 'JSON',
                    url: '<?php echo url('admin/hide-all/products'); ?>',
                    data: {_token: CSRF_TOKEN,product_ids : ids},
                    success: function (data) {
                    },
                    complete: function(){
                        window.location='<?=url('admin/products');?>';
                    }
                });
            }
        }
        $(document).ready(function () {
            var array = [];
            $("#selectAllProducts").on("click", function(e)
            {
                var table= $("#tableProducts");
                var boxes = $('input:checkbox', table);
                $.each($('input:checkbox', table), function() {
                    $(this).parent().addClass('checked');
                    $(this).prop('checked', 'checked');
                });
                $('#hideProducts').prop('disabled',false);
                $('#deselectAllProducts').prop('disabled',false);
            });

            $("#deselectAllProducts").on("click", function(e)
            {
                var table= $("#tableProducts");
                var boxes = $('input:checkbox', table);
                $.each($('input:checkbox', table), function()
                {
                    $(this).parent().removeClass('checked');
                    $(this).prop('checked', false);
                });
                $('#hideProducts').prop('disabled',true);
            });
        });

        $(document).ready(function (e) {
            var table = $('#tableProducts');
            $.fn.dataTable.ext.errMode = 'none';
            var product_datatable = table.DataTable({ 
                "serverSide": true,
                "sDom": '<"H"lfr>t<"F"ip>',
                "destroy": true,
                "pageLength": 10,
                "sPaginationType": "full_numbers",
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                "ajax": {
                    "url": "{{ route('products.datatable') }}",
                    "type": "POST",
                    'data': function(data){
                        data.vendor_id = $('#vendor_filter').val();
                        data.product_id = $('#product_filter').val();
                        data.sku = $('#sku').val();
                        data.from = $('#from').val();
                        data.to = $('#to').val();
                        data.date_option = $('#date_options').val();
                    }
                },
                "order": [[ 0, "asc" ]],
                "columns": [
                    // {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'checkboxes', name: 'checkboxes', orderable: false, searchable: false},
                    {data: 'id', name: 'id'},
                    {data: 'vendor', name: 'vendor', fnCreatedCell: function (nTd, sData, oData, iRow, iCol) {
                        $(nTd).html("<a style='color: #0090d9' href='/admin/vendors/"+oData.vendor_id+"'>"+oData.vendor+"</a>");
                    }},
                    {data: 'name', name: 'name'},
                    {data: 'sku', name: 'sku'},
                    {data: 'price',fnCreatedCell: function (nTd, sData, oData, iRow, iCol) {
                                $(nTd).html("$"+oData.price);
                            }},
                    {data: 'created', name: 'created'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                "language": {
                    "info": "Showing _START_ to _END_ of _TOTAL_ records",
                }
            });

            $("#vendor_filter").select2();
            $("#product_filter").select2();

            $('#product_name').keyup( function() {
                product_datatable.draw();
            });

            $('#sku').keyup( function() {
                product_datatable.draw();
            });

            $(document).on('change', '#vendor_filter', function () {
                product_datatable.draw();
            });
            $(document).on('change', '#product_filter', function () {
                product_datatable.draw();
            });

            $('#from').change( function() {
                product_datatable.draw();
            });
            $('#to').change( function() {
                product_datatable.draw();
            });
            $('#date_options').change( function() {
                product_datatable.draw();
            });

        //Date Pickers
         $('#from').datepicker({
                format: 'mm-dd-yyyy'
            });

            $('#to').datepicker({
                format: 'mm-dd-yyyy'
            });
        });
    </script>

@endsection

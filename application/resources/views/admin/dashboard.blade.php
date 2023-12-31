@extends('admin.layouts.newMaster')
@section('title', 'Dashboard')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-3 col-lg-6 col-sm-12">
                @include('admin.components.dashboard-card', ['background' => '#10cfbd', 'id' => 'week', 'title' => "Week"])
            </div>
            <div class="col-xl-3 col-lg-6 col-sm-12">
                @include('admin.components.dashboard-card', ['background' => '#10cfbd','id' => 'month', 'title' => "Month"])
            </div>
            <div class="col-xl-3 col-lg-6 col-sm-12">
                <div class="card" style="background: #10cfbd; height: 130px; box-shadow: rgba(0, 0, 0, 0.2) 0px 4px 6px -1px, rgba(0, 0, 0, 0.4) 0px 2px 4px -1px;">
                    <div class="card-body">
                        <h3 class="card-title" style="color: #fff">Search Vendor</h3>
                        <select name="vendor" id="filter_vendor">
                            <option value="">--Vendor--</option>
                            @foreach($vendors AS $vendor)
                            <option value="{{$vendor->id}}">{{$vendor->name}}</option>
                            @endforeach
                        </select>

                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-sm-12">
                <div class="card" style="background: #10cfbd; height: 130px; box-shadow: rgba(0, 0, 0, 0.2) 0px 4px 6px -1px, rgba(0, 0, 0, 0.4) 0px 2px 4px -1px;">
                    <div class="card-body">
                        <h3 class="card-title" style="color: #fff">Reports</h3>
                        <a style="color: #fff; font-size: 1.05rem; margin-right: 1.1rem;" href="{{route('sales.list')}}" id="salesByVendor" target="_blank"><i class="fa fa-money mr-2" aria-hidden="true"></i>Sales</a>
                        <a style="color: #fff; font-size: 1.05rem;" href="{{route('transactions.list')}}" id="transactionsByVendor" target="_blank"><i class="fa fa-paper-plane mr-2" aria-hidden="true"></i>Transactions</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-4 col-lg-12">
                @include('admin.components.vendor-dashboard-table')
            </div>
            <div class="col-xl-4 col-lg-12">
                @include('admin.components.sales-dashboard-table')
            </div>
        </div>
    </div>
@endsection
@section('page-js')
    <script>
        $(document).ready(function (e) {
            var table = $('#vendorsTable');
            $.fn.dataTable.ext.errMode = 'none';
            table.DataTable({
                "searching": false,
                "lengthMenu": [ 5, 10, 25, 50, 75, 100 ],
                "order": [[ 0, "desc" ]],
                "info": false,
                "pageLength": 5,
                "ajax": {
                    url: "{{ route('vendors.recent') }}",
                    dataSrc: ""
                },
                "columns": [
                        { "data": "name", "name": "name",
                            fnCreatedCell: function (nTd, sData, oData, iRow, iCol) {
                                $(nTd).html("<a style='color: #0090d9' href='/admin/vendors/"+oData.id+"'>"+oData.name+"</a>");
                            }
                        },
                        {data: 'email'},
                        {data: 'address'},
                        {data: 'store_no'},
                        {data: 'tax_no'}
                    ]
            });
        });
        $(document).ready(function (e) {
            var table = $('#salesTable');
            $.fn.dataTable.ext.errMode = 'none';
            table.DataTable({
                "searching": false,
                "info": false,
                "lengthMenu": [ 5, 10, 25, 50, 75, 100 ],
                "pageLength": 5,
                "ajax": {
                    url: "{{ route('sales.top') }}",
                    dataSrc: ""
                },
                "columns": [
                        {data: 'transaction.transaction_no'},
                        {data: 'product.name'},
                        {data: 'price',fnCreatedCell: function (nTd, sData, oData, iRow, iCol) {
                                $(nTd).html("$"+oData.price);
                            }},
                        {data: 'quantity'},
                        {data: 'created_at'}
                    ]
            });
            $("#filter_vendor").select2();
            $("#filter_vendor").on('change', function() {
               var $option = $(this).find('option:selected');
               var value = $option.val();
               var oldSalesUrl = $("#salesByVendor").attr("href"); // Get current url
               var oldTransactionsUrl = $("#transactionsByVendor").attr("href"); // Get current url
               var salesUrl='?vendor='+value;
               var transactionsUrl='?vendor='+value;
               var salesNewUrl = oldSalesUrl.replace("/admin/sales", "/admin/sales/"+salesUrl); // Create new url
               var transactionsNewUrl = oldTransactionsUrl.replace("/admin/transactions", "/admin/transactions/"+transactionsUrl); // Create new url
               $("#salesByVendor").attr("href", salesNewUrl);
               $("#transactionsByVendor").attr("href", transactionsNewUrl);

              // Set herf value
            });
        });
    </script>
@endsection
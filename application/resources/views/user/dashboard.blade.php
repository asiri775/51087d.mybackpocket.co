@extends('user.layouts.master')
@section('title', 'Dashboard')
@section('content')
   <!-- START CONTAINER FLUID -->
   <div class="container-fluid p-b-50">
    <div class="row">
        <div class="col-lg-4 col-xl-3 col-xlg-2 ">
            <div class="row">
                <div class="col-md-12 m-b-10">
                    <!-- START WIDGET D3 widget_graphTileFlat-->
                    <div id="credits"
                        class="widget-8 card no-border bg-primary no-margin widget-loader-bar">
                        <div class="container-xs-height full-height">
                            <div class="row-xs-height">
                                <div class="col-xs-height col-top">
                                    <div class="card-header  top-left top-right">
                                        <div class="card-title">
                                            <span
                                                class="font-montserrat fs-11 all-caps text-white font-weight-bold">Account
                                                Balance
                                            </span>
                                        </div>
                                        <div class="card-controls">
                                            <ul>
                                                <li>
                                                    <a data-toggle="refresh"
                                                        class="card-refresh text-black" href="#"><i
                                                            class="card-icon card-icon-refresh"></i></a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row-xs-height ">
                                <div class="col-xs-height col-top">
                                    <div class="row p-l-20 p-r-20">
                                        <div class="col-sm-6" style="width: 50%;">
                                            <h4 class="p-b-5 m-b-5 text-white text-center">
                                                $12,500
                                                <br>
                                                <small class="fs-10 all-caps">Credits</small>
                                            </h4>
                                        </div>
                                        <div class="col-sm-6" style="width: 50%;">
                                            <h4 class="p-b-5 m-b-5 text-white text-center">
                                                $14,000
                                                <br><small class="fs-10 all-caps m-b-5">cad$</small>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END WIDGET -->
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-xl-3 col-xlg-2 ">
            <div class="row">
                <div class="col-md-12 m-b-10">
                    <!-- START WIDGET D3 widget_graphTileFlat-->
                    <div class="widget-8 card no-border bg-success no-margin widget-loader-bar">
                        <div class="container-xs-height full-height">
                            <div class="row-xs-height">
                                <div class="col-xs-height col-top">
                                    <div class="card-header  top-left top-right">
                                        <div class="card-title">
                                            <span
                                                class="font-montserrat fs-11 all-caps text-white font-weight-bold">Weekly
                                                Spending <i class="fa fa-chevron-right"></i>
                                            </span>
                                        </div>
                                        <div class="card-controls">
                                            <ul>
                                                <li>
                                                    <a data-toggle="refresh"
                                                        class="card-refresh text-black" href="#"><i
                                                            class="card-icon card-icon-refresh"></i></a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row-xs-height ">
                                <div class="col-xs-height col-top relative">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="p-l-20">
                                                <h3 class="p-b-5 m-b-5 text-white">$14,000</h3>
                                                <p class="small m-t-5 p-t-5">
                                                    <a href="#!" class="fs-10 text-white">VIEW
                                                        DETAILS</a>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                        </div>
                                    </div>
                                    <div class="widget-8-chart line-chart" data-line-color="black"
                                        data-points="true" data-point-color="success"
                                        data-stroke-width="2">
                                        <svg>
                                            <g class="nvd3 nv-wrap nv-lineChart"
                                                transform="translate(-10,10)">
                                                <g>
                                                    <rect width="380" height="103" style="opacity: 0;">
                                                    </rect>
                                                    <g class="nv-x nv-axis"></g>
                                                    <g class="nv-y nv-axis"></g>
                                                    <g class="nv-linesWrap">
                                                        <g class="nvd3 nv-wrap nv-line"
                                                            transform="translate(0,0)">
                                                            <defs>
                                                                <clipPath id="nv-edge-clip-24382">
                                                                    <rect width="380" height="103">
                                                                    </rect>
                                                                </clipPath>
                                                            </defs>
                                                            <g clip-path="">
                                                                <g class="nv-groups">
                                                                    <g class="nv-group nv-series-0"
                                                                        style="stroke-opacity: 1; fill-opacity: 0.5; fill: rgb(0, 0, 0); stroke: rgb(0, 0, 0);">
                                                                        <path class="nv-line"
                                                                            d="M0,103L63.33333333333334,75.53333333333333L126.66666666666669,34.33333333333334L190,27.46666666666667L253.33333333333337,0L316.6666666666667,13.733333333333334L380,68.66666666666667">
                                                                        </path>
                                                                    </g>
                                                                </g>
                                                                <g class="nv-scatterWrap" clip-path="">
                                                                    <g class="nvd3 nv-wrap nv-scatter nv-chart-24382"
                                                                        transform="translate(0,0)">
                                                                        <defs>
                                                                            <clipPath
                                                                                id="nv-edge-clip-24382">
                                                                                <rect width="380"
                                                                                    height="103">
                                                                                </rect>
                                                                            </clipPath>
                                                                        </defs>
                                                                        <g clip-path="">
                                                                            <g class="nv-groups">
                                                                                <g class="nv-group nv-series-0"
                                                                                    style="stroke-opacity: 1; fill-opacity: 0.5; stroke: rgb(0, 0, 0); fill: rgb(0, 0, 0);">
                                                                                    <circle cx="0"
                                                                                        cy="103" r="3"
                                                                                        class="nv-point nv-point-0"
                                                                                        style="stroke-width: 2px;">
                                                                                    </circle>
                                                                                    <circle
                                                                                        cx="63.33333333333334"
                                                                                        cy="75.53333333333333"
                                                                                        r="3"
                                                                                        class="nv-point nv-point-1"
                                                                                        style="stroke-width: 2px;">
                                                                                    </circle>
                                                                                    <circle
                                                                                        cx="126.66666666666669"
                                                                                        cy="34.33333333333334"
                                                                                        r="3"
                                                                                        class="nv-point nv-point-2"
                                                                                        style="stroke-width: 2px;">
                                                                                    </circle>
                                                                                    <circle cx="190"
                                                                                        cy="27.46666666666667"
                                                                                        r="3"
                                                                                        class="nv-point nv-point-3"
                                                                                        style="stroke-width: 2px;">
                                                                                    </circle>
                                                                                    <circle
                                                                                        cx="253.33333333333337"
                                                                                        cy="0" r="3"
                                                                                        class="nv-point nv-point-4"
                                                                                        style="stroke-width: 2px;">
                                                                                    </circle>
                                                                                    <circle
                                                                                        cx="316.6666666666667"
                                                                                        cy="13.733333333333334"
                                                                                        r="3"
                                                                                        class="nv-point nv-point-5"
                                                                                        style="stroke-width: 2px;">
                                                                                    </circle>
                                                                                    <circle cx="380"
                                                                                        cy="68.66666666666667"
                                                                                        r="3"
                                                                                        class="nv-point nv-point-6"
                                                                                        style="stroke-width: 2px;">
                                                                                    </circle>
                                                                                </g>
                                                                            </g>
                                                                            <g class="nv-point-paths">
                                                                            </g>
                                                                        </g>
                                                                    </g>
                                                                </g>
                                                            </g>
                                                        </g>
                                                    </g>
                                                    <g class="nv-legendWrap"></g>
                                                    <g class="nv-interactive"></g>
                                                </g>
                                            </g>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END WIDGET -->
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-xl-3 col-xlg-2 ">
            <div class="row">
                <div class="col-md-12 m-b-10">
                    <!-- START WIDGET D3 widget_graphTileFlat-->
                    <div class="widget-8 card no-border bg-success no-margin widget-loader-bar"  style="background: url('assets/img/dashboard_tile-envelopes.jpg') no-repeat center;">
                        <div class="container-xs-height full-height" style="background-color: rgba(0, 0, 0, 0.6)">
                            <div class="row-xs-height">
                                <div class="col-xs-height col-top">
                                    <div class="card-header  top-left top-right">
                                        <div class="card-title">
                                            <span
                                                class="font-montserrat fs-11 all-caps text-white font-weight-bold">Envelopes
                                            </span>
                                        </div>
                                        <div class="card-controls">
                                            <ul>
                                                <li>
                                                    <a data-toggle="refresh"
                                                        class="card-refresh text-black" href="#"><i
                                                            class="card-icon card-icon-refresh"></i></a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row-xs-height ">
                                <div class="col-xs-height col-top relative">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="p-l-20">
                                                <!-- <h3 class="p-b-5 m-b-5 text-white">$14,000</h3> -->
                                                <p class="small m-t-5 p-t-5">
                                                    <a href="#!" class="fs-10 text-white">VIEW
                                                        DETAILS</a>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END WIDGET -->
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-xl-3 col-xlg-2 ">
            <div class="row">
                <div class="col-md-12 m-b-10">
                    <!-- START WIDGET D3 widget_graphTileFlat-->
                    <div class="widget-8 card no-border bg-success no-margin widget-loader-bar" style="background: url('assets/img/dashboard_tile-support.jpg') no-repeat center;">
                        <div class="container-xs-height full-height" style="background-color: rgba(0, 0, 0, 0.6)">
                            <div class="row-xs-height">
                                <div class="col-xs-height col-top">
                                    <div class="card-header  top-left top-right">
                                        <div class="card-title">
                                            <span
                                                class="font-montserrat fs-11 all-caps text-white font-weight-bold">Support
                                            </span>
                                        </div>
                                        <div class="card-controls">
                                            <ul>
                                                <li>
                                                    <a data-toggle="refresh"
                                                        class="card-refresh text-black" href="#"><i
                                                            class="card-icon card-icon-refresh"></i></a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row-xs-height ">
                                <div class="col-xs-height col-top relative">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="p-l-20">
                                                <!-- <h3 class="p-b-5 m-b-5 text-white">$14,000</h3> -->
                                                <p class="small m-t-5 p-t-5">
                                                    <a href="#!" class="fs-10 text-white">VIEW
                                                        DETAILS</a>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END WIDGET -->
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-xl-5 col-xlg-5 p-b-5">
            <div
                class="widget-11-2 card no-border card-condensed no-margin widget-loader-circle full-height d-flex flex-column">
                <div class="card-header  top-right">
                    <div class="card-controls">
                        <ul>
                            <li><a data-toggle="refresh" class="card-refresh text-black" href="#"><i
                                        class="card-icon card-icon-refresh"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="padding-25">
                    <div class="pull-left">
                        <h2 class="no-margin">My Receipts</h2>
                        <p class="no-margin">Recent Transactions</p>
                    </div>
                    <h3 class="pull-right semi-bold"><sup>
                            <small class="semi-bold">$</small>
                        </sup> {{ App\Models\Calculate::getTotalAmount(Auth::user()->id)}}
                    </h3>
                    <div class="clearfix"></div>
                </div>
                <div class="auto-overflow widget-11-2-table">
                    <table class="table table-condensed table-hover table-responsive" id="tableTransactions">
                        <thead>
                            <tr class="text-center">
                                <th class="all-caps">Date</th>
                                <th class="all-caps">Order ID</th>
                                <th class="all-caps">Merchant Name</th>
                                <th class="all-caps">amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="text-center">
                                <td class="fs-12"></td>
                            </tr>
                           
                        </tbody>
                    </table>
                </div>
                <div class="padding-25 mt-auto">
                    <p class="small">
                        <a href="{{ route('user.transactions.index') }}"><span>Go To Transactions Manager</span> <i
                                class="fa fs-12 fa-arrow-circle-o-right text-success m-l-10"></i></a>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-5 col-xl-3 col-xlg-3">
            <div class="widget-11-2 card no-border card-condensed no-margin widget-loader-circle ">
                <div class="card-header  top-right">
                    <div class="card-controls">
                        <ul>
                            <li><a data-toggle="refresh" class="card-refresh text-black" href="#"><i
                                        class="card-icon card-icon-refresh"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="padding-25">
                    <div class="pull-left">
                        <h2 class="no-margin">My Envelopes</h2>
                        <!-- <p class="no-margin">Recent Transactions</p> -->
                    </div>
                    <!-- <h3 class="pull-right semi-bold"><sup>
                            <small class="semi-bold">$</small>
                        </sup> 102,967
                    </h3> -->
                    <div class="clearfix"></div>
                </div>
                <div class="widget-11-2-table">
                    <table class="table table-condensed table-hover" id="tableEnvelope">
                        <thead>
                            <tr class="text-center">
                                <th class="all-caps">Name</th>
                                <th class="all-caps">items</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="text-center">
                                <td class="fs-12"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="padding-25 mt-auto">
                    <p class="small">
                        <a href="{{ route('user.envelopes.index') }}"><span>Go To Envelopes Manager</span> <i
                                class="fa fs-12 fa-arrow-circle-o-right text-success m-l-10"></i></a>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-7 col-xl-4 col-xlg-4">
            <div class="widget-11-2 card no-border card-condensed no-margin widget-loader-circle ">
                <div class="card-header  top-right">
                    <div class="card-controls">
                        <ul>
                            <li><a data-toggle="refresh" class="card-refresh text-black" href="#"><i
                                        class="card-icon card-icon-refresh"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="padding-25">
                    <div class="pull-left">
                        <h2 class="no-margin">My Stores</h2>
                        <!-- <p class="no-margin">Recent Transactions</p> -->
                    </div>
                    <!-- <h3 class="pull-right semi-bold"><sup>
                            <small class="semi-bold">$</small>
                        </sup> 102,967
                    </h3> -->
                    <div class="clearfix"></div>
                </div>
                <div class="widget-11-2-table">
                    <table class="table table-condensed table-hover table-responsive" id="tableStore">
                        <thead>
                            <tr class="text-center">
                                <th style="width: 20%;" class="all-caps">rank</th>
                                <th style="width: 30%;" class="all-caps">merchant</th>
                                <th style="width: 20%;" class="all-caps">qty</th>
                                <th style="width: 30%;" class="all-caps">spending</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="text-center">
                                <td class="fs-12">1</td>
                                <td class="fs-12"><a href="#!">Merchant 1</a></td>
                                <td class="fs-12">12</td>
                                <td class="fs-12">$50.00</td>
                            </tr>
                            <tr class="text-center">
                                <td class="fs-12">2</td>
                                <td class="fs-12"><a href="#!">Merchant 1</a></td>
                                <td class="fs-12">12</td>
                                <td class="fs-12">$50.00</td>
                            </tr>
                            <tr class="text-center">
                                <td class="fs-12">3</td>
                                <td class="fs-12"><a href="#!">Merchant 1</a></td>
                                <td class="fs-12">12</td>
                                <td class="fs-12">$50.00</td>
                            </tr>
                            <tr class="text-center">
                                <td class="fs-12">4</td>
                                <td class="fs-12"><a href="#!">Merchant 1</a></td>
                                <td class="fs-12">12</td>
                                <td class="fs-12">$50.00</td>
                            </tr>
                            <tr class="text-center">
                                <td class="fs-12">5</td>
                                <td class="fs-12"><a href="#!">Merchant 1</a></td>
                                <td class="fs-12">12</td>
                                <td class="fs-12">$50.00.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="padding-25 mt-auto">
                    <p class="small">
                        <a href="{{ route('user.stores.index') }}"><span>Go To Stores Manager</span> <i
                                class="fa fs-12 fa-arrow-circle-o-right text-success m-l-10"></i></a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END CONTAINER FLUID -->
@endsection
@section('page-js')
<script>
    $(document).ready(function (e) {
        $("#tableEnvelope").dataTable({
            "serverSide": true,
               "sDom": "<t><'row'<p i>>",
            "paging": false,
            "info": false,
            "destroy": true,
            "scrollCollapse": true,
            "oLanguage": {
                "sLengthMenu": "_MENU_ ",
                "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
            },
            "iDisplayLength": 5,
            "ajax": {
                    "url": "{{ route('user.dashboard.envelope.datatable') }}",
                    "method": "POST",
                },
                "order": [
                    [0, "asc"]
                ],
                "columns": [
                    {
                        data: 'name', name: 'name', fnCreatedCell: function (nTd, sData, oData, iRow, iCol) {
                            $(nTd).html("<a style='color: #0090d9' href='/user/envelopes/preview/" + oData.id + "'>" + oData.name + "</a>");
                        }
                    },
                    {
                        data: 'items',
                        name: 'items'
                    },
                  
                ],
        })

        $("#tableStore").dataTable({
            "sDom": "<t><'row'<p i>>",
            "paging": false,
            "info": false,
            "destroy": true,
            "scrollCollapse": true,
            "oLanguage": {
                "sLengthMenu": "_MENU_ ",
                "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
            },
            "iDisplayLength": 5
        })

        var table = $('#tableTransactions');
           $.fn.dataTable.ext.errMode = 'none';
           var trans_datatable = table.DataTable({
               "serverSide": true,
               "sDom": "<t><'row'<p i>>",
            "paging": false,
            "info": false,
            "destroy": true,
            "scrollCollapse": true,
            "oLanguage": {
                "sLengthMenu": "_MENU_ ",
                "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
            },
            "iDisplayLength": 5,
               "ajax": {
                    "url": "{{ route('user.dashboard.transactions.datatable') }}",
                    "method": "POST",
                },
                "order": [[0, "asc"]],
                "columns": [
                    {data: 'transaction_date', name: 'transaction_date', orderable: false, searchable: false},
                    {
                        data: 'order_no', name: 'order_no', fnCreatedCell: function (nTd, sData, oData, iRow, iCol) {
                            $(nTd).html("<a style='color: #0090d9' href='/user/user-transactions/" + oData.id + "'>" + oData.order_no + "</a>");
                        }
                    },
                    {
                        data: 'vendor_name', name: 'vendor_name', fnCreatedCell: function (nTd, sData, oData, iRow, iCol) {
                            $(nTd).html("<a style='color: #0090d9' href='/admin/vendors/" + oData.vendor_id + "'>" + oData.vendor_name + "</a>");
                        }
                    },
                    {data: 'total', name: 'total', orderable: false, searchable: false},
                ],
           });
    })
</script>
@endsection

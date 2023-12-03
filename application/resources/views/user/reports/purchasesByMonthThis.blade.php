@extends('user.layouts.master')

@section('title', 'Purchases By Marchant Report')

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

    #tableTransactions_info {
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
    <div class=" container-fluid   container-fixed-lg">
        @if (Session::has('success'))
        <div class="alert alert-success">{{ Session::get('success') }}</div>
    @elseif(Session::has('error'))
        <div class="alert alert-danger">{{ Session::get('error') }}</div>
@endif
        <!-- START card -->
        <div class="card card-default  p-l-20  p-r-20">
            <div class="card-header separator">
                <div class="card-title">
                    <h5><strong>Purchases by Month reports {{($time=="this_month")?': Month of '.date("F"):''}}</strong></h5>
                </div>
            </div>
                <hr>
                <div class="col-md-6">
                <table class=" table table-hover table-condensed table-responsive-block
                    table-responsive" id="tableTransactions">
                    <thead>
                        <tr>
                            <th style="width:5%">Category</th>
                            <th style="width:5%;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                       
                        @if(count($monthlyPurchaseData) > 0)
                         @foreach($categories as $category)
                         <tr>
                           <td>{{$category->name}}</td>
                                <td>${{ $monthlyPurchaseData[$category->id]['grandTotal'] }}</td>
                          </tr>   
    
                          @endforeach
                          <tr>
                            <td><b>Total</b></td>
                            <td><b>${{number_format(($total)?$total:0.00, 2, '.', '')}}</b></td>
                           </tr>
                        @else
                            <tr>
                                <td colspan="8" class="text-center">No Records Found</td>
                            </tr>
                        @endif
   
                    </tbody>
                    </table>
                </div>

        </div>
        <!-- END card -->
    </div>
    <!-- END CONTAINER FLUID -->

@endsection
@section('page-js')



@endsection

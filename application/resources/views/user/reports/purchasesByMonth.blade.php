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
        {{-- <form action="">
            <div class="p-b-10">
                <div class="card-body p-t-10 searchFilters">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="from" class="control-label">Year</label>
                                <select id="filter_category" class="form-control" name="year" onChange="">
                                    <option value="">Select</option>
                                    <?php  for($i = 0;$i <= 15;$i++) {?>
                                    <option value="{{ date('Y') - $i }}"
                                     <?php if (isset($_GET['year']) AND $_GET['year'] == date('Y') - $i) { echo "selected='selected'";} ?> value="{{date('Y') - $i}}">
                                        {{date('Y') - $i}}
                                    </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form> --}}
        <div class="card card-default  p-l-20  p-r-20">
            <div class="card-header separator">
                <div class="card-title">
                    <h5><strong>Purchases by Month reports {{($time=="this_year")?': Year '.date("Y"):''}}</strong></h5>
                </div>
            </div>
        
                <hr>
                <div class="">
                <table class=" table table-hover table-condensed table-responsive-block
                    table-responsive" id="tableTransactions">
                    <thead>
                        <tr>
                            <th style="width:5%">Category</th>
                            <th style="width:5%;">JAN</th>
                            <th style="width: 5%;">FEB</th>
                            <th style="width: 5%;">MAR</th>
                            <th style="width: 5%;">APR</th>
                            <th style="width: 5%;">MAY</th>
                            <th style="width: 5%;">JUN</th>
                            <th style="width: 5%;">JUL</th>
                            <th style="width: 5%;">AUG</th>
                            <th style="width: 5%;">SEP</th>
                            <th style="width: 5%;">OCT</th>
                            <th style="width: 5%;">NOV</th>
                            <th style="width: 5%;">DEC</th> 
                        </tr>
                    </thead>
                    <tbody>
                       
                        @if(count($monthlyPurchaseData) > 0)
                         @foreach($categories as $category)
                         <tr>
                           <td>{{$category->name}}</td>
                             @for($i = 0;$i<12;$i++)
                                <td>${{ $monthlyPurchaseData[$category->id][$i]['grandTotal'] }}</td>
                             @endfor   
                          </tr>   
    
                          @endforeach
                          <tr>
                            <td><b>Total</b></td>
                            @for($i = 0;$i<12;$i++)
                            <td><b>${{number_format(($monthlyPurchaseTotal[$i])?$monthlyPurchaseTotal[$i]:0.00, 2, '.', '')}}</b></td>
                            @endfor   
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

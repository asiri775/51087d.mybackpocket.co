@extends('user.layouts.master')
@section('title', 'Reports')

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
                            <h5><strong>Standard Reports</strong></h5>
                        </div>
                    </div>
                    <div class="card-body p-t-20">

                        <div class="widget-11-2-table p-t-20">
                            <table class="table table-hover table-condensed table-responsive" id="tableCategory">
                                <thead>
                                    <tr>
                                        <!-- NOTE * : Inline Style Width For Table Cell is Required as it may differ from user to user
                                            Comman Practice Followed
                                            -->
                                        <th style="width: 20%;">ID</th>
                                        <th style="width: 30%;">Report Name</th>
                                        <th style="width: 50%;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="v-align-middle">1</td>
                                        <td class="v-align-middle">Purchases by Category</td>
                                        <td class="v-align-middle">
                                            <div class="btn-group">
                                                <a href="{{ route('user.reports.purchaseByCategory', ['time' => "this_month"]) }}" class="btn btn-primary">This Month
                                                </a>
                                                <a href="{{ route('user.reports.purchaseByCategory', ['time' => "this_year"]) }}" class="btn btn-success">This Year
                                                </a>
                                                <a href="{{ route('user.reports.purchaseByCategory', ['time' => "all_time"]) }}" class="btn btn-complete">All Time
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="v-align-middle">2</td>
                                        <td class="v-align-middle">Purchases by Vendor</td>
                                        <td class="v-align-middle">
                                            <div class="btn-group">
                                                <a href="{{ route('user.reports.purchaseByVendor', ['time' => "this_month"]) }}" class="btn btn-primary">This Month
                                                </a>
                                                <a href="{{ route('user.reports.purchaseByVendor', ['time' => "this_year"]) }}" class="btn btn-success">This Year
                                                </a>
                                                <a href="{{ route('user.reports.purchaseByVendor', ['time' => "all_time"]) }}" class="btn btn-complete">All Time
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="v-align-middle">3</td>
                                        <td class="v-align-middle">Purchases by Month</td>
                                        <td class="v-align-middle">
                                            <div class="btn-group">
                                                <a href="{{ route('user.reports.purchasesByMonth', ['time' => "this_month"]) }}" class="btn btn-primary">This Month
                                                </a>
                                                <a href="{{ route('user.reports.purchasesByMonth', ['time' => "this_year"]) }}" class="btn btn-success">This Year
                                                </a>
                                                <a href="{{ route('user.reports.purchasesByMonth', ['time' => "all_time"]).'?year=all' }}" class="btn btn-complete">All Time
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="v-align-middle">4</td>
                                        <td class="v-align-middle">My Envelopes</td>
                                        <td class="v-align-middle">
                                            <div class="btn-group">
                                                <a href="{{ route('user.reports.myEnvelopesReports',  ['time' => "this_month"]) }}" class="btn btn-primary">This Month
                                                </a>
                                                <a href="{{ route('user.reports.myEnvelopesReports',  ['time' => "this_year"]) }}" class="btn btn-success">This Year
                                                </a>
                                                <a href="{{ route('user.reports.myEnvelopesReports',  ['time' => "all_time"]) }}" class="btn btn-complete">All Time
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="v-align-middle">5</td>
                                        <td class="v-align-middle">My Budgets</td>
                                        <td class="v-align-middle">
                                            <div class="btn-group">
                                                <a href="{{ route('user.reports.myBudgetsReports',  ['time' => "this_month"]) }}" class="btn btn-primary">This Month
                                                </a>
                                                <a href="{{ route('user.reports.myBudgetsReports',  ['time' => "this_year"]) }}" class="btn btn-success">This Year
                                                </a>
                                                <a href="{{ route('user.reports.myBudgetsReports',  ['time' => "all_time"]) }}" class="btn btn-complete">All Time
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- END card -->
            </div>
            <!-- END CONTAINER FLUID -->
        </div>
        <!-- END CONTAINER FLUID -->
    </div>


    <!-- END PAGE CONTENT WRAPPER -->
    <!-- END PAGE CONTAINER -->
@endsection

@section('script')
    <!-- BEGIN VENDOR JS -->


@endsection

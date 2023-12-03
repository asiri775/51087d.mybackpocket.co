@extends('admin.layouts.newMaster')
@section('title', 'User Budget Report')

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
                            <h5><strong>Budget Report</strong></h5>
                        </div>
                    </div>
                    <div class="card-body p-t-20">
                        <table style="border:1px dashed black;">
                            <tbody>

                                <tr style="align: center;">
                                    <td
                                        style=" font-family: 'Montserrat';font-size: 15px; color:#626262; font-weight: bold; text-align: right; border:1px dashed black;">
                                        User ID # : &nbsp;&nbsp;&nbsp;</td>
                                    <td style=" border:1px dashed black; width:20px;"></td>
                                    <td
                                        style=" font-family: 'Montserrat';font-size: 15px; color:#626262; text-align: left; border:1px dashed black;">
                                        &nbsp;&nbsp;{{ $user->id }}&nbsp;&nbsp;</td>
                                </tr>
                                <tr style="align: center;">
                                    <td
                                        style=" font-family: 'Montserrat';font-size: 15px; color:#626262; font-weight: bold; text-align: right; border:1px dashed black;">
                                        User Name : &nbsp;&nbsp;&nbsp;</td>
                                    <td style=" border:1px dashed black; width:20px;"></td>
                                    <td
                                        style=" font-family: 'Montserrat';font-size: 15px; color:#626262; text-align: left; border:1px dashed black;">
                                        &nbsp;&nbsp;{{ $user->name }}&nbsp;&nbsp;</td>
                                </tr>
                            </tbody>
                        </table>

                    </div>


                    <hr>
                    <div class="widget-11-2-table p-t-20 p-l-20 p-r-20">
                        <table class="table table-hover table-condensed table-responsive" id="tableBudget">
                            <thead>
                                <tr>
                                    <!-- NOTE * : Inline Style Width For Table Cell is Required as it may differ from user to user
                                     Comman Practice Followed
                                     -->

                                    <th style="width: 5%;">ID</th>
                                    <th style="width: 15%;"> Name</th>
                                    <th style="width: 20%;"> Category</th>
                                    <th style="width: 20%;"> Date</th>
                                    <th style="width: 20%;"> Value</th>
                                    <th style="width: 20%;"> Budget Value</th>
                                    {{-- <th style="width: 20%;">Amount</th> --}}
                                    <!--                                         <th style="width: 5%;">Status</th>-->
                                    <th style="width: 30%;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($budgets as $budget)
                                    <tr>

                                        <td class="v-align-middle">{{ $budget->id }}</td>
                                        <td class="v-align-middle">{{ $budget->name }}</td>
                                        <?php
                                        $categoryName = $budget->getCategoryName($budget->category_id);
                                        $categoryName = isset($categoryName['name'])
                                        ? $categoryName['name']
                                        : 'No
                                        Category';
                                        ?>
                                        <td class="v-align-middle">{{ $categoryName }}
                                        </td>
                                        <td class="v-align-middle">{{ $budget->budget_date }}</td>

                                        <?php
                                        $amount = $budget->getBudgetAmountById($budget->id);
                                        $amount = isset($amount)
                                        ? $amount
                                        : 'No
                                        Receipts';
                                        ?>
                                        <td class="v-align-middle">$ {{ $amount }}</td>
                                        <td class="v-align-middle">${{ $budget->target_budget_value }}</td>
                                        <td class="v-align-middle">
                                            <div class="btn-group">
                                                <a href="{{ url('admin/budgets/preview') . '/' . $budget->id }}"
                                                    class="btn btn-complete" data-toggle="tooltip" data-placement="bottom"
                                                    title="Edit Budget"><i class="fa fa-edit"></i>
                                                </a>
                                                {{-- <a href="{{ url('admin/reports/preview') . '/' . $envelope->id }}"
                                                    class="btn btn-success" data-toggle="tooltip" data-placement="bottom"
                                                    title="Preview Envelope"><i class="fa fa-eye"></i>
                                                </a> --}}
                                                {{-- <a href="{{ url('admin/envelopes/delete/' . $envelope->id) }}"
                                                    class="btn btn-danger"
                                                    onclick="return confirm('Are you sure you want to remove envelope Ref#{{ $envelope->id }} ?')"
                                                    data-toggle="tooltip" data-placement="bottom" title="Delete"><i
                                                        class="fa fa-trash-o"></i></a>
                                                @if ($envelope->category_id != null)
                                                    <a class="btn btn-primary"
                                                        href="{{ url('admin/envelopes/print') . '/' . $envelope->id }}"
                                                        data-toggle="tooltip" data-placement="bottom" title="Add reciept"><i
                                                            class="fa fa-archive"></i></a>
                                                @else
                                                    <a class="btn btn-primary" href="#!" data-toggle="tooltip"
                                                        data-placement="bottom" title="Add reciept"
                                                        onclick="return confirm('No Transactions')"><i
                                                            class="fa fa-archive"></i></a>
                                                @endif
                                                <a class="btn btn-complete" data-toggle="modal" data-target="#send"
                                                    data-placement="bottom" title="Email">
                                                    <i class="fa fa-envelope" style="color: #FFF;"></i></a> --}}

                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>



                    </div>
                    <div class="content-group "
                        style="float: right !important; padding-right: 100px; padding-right: 100px;  ">
                        <br>
                        <div class="form-group">
                            <div class="col-md-12">
                                <div class="row p-l-20">
                                    <div class="col-xs-12">
                                        <label style="font-size: 14px;"><b>Summary :
                                                {{ $budgetCount }}&nbsp;Items&nbsp;|&nbsp;${{ $grandTotal }}</b></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="left-pagination" style="padding-top: 20px ; padding-bottom: 20px; padding-left: 20px;">
                        <a href="{{ url('admin/reports') }}" class="btn btn-info btn-md">Back to Manage Budget Reports</a>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-md">Back to Home</a>
                    </div>
                </div>
            </div>
            <!-- END card -->
        </div>
        <!-- END CONTAINER FLUID -->
    </div>
    <!-- END PAGE CONTENT -->
    <!-- START COPYRIGHT -->
    <!-- START CONTAINER FLUID -->
    <!-- START CONTAINER FLUID -->

    <!-- END COPYRIGHT -->

    <!-- END PAGE CONTENT WRAPPER -->
    </div>

    <!-- END PAGE CONTAINER -->
@endsection

@section('script')
    <!-- BEGIN VENDOR JS -->
    <script src="assets/plugins/pace/pace.min.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery/jquery-3.2.1.min.js" type="text/javascript"></script>
    <script src="assets/plugins/modernizr.custom.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
    <script src="assets/plugins/popper/umd/popper.min.js" type="text/javascript"></script>
    <script src="assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery/jquery-easy.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery-unveil/jquery.unveil.min.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery-ios-list/jquery.ioslist.min.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery-actual/jquery.actual.min.js"></script>
    <script src="assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script type="text/javascript" src="assets/plugins/select2/js/select2.full.min.js"></script>
    <script type="text/javascript" src="assets/plugins/classie/classie.js"></script>
    <script src="assets/plugins/switchery/js/switchery.min.js" type="text/javascript"></script>
    <script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
    <script src="assets/plugins/moment/moment.min.js"></script>
    <script src="assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script src="assets/plugins/bootstrap-timepicker/bootstrap-timepicker.min.js"></script>
    <script src="assets/plugins/jquery-datatable/media/js/jquery.dataTables.min.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery-datatable/extensions/TableTools/js/dataTables.tableTools.min.js"
        type="text/javascript"></script>
    <script src="assets/plugins/jquery-datatable/media/js/dataTables.bootstrap.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery-datatable/extensions/Bootstrap/jquery-datatable-bootstrap.js" type="text/javascript">
    </script>
    <script type="text/javascript" src="assets/plugins/datatables-responsive/js/datatables.responsive.js"></script>
    <script src="assets/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
    <!-- END VENDOR JS -->
    <!-- BEGIN CORE TEMPLATE JS -->
    <script src="pages/js/pages.js"></script>
    <!-- END CORE TEMPLATE JS -->
    <!-- BEGIN PAGE LEVEL JS -->
    <script src="assets/js/scripts.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL JS -->
    <!-- END CORE TEMPLATE JS -->
    <!-- BEGIN PAGE LEVEL JS -->
    <script src="assets/js/datatables.js" type="text/javascript"></script>
    <!-- <script src="assets/js/form_elements.js" type="text/javascript"></script> -->
    <script src="assets/js/scripts.js" type="text/javascript"></script>
    <script>
        $(document).ready(function(e) {
            //datatable
            var table = $('#tableBudget');
            table.dataTable({
                "sDom": "<'top'f<'clear'>><t><'row'<p i>>",
                "destroy": true,
                "scrollCollapse": true,
                "oLanguage": {
                    "sLengthMenu": "_MENU_ ",
                    "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
                },
                "iDisplayLength": 5
            })

            //Date Pickers
            $('#daterangepicker').daterangepicker({
                timePicker: true,
                timePickerIncrement: 30,
                format: 'MM/DD/YYYY h:mm A'
            }, function(start, end, label) {
                console.log(start.toISOString(), end.toISOString(), label);
            });

            //form validation
            $("#user_form").validate();
        });

    </script>
@endsection

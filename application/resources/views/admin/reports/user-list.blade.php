@extends('admin.layouts.masterToManageNoDatatable')
@section('title', 'Manage Users')

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
    <script type="text/javascript">
        function closePrint () {
            document.body.removeChild(this.__container__);
        }

        function setPrint () {
            this.contentWindow.__container__ = this;
            this.contentWindow.onbeforeunload = closePrint;
            this.contentWindow.onafterprint = closePrint;
            this.contentWindow.focus(); // Required for IE
            this.contentWindow.print();
        }

        function printPage (sURL) {
            var oHiddFrame = document.createElement("iframe");
            oHiddFrame.onload = setPrint;
            oHiddFrame.style.visibility = "hidden";
            oHiddFrame.style.position = "fixed";
            oHiddFrame.style.right = "0";
            oHiddFrame.style.bottom = "0";
            oHiddFrame.src = sURL;
            document.body.appendChild(oHiddFrame);
        }
    </script>
    <div class="page-content-wrapper ">
        <!-- START PAGE CONTENT -->
        <div class="content ">
            <!-- START JUMBOTRON -->
            @if (Session::has('success'))
                <div class="alert alert-success">{{ Session::get('success') }}</div>
            @elseif(Session::has('error'))
                <div class="alert alert-danger">{{ Session::get('error') }}</div>
            @endif
            <script type="text/javascript">
                function modalSend(trans_id) {
                    $('#trans_id').val(trans_id);
                }
            </script>
            <!-- END JUMBOTRON -->
            <!-- START CONTAINER FLUID -->
            <div class=" container-fluid   container-fixed-lg">
                <!-- START card -->
                <div class="card card-default">
                    <div class="card-header separator">
                        <div class="card-title">
                            <h5><strong>Report: User Envelopes</strong></h5>
                        </div>
                    </div>
                    <div class="card-body p-t-20">
                        <table style="border:1px dashed black;">
                            <tbody>

                                <tr style="align: center;">
                                    <td
                                        style=" font-family: 'Montserrat';font-size: 15px; color:#626262; font-weight: bold; text-align: right; border:1px dashed black;">
                                        User ID : &nbsp;</td>
                                    <td style=" border:1px dashed black; width:20px;"></td>
                                    <td
                                        style=" font-family: 'Montserrat';font-size: 15px; color:#626262; text-align: left; border:1px dashed black;">
                                        &nbsp;&nbsp;{{ $user->id }}&nbsp;</td>
                                </tr>
                                <tr style="align: center;">
                                    <td
                                        style=" font-family: 'Montserrat';font-size: 15px; color:#626262; font-weight: bold; text-align: right; border:1px dashed black;">
                                        User Name : &nbsp;</td>
                                    <td style=" border:1px dashed black; width:20px;"></td>
                                    <td
                                        style=" font-family: 'Montserrat';font-size: 15px; color:#626262; text-align: left; border:1px dashed black;">
                                        &nbsp;&nbsp;{{ $user->name }}&nbsp;</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <hr>
                    <div class="widget-11-2-table p-t-20">
                        <table class="table table-hover table-condensed table-responsive" id="tableEnvelope">
                            <thead>
                                <tr>
                                    <!-- NOTE * : Inline Style Width For Table Cell is Required as it may differ from user to user
                                     Comman Practice Followed
                                     -->
                                    {{-- <th style="width: 5%;">#</th> --}}
                                    <th style="width: 5%;">ID</th>
                                    <th style="width: 15%;">Name</th>
                                    <th style="width: 20%;">Category</th>
                                    <th style="width: 20%;">Create Date</th>
                                    <th style="width: 20%;">Amount</th>
                                    {{-- <th style="width: 20%;">Amount</th> --}}
                                    <!--                                         <th style="width: 5%;">Status</th>-->
                                    <th style="width: 30%;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($envelopes as $envelope)
                                    <tr>
                                        {{-- <td class="v-align-middle" type="checkbox" name="check[]"
                                            value="{{ $envelope->id }}"></td> --}}
                                        <td class="v-align-middle" style="text-align: center;">{{ $envelope->id }}</td>
                                        <td class="v-align-middle" style="text-align: center;"><a
                                                href="{{ route('preview-envelope', $envelope->id) }}"
                                                target="_blank">{{ $envelope->name }}</a></td>
                                        <?php
                                        $categoryName = $envelope->getCategoryName($envelope->category_id);
                                        $categoryName = isset($categoryName['name'])
                                        ? $categoryName['name']
                                        : 'No
                                        Category';
                                        ?>
                                        <td class="v-align-middle" style="text-align: center;">{{ $categoryName }}
                                        </td>
                                        <?php $date = strtotime($envelope->envelope_date); ?>
                                        <td class="v-align-middle">{{ date('m/d/Y', $date) }}</td>
                                        <?php
                                        $amount = $envelope->getEnvelopeAmountById($envelope->id);
                                        $amount = isset($amount)
                                        ? $amount
                                        : 'No
                                        Receipts';
                                        ?>
                                        <td class="v-align-middle" style="text-align: center;">$ {{ $amount }}</td>
                                        <!--                                        <td class="v-align-middle">{{ $envelope->envelope_status }}</td>-->
                                        <td class="v-align-middle">
                                            <div class="btn-group">
                                                <a href="{{ url('admin/envelopes/preview') . '/' . $envelope->id }}"
                                                    class="btn btn-complete" data-toggle="tooltip" data-placement="bottom"
                                                    title="Edit Envelope"><i class="fa fa-edit"></i>
                                                </a>
                                                <a href="{{ url('admin/reports/preview') . '/' . $envelope->id }}"
                                                    class="btn btn-success" data-toggle="tooltip" data-placement="bottom"
                                                    title="Preview Envelope"><i class="fa fa-eye"></i>
                                                </a>
                                                <a href="{{ url('admin/envelopes/delete/' . $envelope->id) }}"
                                                    class="btn btn-danger"
                                                    onclick="return confirm('Are you sure you want to remove envelope Ref#{{ $envelope->id }} ?')"
                                                    data-toggle="tooltip" data-placement="bottom" title="Delete"><i
                                                        class="fa fa-trash-o"></i></a>


                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
 
                    <div style="padding: 25px 20px 20px 20px; ">
                        <div class="pull-left">
                            <button type="button" class="btn btn-default" id="selectAllEnvelopes">Select All</button>
                            <button type="button" class="btn btn-default" id="deselectAllEnvelopes" @if (!Session::has('envelope_id')) disabled @endif>De-Select
                                All
                            </button>
                            <a href="{{ url('admin/reports') }}" class="btn btn-info btn-md">Back to Reports</a>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-md">Back to Home</a>
                        </div>
                        <div class="pull-right">
                            <button type="button" class="btn btn-success" style="background-color: green;" data-toggle="modal" data-target="#link" onclick="modalSend(<?=$envelope->id?>)"><i class="fa fa-share-alt"></i>&nbsp;&nbsp;Share</button>
                            <button type="button" class="btn btn-success" style="background-color: green;" onclick="printPage('<?=url('admin/reports/printUserReportPdf').'/'.$user->id?>');"><i class="fa fa-print"></i>&nbsp;Print</button>
                            <a type="button" class="btn btn-success" target="_blank" href="<?=url('admin/reports/printUserReportPdfDownload').'/'.$user->id?>" style="background-color: green;"><i class="fa fa-download"></i>&nbsp;PDF</a>
                            <button type="button" class="btn btn-success" style="background-color: green;"><i class="fa fa-download"></i>&nbsp;xls</button>
                            
                        </div>
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
    <div id="link" class="modal fade" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button><br>
                    <h4 class="modal-title"><strong>Share Report</strong></h4>
                </div>
                <form method="post" action="{{route('preview.notify.report')}}">
                    {{ csrf_field() }}
                    {{ method_field('POST') }}
                    <div class="modal-body">
                        <br>
                        <p style="font-size: 20px;">Enter Your Email</p>
                        <input type="text" placeholder="email@domain.com" class="form-control" name="send_email">
                        <input type="hidden" name="user_id" id="user_id" value="{{ $user->id }}">
                    </div>
                    <div class="modal-footer">
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-danger">Send</button>
                            <button type="button" class="btn btn-primary" data-dismiss="modal">No</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
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
            var table = $('#tableEnvelope');
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

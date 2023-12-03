@extends('admin.layouts.masterToManageNoDatatable')
@section('title', 'Manage Envelopes')

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
            <!-- END JUMBOTRON -->
            <!-- START CONTAINER FLUID -->
            <div class=" container-fluid   container-fixed-lg">
                <!-- START card -->
                <div class="card card-default">
                    <div class="card-header separator">
                        <div class="card-title">
                            <h5><strong>Manage Envelopes</strong></h5>
                        </div>
                    </div>
                    <div class="card-body p-t-20">
                        <form action="{{ route('create-envelope') }}" id="user_envelope" method="POST">
                            {{ csrf_field() }}
                            <div class="row justify-content-left">
                                <div class="col-md-10">
                                    <div class="form-group" style="float:none;">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label>New Envelope Name</label>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="text" class="form-control" name="name" id="name" required>
                                            </div>
                                            <div class="col-md-1"></div>
                                            <div class="col-md-2">
                                                <label>Envelope Category</label>
                                            </div>
                                            <div class="col-md-3">
                                                <select name="category" id="category" class="form-control" required>
                                                    <option value="">Select a Category</option>
                                                    @foreach ($categories as $category)
                                                         @if($category->role=='main')
                                                           <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                         @else
                                                           <option value="{{ $category->id }}">&nbsp;&nbsp;&nbsp;&nbsp;{{ $category->name }}</option>
                                                         @endif
                                                      
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-complete">Add Envelope</button>
                                </div>
                            </div>
                            </form>
                            <hr>
                            <div class="widget-11-2-table p-t-20">
                                <table class="table table-hover table-condensed table-responsive" id="tableEnvelope">
                                    <thead>
                                        <tr>
                                            <th class="v-align-middle" style="width: 5%; text-align:center;">ID</th>
                                            <th class="v-align-middle" style="width: 15%; text-align:center;">Envelope Name</th>
                                            <th class="v-align-middle" style="width: 20%; text-align:center;">Envelope Category</th>
                                            <th class="v-align-middle" style="width: 20%; text-align:center;">Envelope Date</th>
                                            <th class="v-align-middle" style="width: 20%; text-align:center;">Envelope Amount</th>
                                            <th class="v-align-middle" style="width: 20%; text-align:center;">User</th>
                                            <th class="v-align-middle" style="width: 30%; text-align:center;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($envelopes as $envelope)
                                            <tr>
                                                <td class="v-align-middle" style="text-align:center;">{{ $envelope->id }}</td>
                                                <td class="v-align-middle" style="text-align:center;">{{ $envelope->name }}</td>
                                                <?php
                                                $categoryName = $envelope->getCategoryName($envelope->category_id);
                                                $categoryName = isset($categoryName['name'])
                                                ? $categoryName['name']
                                                : 'No
                                                Category';
                                                ?>
                                                <td class="v-align-middle" style="text-align:center;">{{ $categoryName }}
                                                </td>
                                              <?php
                                                $date = strtotime($envelope->envelope_date)
                                              ?>
                                                <td class="v-align-middle" style="text-align:center;">{{ date("m-d-Y", $date) }}</td>
                                                <?php $amount = $envelope->EnvelopAmount($envelope->id); ?>
                                                <td class="v-align-middle" style="text-align:center;">${{ $amount }}
                                                </td>
                                                <?php
                                                $userName = $envelope->getUserById($envelope->enveloped_by);
                                                $userName = isset($userName['name'])
                                                ? $userName['name']
                                                : 'No
                                                Category';
                                                ?>

                                                <td class="v-align-middle" style="text-align:center;">
                                                    <a href="{{route('admin.users.show', ['user' => $envelope->enveloped_by])}}"><u>{{ $userName }}</u></a>
                                                </td>
                                                <td class="v-align-middle">
                                                    <div class="btn-group">
                                                        <a class="btn btn-primary"
                                                            href="{{ url('admin/envelopes/AddReceipts/' . $envelope->id) }}"
                                                            id="sessionSave" data-toggle="tooltip" data-placement="bottom"
                                                            title="Add reciept" name="add"><i class="fa fa-plus"
                                                                aria-hidden="true"></i></a>
                                                        <a href="{{ url('admin/envelopes/preview') . '/' . $envelope->id }}"
                                                            class="btn btn-complete" data-toggle="tooltip"
                                                            data-placement="bottom" title="Edit"><i class="fa fa-edit"></i>
                                                        </a>
                                                        <a href="{{ url('admin/envelopes/delete/' . $envelope->id) }}"
                                                            class="btn btn-danger"
                                                            onclick="return confirm('Are you sure you want to remove envelope Ref#{{ $envelope->id }} ?')"
                                                            data-toggle="tooltip" data-placement="bottom" title="Delete"><i
                                                                class="fa fa-trash-o"></i></a>
                                                         @if ( $amount != 0)
                                                            <a class="btn btn-primary"
                                                                href="#"
                                                                data-toggle="tooltip" data-placement="bottom"
                                                               onclick="printPage('<?=url('admin/envelopes/print') . '/' . $envelope->id?>');" title="Print"><i class="fa fa-print"></i></a>
                                                        @else
                                                            <a class="btn btn-primary" href="#!" data-toggle="tooltip"
                                                                data-placement="bottom" title="Email"
                                                                onclick="return confirm('No Transactions')"><i
                                                                    class="fa fa-print"></i></a>
                                                        @endif
                                                        <a href="#" onclick="modalSend(<?= $envelope->id ?>)"  class="btn btn-complete" data-toggle="modal" data-target="#send"
                            data-placement="bottom" title="Email"><i class="fa fa-envelope" style="color: #FFF;"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- END card -->
                </div>
                <!-- END CONTAINER FLUID -->
            </div>

            <!-- END COPYRIGHT -->
        </div>
        <!-- END PAGE CONTENT WRAPPER -->
       
     <div id="send" class="modal fade" role="dialog" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog">
                <!-- Modal content--> 
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h3 class="modal-title"><i class="fa fa-envelope"></i> &nbsp;<strong>{{ __('Send Envelope Email') }}</strong></h3>
                    </div>
                  <form method="post" action="{{ route('envelopes.notify') }}">
                    {{ csrf_field() }}
                    {{ method_field('POST') }}
                    <div class="modal-body"> 
                        <br>
                        <p>{{ __('Are sure you want to send email envelope ?') }}</p>
                        <input type="text" placeholder="email@domain.com" class="form-control" name="send_email" id="send_email">
                        <input type="hidden" name="envelope_id" id="envelope_id" >
                        <div id="successMessage" style="display:none;" class="alert alert-success" role="alert"> Invoice successfully sent.</div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger" >{{ __('Send') }}</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    </div>
                   </form>

                </div>
                </div>
            </div>
        </div>
        <!-- END PAGE CONTAINER -->
@endsection

@section('script')
        <!-- BEGIN VENDOR JS -->

@endsection

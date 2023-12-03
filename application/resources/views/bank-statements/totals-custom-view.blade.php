<table id="thisPageStats" class="table transactionTotal table-bordered">
    <tr>
        <td><h4>This Page</h4></td>
        <td>
            <h4 class="text-danger">Debits</h4>
            <h6>{{$stats['this_page']['debits']}} items
                | {{\App\Helpers\Helper::printAmount($stats['this_page']['debits_total'])}}</h6>
        </td>
        <td>
            <h4 class="text-success">Credits</h4>
            <h6>{{$stats['this_page']['credits']}} items
                | {{\App\Helpers\Helper::printAmount($stats['this_page']['credits_total'])}}</h6>
        </td>
    </tr>
</table>


<table id="totalPageStats" class="table transactionTotal table-bordered">
    <tr>
        <td><h4>Grand Total</h4></td>
        <td>
            <h4 class="text-danger">Debits</h4>
            <h6>{{$stats['total']['debits']}} items
                | {{\App\Helpers\Helper::printAmount($stats['total']['debits_total'])}}</h6>
        </td>
        <td>
            <h4 class="text-success">Credits</h4>
            <h6>{{$stats['total']['credits']}} items
                | {{\App\Helpers\Helper::printAmount($stats['total']['credits_total'])}}</h6>
        </td>
    </tr>
</table>

<div class="table-footers">
    <div class="btns">
        <a href="javascript:;" class="btn btn-info LwxSelectAll">Select All</a>
        <a href="javascript:;" class="btn btn-info LwxDeSelectAll">Deselect All</a>
        <a href="javascript:;" data-target-lwx="#bulkUpdateRecordsLwx" class="btn btn-primary LwxBulkTrigger">Bulk Update</a>
        <a href="javascript:;" class="btn btn-success LwxBulkTrigger">Export XLS</a>
        <a href="javascript:;" class="btn btn-success LwxBulkTrigger">Download PDF</a>
        <a href="javascript:;" class="btn btn-success LwxBulkTrigger">Print</a>
    </div>
</div>


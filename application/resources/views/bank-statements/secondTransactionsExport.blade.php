<style>
    .strikethroughCell{
        text-decoration: line-through !important;
    }
    .table-text-center th,
    .table-text-center td{
        text-align: center !important;
    }
</style>
<h1>Transactions Report</h1>
@php 
$first= App\Models\Transaction::orderBy('id', 'ASC')->first();
$last=App\Models\Transaction::orderBy('id', 'DESC')->first();

if($from!='' AND $to!=''){
    $start=$from;
    $end=$to;
}
else if($from!='' AND $to==''){
    $end=date('Y-m-d');
}
elseif($from=='' AND $to!=''){
    $start=date('Y-m-d', strtotime('first day of january this year'));
}
else {
    $start=$first->transaction_date;
    $end=$last->transaction_date;
}
@endphp
@php 
$totalList=[];
    foreach($transactions as $key=>$transaction)
    {
        $preMidTotal=0;
        $preTotal=0;
        foreach($transaction['data'] as $trans)
        {
            $preTotal=$preTotal+abs($trans['childtotal']);
        }
        $preMidTotal=abs($transaction['subTotal'])+$preTotal;
        $totalList[]=$preMidTotal;
    }

@endphp
<h3 style="border-top:1px solid #000;">Start: @if($from) {{date("m/d/Y", strtotime($from))}} @else {{date("m/d/Y", strtotime($start))}} @endif</h3>
<h3 style="border-buttom:1px solid #000;">Period End: @if($to) {{date("m/d/Y", strtotime($to))}} @else {{date("m/d/Y", strtotime($end))}} @endif</h3>
<table class="table-text-center">
    <tbody>
        <tr>
            <td>&nbsp;&nbsp;</td>
            <td>&nbsp;&nbsp;</td>
            <td>&nbsp;&nbsp;</td>
            <td>&nbsp;&nbsp;</td>
        </tr>
        <tr>
            <td><strong>Category</strong></td>
            <td><strong>QTY</strong></td>
            <td><strong>Amount</strong></td>
            <td><strong>Per Category</strong></td>
        </tr>
    @php 
    $grandTotal=0;
    @endphp
    @foreach($transactions as $key=>$transaction)
        @php 
        $subTotal=0;
        $childtotal=0;
        $k=0;
        @endphp

                <tr>
                    <td>{{ $transaction['sub_name']}}</td>
                    <td>{{$transaction['subQty']}}</td>
                    <td>{{ \App\Helpers\Helper::printAmountExport(abs($transaction['subTotal']))}}</td>
                    <td>{{\App\Helpers\Helper::printAmountExport(abs($totalList[$key]))}}</td>
                </tr>
                @php 
                $total=0;
                $midTotal=0;
                @endphp
                @foreach($transaction['data'] as $trans)
                    @php  
                    $k++;
                    $total=$total+abs($trans['childtotal']);
                    @endphp
                    <tr>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $trans['child_name']}}</td>
                    <td>{{$trans['childQty']}}</td>
                    <td>{{ \App\Helpers\Helper::printAmountExport(abs($trans['childtotal']))}}</td>
                    <td></td>
                    </tr> 
                @endforeach
                @php 
                $midTotal=abs($transaction['subTotal'])+$total; 
                $grandTotal=$grandTotal+$midTotal;
                @endphp
    @endforeach
    <tr>
        <td>&nbsp;&nbsp;</td>
        <td>&nbsp;&nbsp;</td>
        <td>&nbsp;&nbsp;</td>
        <td>&nbsp;&nbsp;</td>
    </tr>
    <tr>
        <td><strong>Total</strong></td>
        <td><strong>{{\App\Helpers\Helper::printAmountExport(abs($grandTotal))}}</strong></td>
        <td></td>
    </tr>
</tbody>
</table>




		
	


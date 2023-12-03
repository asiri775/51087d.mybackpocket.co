<h1>
    Transactions Report</h1>
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
    $start=$first->transaction_date;
}
else {
    $start=$first->transaction_date;
    $end=$last->transaction_date;
}
$sum = 0;
@endphp
<h3 style="border-top:1px solid #000;">Start: @if($from) {{date("m/d/Y", strtotime($from))}} @else {{date("m/d/Y", strtotime($start))}} @endif</h3>
<h3 style="border-buttom:1px solid #000;">Period End: @if($to) {{date("m/d/Y", strtotime($to))}} @else {{date("m/d/Y", strtotime($end))}} @endif</h3>
<h3></h3>
<table>
    <thead>
        <tr>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @php
        $sum = 0;
        @endphp
        @foreach ($transactions as $transaction)
            @php
                $total = 0;
                $both=0;
            @endphp
            @foreach ($transaction['data'] as $trans)
                @if (isset($trans['childtotal']) and $trans['childtotal'] != 0)
                    @php
                        $total = $total + abs($trans['childtotal']);
                    @endphp
                @endif
            @endforeach
            @php
                $sum = $sum + (abs($transaction['subTotal']) + $total);
                $both=abs($transaction['subTotal']) + $total;
            @endphp
           @if($both!=0)
            <tr>
                <td>{{ $transaction['sub_name'] }}</td>
                <td>{{ \App\Helpers\Helper::printAmount(abs($both)) }}</td>
            </tr>
           @endif
        @endforeach
    </tbody>
</table>
<h3 style="border-buttom:1px solid #000;">Total {{ \App\Helpers\Helper::printAmount(abs($sum)) }}</h3>

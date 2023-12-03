<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Transaction Date</th>
            <th>Account</th>
            <th>Category</th>
            <th>Statement ID</th>
            <th>Vendor</th>
            <th>Amount</th>
            <th>Account Type</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($transactions as $transaction)
        <tr>
        <td>{{$transaction->id}}</td>  
        <td>{{$transaction->transaction_date}}</td>  
        <td>{{$transaction->bank_account}}</td>
        <td>{{$transaction->name}}</td>  
        <td>{{$transaction->statement_id}}</td>  
        <td>{{$transaction->Vendor}}</td>    
        <td>{{$transaction->Amount}}</td>
        <td>EXPENSES</td>
        </tr>  
        @endforeach
    </tbody>
</table>


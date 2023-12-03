<div style="width:100%;">
    <div style="font-family:sans-serif; box-sizing: border-box;">
        <div
            style="width:100%; box-sizing: border-box; text-align:right; padding-bottom:15px; padding-top:20px; padding-right:10px; border-bottom:3px #808080 solid;">
            <img src="https://users.backpocket.ca/admin/logo.jpeg" width="190px" height="48px" style="">
        </div> 
        <div class="ctitle" align="center" style="font-size:30px;">
            <h5><strong>Report: User Envelopes</strong></h5>
        </div>
        <table>
            <tbody>
                <tr>
                    <td><strong>User ID # </strong> &nbsp;&nbsp;&nbsp;</td>
                    <td>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ $user->id }}</td>
                </tr>
                <tr>
                    <td><strong>User Name</strong>   &nbsp;&nbsp;&nbsp;</td>
                    <td>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{ $user->name }}</td>
                </tr>
               
            </tbody>
        </table>
        <div class="row" style="font-family:sans-serif; font-size:14px; margin-top:10px;">
            <table width="100%"  cellpadding="3" cellspacing="0" align="left" style="margin-top:30px; border:2px solid #000;">
                <tr>
                    <th  width="150" style="font-size:17px;text-align:center; background-color:#000000; color:#ffffff; border:1px solid #000;">
                        ID
                    </th>
                    <th width="60"
                        style="text-align:center;   border:1px solid #000; color:#ffffff; background-color:#000000;">Name
                    </th>
                    <th width="60"
                        style="text-align:center;   border:1px solid #000; color:#ffffff; background-color:#000000;">Category
                    </th>
                    <th width="60" style="text-align:right;  border:1px solid #000; color:#ffffff; background-color:#000000;">
                        Create Date
                    </th>
                    <th width="60" style="text-align:right;  border:1px solid #000; color:#ffffff; background-color:#000000;">
                        Amount
                    </th>
                </tr>
                @foreach ($envelopes as $envelope)
                    <tr>
                        <td style="text-align:left; border:1px solid #000;">{{ $envelope->id }}</td>
                        <td style="text-align:left; border:1px solid #000;">{{ $envelope->name }}</td>
                         <?php
                            $categoryName = $envelope->getCategoryName($envelope->category_id);
                            $categoryName = isset($categoryName['name'])
                            ? $categoryName['name']
                            : 'No
                            Category';
                          ?>
                        <td style="text-align:left; border:1px solid #000;">{{ $categoryName }}</td>
                         <?php $date = strtotime($envelope->envelope_date); ?>
                        <td style=" border:1px solid #000; vertical-align:middle;"><span
                                style="float:left;"></span><span style="float:right;">{{ date('m/d/Y', $date) }}</span>
                        </td>
                        <?php
                        $amount = $envelope->getEnvelopeAmountById($envelope->id);
                        $amount = isset($amount)
                        ? $amount
                        : 'No
                        Receipts';
                        ?>
                        <td style="text-align:left; border:1px solid #000;">${{ $amount }}</td>
                    </tr>
                @endforeach

            </table>
        </div>


    </div>
</div>

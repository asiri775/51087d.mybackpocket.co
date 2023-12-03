<?php
namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;

class Items extends Model
{
    public static function emailProductLIstItems($items)
    {
        $list = '';
        foreach ($items as $item) {
            $list .= '<table width="100%" cellspacing="0" cellpadding="0" border="0">
                        <tbody>
                        <tr>
                            <td style="background-color:rgb(66,70,81);padding-left:8px;padding-right:8px"
                                align="center">
                                <table style="max-width:632px;margin:0 auto" width="100%" cellspacing="0"
                                       cellpadding="0" border="0">
                                    <tbody>
                                    <tr>
                                        <td style="font-size:0;vertical-align:top;background-color:#ffffff;padding-left:16px;padding-right:16px;padding-top:16px"
                                            align="center"><div style="display:inline-block;vertical-align:top;width:100%;max-width:300px">
                    <div style="font-family:Helvetica,Arial,sans-serif;margin-top:0px;margin-bottom:0px;font-size:14px;line-height:21px;text-align:left;padding-left:8px;padding-right:8px">
                    <p style="line-height:24px;margin-top:0px;margin-bottom:0px">
                    <span style="color:rgb(66,70,81)"><span style="font-size:16px;line-height:24px;margin-top:0px;margin-bottom:0px">
                    <b>' . $item->product->name . '</b></span></span><br></p>
                    <p style="margin-top:0px;margin-bottom:0px"><span style="color:rgb(130,137,154);margin-top:0px;margin-bottom:0px">' . $item->product->description . ' </span><br></p></div></div><div style="display:inline-block;vertical-align:top;width:100%;max-width:100px">
                                                <div style="font-family:Helvetica,Arial,sans-serif;margin-top:0px;margin-bottom:0px;font-size:16px;line-height:24px;text-align:center;padding-left:8px;padding-right:8px">
                                                    <p style="margin-top:0px;margin-bottom:0px"><span
                                                            style="color:rgb(66,70,81);margin-top:0px;margin-bottom:0px"><span
                                                            style="font-size:0px;display:none;max-height:0px;width:0px;line-height:0;overflow:hidden"></span>' . $item->product->quantity . ' </span><br>
                                                    </p></div>
                                            </div>
                                            <div style="display:inline-block;vertical-align:top;width:100%;max-width:100px">
                                                <div style="font-family:Helvetica,Arial,sans-serif;margin-top:0px;margin-bottom:0px;font-size:16px;line-height:24px;text-align:right;padding-left:8px;padding-right:8px">
                                                    <p style="margin-top:0px;margin-bottom:0px"><span
                                                            style="color:rgb(66,70,81);margin-top:0px;margin-bottom:0px"><span
                                                            style="font-size:0px;display:none;max-height:0px;width:0px;line-height:0;overflow:hidden"></span> $' . $item->product->price . ' </span><br>
                                                    </p></div>
                                            </div><div style="padding-left:8px;padding-right:8px">
                                                <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                                    <tbody>
                                                    <tr>
                                                        <td style="font-size:16px;line-height:16px;height:16px;vertical-align:top;border-bottom:1px solid #d3dce0">
                                                            &nbsp;<br></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        </tbody>
                    </table>';
        }

        return $list;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calculate extends Model
{
    public static function getTotalAmount($id)
    {
        $transactions = Transaction::where('user_id', $id)->get();
        $amount = 0;
        foreach ($transactions as $t) {
            $total = number_format((float)$t->total, 2, '.', '');
            $amount = $amount + $total;
        }

        return $amount;
    }


    public static function dateConvertion($date)
    {
        $d = explode('-', $date);
        $date = $d[2] . '-' . $d[0] . '-' . $d[1];
        return $date;
    }


    public static function vendorAmount($id, $user)
    {
        $transactions = Transaction::where('vendor_id', $id)->where('user_id', $user)->get();
        $amount = 0;
        foreach ($transactions as $t) {
            $total = number_format((float)$t->total, 2, '.', '');
            $amount = $amount + $total;
        }
        return $amount;
    }

    public static function countReceipts($id, $user)
    {
        $transactions = Transaction::where('vendor_id', $id)->where('user_id', $user)->count();

        return $transactions;
    }

    public static function vendorCount($id)
    {
        $count = Transaction::where('vendor_id', $id)->count();
        return $count;
    }
}

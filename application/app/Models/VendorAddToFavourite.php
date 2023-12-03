<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorAddToFavourite extends Model
{
    protected $table = 'vendor_favourites';
    protected $fillable = ['id', 'vendor_id', 'user_id'];


    public function getVendor($id)
    {
        $vendor = Vendor::where('id', $id)->first()->name;
        return $vendor;
    }

    public function transactionCount($id)
    {
        $transactions = Transaction::where('vendor_id', $id)->get();
        $count = count($transactions);

        return $count;
    }
    public function transactionAmount($id)
    {
        $transactions = Transaction::where('vendor_id', $id)->get();
        $amount = 0;
        foreach ($transactions as $transaction) {
            $amount = $amount + $transaction->total;;
        }

        
        return number_format((float)$amount, 2, '.', '');
    }
}

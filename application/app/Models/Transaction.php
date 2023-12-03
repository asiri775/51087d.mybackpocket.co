<?php

namespace App\Models;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Items;

class Transaction extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function vendor(){
        return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
    }

    public function purchase(){
        return $this->hasMany(Purchase::class);
    }

    public static function emailProductLIst($id)
    {
        $items = Purchase::where('transaction_id', $id)->get();
       return Items::emailProductLIstItems($items);
    }

    public function getAmount($ids)
    {
        if(is_null($ids)){
            return $ids ;
        }
        else{
            $transaction = Transaction::find($ids);
//            if($transaction->PAYMENT_STATUS==3){
//                return $transaction->PRE_PAID;
//            }
            $total = number_format((float)$transaction->total, 2, '.', '');
            return $total;
        }

    }

    public static function printName($pri_name) {
        $printname = Vendor::select('name')->find($pri_name);
        return $printname;
    }

    public static function getTranscationsListByvendorId($vendor_id)
    {
        $transactions=Transaction::where('vendor_id',$vendor_id)->get();
        foreach ($transactions AS $transaction)
        {
            $trans[]=$transaction['id'];
        }

        return $transactions;
    }


}

<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\Vendor;
use App\User;

class Envelope extends Model
{

    protected $table = "envelopes";
    //    protected $fillable = ['envelope_status'];

    public function vendor(){
        return $this->belongsTo(Vendor::class);
    }


    public function transaction()
    {
        return $this->belongsTo('App\Models\Transaction', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    public function user()
    {
        return $this->hasOne('App\User', 'id', 'enveloped_by');
    }

    public function transactionCount($id)
    {
        $count = Transaction::where('envelope_id', $id)->count();
        return $count;
    }

    public function getCategoryName($cat_id)
    {
        $categoryName = Category::select('name')->where('id', $cat_id)->first();
        return $categoryName;
    }

    public function getUserById($id)
    {

        $userName = User::select('name')->where('id', $id)->first();
        return $userName;
    }


    public function EnvelopAmount($id)
    {
        $envTrans = EnvelopeTransaction::where('envelope_id', $id)->get();
        $amount =   0.00;
        foreach($envTrans as $eTran)
        {
            $transaction = Transaction::where('id', $eTran->transaction_id)->first();
            if($transaction)
            {
                $total  =   $transaction->total;
                $amount +=   $total;
            }else {
                $amount = 0;
            }
        }
        return $amount;
    }

    public function envCount($id)
    {
        $envTrans = EnvelopeTransaction::where('envelope_id', $id)->get();
        $count =   0;
        foreach($envTrans as $eTran)
        {
            $transaction = Transaction::where('id', $eTran->transaction_id)->first();
            if($transaction)
            {
                $count = $count + 1 ;
            }else {
                $count = 0;
            }
        }
        return $count;
    }


    public function getEnvelopeAmountById($ids)
    {
        $transactions = Transaction::where('envelope_id', $ids)->get();
        if ($transactions) {
            $amount =   0.00;
            foreach ($transactions as $transaction) {
                $total  =   $transaction->total;
                $amount +=   $total;
            }
        } else {
            $amount = 'No Receipts';
        }
        return  number_format((float)($amount), 2, '.', ',');
    }


    public static function getGrandTotal($userId)
    {
        $total = 0.00;
        $envelopes = Envelope::where('enveloped_by', $userId)->get();
        foreach ($envelopes as $envelope) {
            $total  =   $total + $envelope->getEnvelopeAmountById($envelope->id);
        }


        return number_format((float)($total), 2, '.', ',');
    }

    public function getTransactions($id)
    {
        $envTrans = EnvelopeTransaction::where('envelope_id', $id)->get();
      
        foreach($envTrans as $eTran)
        {
            $transactions[] = Transaction::where('id', $eTran->transaction_id)->first();
        }
        return $transactions;
    }

  
}

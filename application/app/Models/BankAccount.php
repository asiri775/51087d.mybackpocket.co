<?php

namespace App\Models;

use App\Models\Envelope;
use App\Models\Budget;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $table = "bank_accounts";
    protected $guarded = [];

    protected $fillable = ['name'];

    public function displayName(){
        $lastFourAccountNo = substr($this->account_number, -4);
        $name = trim($this->alias);
        if($name==null){
            $name = trim($this->name);
        }
        return $name." - ".$lastFourAccountNo;
    }

    public function bank()
    {
        return $this->hasOne(Bank::class, 'id', 'bank_id');
    }

}

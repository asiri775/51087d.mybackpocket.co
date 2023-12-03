<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnvelopeTransaction extends Model
{
    protected $table = 'envelope_transactions';
    protected $fillable = ['id','envelope_id','transaction_id'];
}

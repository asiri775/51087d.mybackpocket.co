<?php

namespace App\Models;

use App\Models\Envelope;
use App\Models\Budget;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $table = "banks";
    protected $guarded = [];

    protected $fillable = ['name'];
}

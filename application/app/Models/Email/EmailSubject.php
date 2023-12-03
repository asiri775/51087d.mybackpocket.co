<?php

namespace App\Models\Email;

use Illuminate\Database\Eloquent\Model;

class EmailSubject extends Model
{
    protected $connection = 'mailserver';
    protected $table = 'email-subjects';
}

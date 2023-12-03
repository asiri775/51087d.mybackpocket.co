<?php

namespace App\Models\Email;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $connection = 'mailserver';
    protected $table = 'email-contents';
}

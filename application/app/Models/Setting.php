<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = [
        'user_id',
        'IMAP_HOST',
        'IMAP_PORT',
        'IMAP_ENCRYPTION',
        'IMAP_VALIDATE_CERT',
        'IMAP_USERNAME',
        'IMAP_PASSWORD',
        'IMAP_DEFAULT_ACCOUNT',
        'IMAP_PROTOCOL',
        'MAIL_DRIVER',
        'MAIL_HOST',
        'MAIL_PORT',
        'MAIL_USERNAME',
        'MAIL_PASSWORD',
        'MAIL_ENCRYPTION',
        'phone',
        'address',
    ];
}

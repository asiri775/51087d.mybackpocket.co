<?php

namespace App;

use App\Models\Envelope;    
use App\Models\Vendor;  
use App\Models\Role;    
use App\Models\Transaction;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function vendors()
    {
        return $this->belongsToMany(
            Vendor::class,
            'transactions'
        );
    }
    public function roles(){
        return $this->belongsTo(Role::class, 'roles');
    }

    public function getRoleName($id){
        $role = Role::where('id', $id)->first()->name;
        return $role;
    }

    public function NumberOfTransacations($id){
        $transactions = Transaction::where('user_id', $id)->count();
        return $transactions;
    }
    public function NumberOfEnvelopes($id){
        $envelopes = Envelope::where('enveloped_by', $id)->count();
        return $envelopes;
    }

}

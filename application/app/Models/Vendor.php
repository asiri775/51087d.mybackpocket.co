<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Vendor extends Model
{
    protected $guarded = [];
    protected $casts = ['created_at' => 'date:m-d-Y'];
    // protected $appends = ['short_address'];
    protected $fillable = ['id', 'name', 'email', 'logo', 'address', 'street_name', 'unit', 'city', 'state', 'zip_code', 'store_no', 'phone', 'HST', 'QST'];

    public function firstUpdateOrCreate($where, $fields)
    {
        $vendor = Vendor::firstOrCreate(
            $where,
            $fields
        );
        if (!empty($vendor)) {

            if (trim($vendor->address) == null) {
                if (is_array($fields) && array_key_exists('address', $fields)) {
                    $fieldAddr = trim($fields['address']);
                    if ($fieldAddr != null) {
                        $vendor->address = $fieldAddr;
                    }
                }
            }

            if(trim($vendor->state) == null){
                if (is_array($fields) && array_key_exists('state', $fields)) {
                    $fieldState = trim($fields['state']);
                    if ($fieldState != null) {
                        $vendor->state = $fieldState;
                    }
                }
            }

            if(trim($vendor->city) == null){
                if (is_array($fields) && array_key_exists('city', $fields)) {
                    $fieldCity = trim($fields['city']);
                    if ($fieldCity != null) {
                        $vendor->city = $fieldCity;
                    }
                }
            }

            if(trim($vendor->street_name) == null){
                if (is_array($fields) && array_key_exists('street_name', $fields)) {
                    $fieldStreetName = trim($fields['street_name']);
                    if ($fieldStreetName != null) {
                        $vendor->street_name = $fieldStreetName;
                    }
                }
            }

            if(trim($vendor->zip_code) == null){
                if (is_array($fields) && array_key_exists('zip_code', $fields)) {
                    $fieldZipCode = trim($fields['zip_code']);
                    if ($fieldZipCode != null) {
                        $vendor->zip_code = $fieldZipCode;
                    }
                }
            }

            if(trim($vendor->zip_code) == null){
                if (is_array($fields) && array_key_exists('zip_code', $fields)) {
                    $fieldZipCode = trim($fields['zip_code']);
                    if ($fieldZipCode != null) {
                        $vendor->zip_code = $fieldZipCode;
                    }
                }
            }

            $vendor->update();
        }
        return $vendor;
    }

    public function getLogoAttribute()
    {
        return str_replace(' ', '', Str::lower($this->name));
    }

    public function getShortAddressAttribute()
    {
        return Str::limit($this->address, 50);
    }

    public function printAmount($vendor_id)
    {
        $amounts = Transaction::select('total')->where('vendor_id', $vendor_id)->sum('total');
        return $amounts;
    }

    public function countTransaction($vendor_id)
    {
        $count = Transaction::where('vendor_id', $vendor_id)->count();
        return $count;
    }
}

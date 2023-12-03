<?php

namespace App\Http\Requests;

use App\Models\Vendor;
use Illuminate\Foundation\Http\FormRequest;

class EditVendorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
        ];
    }

    public function editVendor($id)
    {
        $vendor = Vendor::where('id', $id)->first();
        if ($file = $this->file('logo')) {
            $logoName = rand() . '.' . $this->file('logo')->getClientOriginalExtension();
            $file->move('admin/assets/img/vendor-logos', $logoName);
        } else {
            $logoName = '';
        }

        $vendor->name = $this->name;
        $vendor->email = $this->email;
        if ($logoName) {
            $vendor->logo_vendor = $logoName;
        }
        $vendor->address = $this->mapAddress;
        $vendor->street_name = $this->street_number . " " . $this->route;
        $vendor->city = $this->locality;
        $vendor->state = $this->state;
        $vendor->zip_code = $this->postal_code;
        $vendor->store_no = $this->store_no;
        $vendor->phone = $this->phone;
        $vendor->HST = $this->hst;
        $vendor->QST = $this->qst;
        $vendor->update();
    }
}

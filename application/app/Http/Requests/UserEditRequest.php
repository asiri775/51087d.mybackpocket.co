<?php

namespace App\Http\Requests;

use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class UserEditRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'role' => 'required',
        ];
    }

    public function editUser($user)
    {
        $user = User::where('id', $user)->first();
        $user->name = $this->name;
        $user->first_name = $this->first_name;
        $user->last_name = $this->last_name;
        $user->mobile = $this->mobile;
        $user->email = $this->email;
        $user->recovery_email = $this->recovery_email;
        $user->password = Hash::make($this->password);
        $user->role_id = $this->role;
        $user->update();
    }
}

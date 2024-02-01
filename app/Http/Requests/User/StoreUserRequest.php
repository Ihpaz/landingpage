<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'fullname' => 'required',
            'nickname' => 'required',
            'email' => 'required',
            'thumbnail_photo' => 'image',
            'password' => 'required|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[@$!%*#?&]/',
            'phonenumber' => 'required',
            'company' => 'nullable',
            'department' => 'nullable',
            'position' => 'nullable',
            'nip' => 'nullable',
            'nik' => 'nullable',
            'pernr' => 'nullable',
            'status' => 'required',
        ];
    }
}

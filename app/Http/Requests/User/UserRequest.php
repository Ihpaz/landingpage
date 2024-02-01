<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'fullname' => 'nullable',
            'email' => 'email',
            'role' => 'nullable',
            'nip' => 'nullable',
            'email' => 'nullable',
            'company' => 'nullable',
            'department' => 'nullable',
            'title' => 'nullable',
            'status' => 'nullable',
            'roles' => 'nullable'
        ];
    }
}

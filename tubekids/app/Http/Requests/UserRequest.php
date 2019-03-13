<?php

namespace App\Http\Requests;

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
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'lastname' => 'required',
            'birthdate' => 'required|date_format:d/m/Y',
            'contry' => 'alpha|nullable',
            'phone' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string',
            'confirm_password' => 'same:password',
        ];
    }
}

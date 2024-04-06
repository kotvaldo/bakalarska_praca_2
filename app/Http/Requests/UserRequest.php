<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->method == 'PUT') {
            return [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6|confirmed'
            ];
        } else {
            return [
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:6|confirmed'
            ];
        }
    }

//    public function messages()
//    {
//        return [
//          'name.required' => 'This field cannot be empty!'
//        ];
//    }
}

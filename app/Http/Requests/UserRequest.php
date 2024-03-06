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
        if ($this->isMethod('put')) {
            return [
                'username' => 'required|string|unique:users,username,'.$this->user_id.',user_id',
                'email' => 'required|email|unique:users,email,'.$this->user_id.',user_id',
                'phone_number' => 'required|string',
                'status' => 'required|string',
                'roles' => 'required|array',
            ];
        } else {
            return [
                'username' => 'required|string|unique:users,username',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'phone_number' => 'required|string',
                'status' => 'required|string',
                'roles' => 'required|array',
            ];
        }
    }
}

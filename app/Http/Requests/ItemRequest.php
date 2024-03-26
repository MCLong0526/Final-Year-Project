<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemRequest extends FormRequest
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
                'name' => 'required|string',
                'description' => 'required|string',
                'condition' => 'required|string',
                'type' => 'required|string',
                'price' => 'required|numeric',
                'quantity' => 'required|integer',
                'images' => 'required|array',

            ];
        }

        return [
            'user_id' => 'required|integer',
            'name' => 'required|string',
            'description' => 'required|string',
            'condition' => 'required|string',
            'type' => 'required|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'images' => 'required|array',
        ];
    }
}

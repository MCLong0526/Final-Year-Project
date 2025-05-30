<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderServiceRequest extends FormRequest
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
        return [
            'service_id' => 'required|exists:services,service_id',
            'status' => 'required|string',
            'place_to_service' => 'required|string',
            'approximated_price' => 'required|numeric',
            'remark_customer' => 'nullable|string',

        ];
    }
}

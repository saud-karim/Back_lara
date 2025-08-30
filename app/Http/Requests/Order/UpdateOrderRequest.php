<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
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
            'shipping_address' => 'sometimes|string|max:1000',
            'payment_method' => 'sometimes|in:card,cod,installment',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'shipping_address.string' => 'Shipping address must be a string.',
            'shipping_address.max' => 'Shipping address cannot exceed 1000 characters.',
            'payment_method.in' => 'Payment method must be card, cod, or installment.',
        ];
    }
} 
<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'category_id' => 'sometimes|exists:categories,id',
            'supplier_id' => 'sometimes|exists:suppliers,id',
            'stock' => 'sometimes|integer|min:0',
            'images' => 'nullable|array',
            'images.*' => 'string|url',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.string' => 'Product name must be a string.',
            'name.max' => 'Product name cannot exceed 255 characters.',
            'price.numeric' => 'Price must be a number.',
            'price.min' => 'Price must be greater than or equal to 0.',
            'category_id.exists' => 'Selected category does not exist.',
            'supplier_id.exists' => 'Selected supplier does not exist.',
            'stock.integer' => 'Stock must be a whole number.',
            'stock.min' => 'Stock must be greater than or equal to 0.',
        ];
    }
} 
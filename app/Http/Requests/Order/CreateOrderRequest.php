<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
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
            // Shipping address as JSON object
            'shipping_address' => 'required|array',
            'shipping_address.name' => 'required|string|max:255',
            'shipping_address.phone' => 'required|string|max:20',
            'shipping_address.governorate' => 'required|string|max:100',
            'shipping_address.city' => 'required|string|max:100',
            'shipping_address.district' => 'nullable|string|max:100',
            'shipping_address.street' => 'required|string|max:255',
            'shipping_address.building_number' => 'nullable|string|max:50',
            'shipping_address.floor' => 'nullable|string|max:50',
            'shipping_address.apartment' => 'nullable|string|max:50',
            'shipping_address.postal_code' => 'nullable|string|max:20',
            
            // Payment method - match database enum values
            'payment_method' => 'required|in:credit_card,debit_card,paypal,bank_transfer,cash_on_delivery',
            
            // Order items
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.variant_id' => 'nullable|integer',
            'items.*.price' => 'nullable|numeric|min:0',
            
            // Optional fields
            'notes' => 'nullable|string|max:1000',
            'coupon_code' => 'nullable|string|max:50',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            // Shipping address messages
            'shipping_address.required' => 'عنوان الشحن مطلوب.',
            'shipping_address.array' => 'عنوان الشحن يجب أن يكون بيانات كاملة.',
            'shipping_address.name.required' => 'الاسم في عنوان الشحن مطلوب.',
            'shipping_address.phone.required' => 'رقم الهاتف مطلوب.',
            'shipping_address.governorate.required' => 'المحافظة مطلوبة.',
            'shipping_address.city.required' => 'المدينة مطلوبة.',
            'shipping_address.street.required' => 'الشارع مطلوب.',
            
            // Payment method messages
            'payment_method.required' => 'طريقة الدفع مطلوبة.',
            'payment_method.in' => 'طريقة الدفع يجب أن تكون: بطاقة ائتمان، بطاقة خصم، باي بال، تحويل بنكي، أو الدفع عند الاستلام.',
            
            // Items messages
            'items.required' => 'يجب اختيار منتج واحد على الأقل.',
            'items.array' => 'بيانات المنتجات غير صحيحة.',
            'items.min' => 'يجب اختيار منتج واحد على الأقل.',
            'items.*.product_id.required' => 'معرف المنتج مطلوب.',
            'items.*.product_id.exists' => 'المنتج المحدد غير موجود.',
            'items.*.quantity.required' => 'الكمية مطلوبة.',
            'items.*.quantity.integer' => 'الكمية يجب أن تكون رقم صحيح.',
            'items.*.quantity.min' => 'الكمية يجب أن تكون 1 على الأقل.',
        ];
    }
} 
<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SendToShippingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Authorization handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'order_ids' => 'required|array|min:1|max:100',
            'order_ids.*' => 'required|integer|exists:orders,id',
            'shipping_company' => 'required|string|max:100',
            'field_mapping' => 'nullable|array',
            'field_mapping.*.id' => 'required_with:field_mapping|string',
            'field_mapping.*.field_path' => 'required_with:field_mapping|string',
            'field_mapping.*.enabled' => 'required_with:field_mapping|boolean',
            'custom_api_url' => 'required|url|max:500',
            'custom_api_key' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'order_ids.required' => 'يجب تحديد طلب واحد على الأقل',
            'order_ids.array' => 'يجب أن تكون الطلبات عبارة عن مصفوفة',
            'order_ids.min' => 'يجب تحديد طلب واحد على الأقل',
            'order_ids.max' => 'لا يمكن معالجة أكثر من 100 طلب في المرة الواحدة',
            'order_ids.*.integer' => 'معرف الطلب يجب أن يكون رقماً',
            'order_ids.*.exists' => 'الطلب غير موجود',
            'shipping_company.required' => 'يجب تحديد اسم شركة الشحن',
            'shipping_company.string' => 'اسم شركة الشحن يجب أن يكون نصاً',
            'custom_api_url.required' => 'يجب إدخال رابط API لشركة الشحن',
            'custom_api_url.url' => 'رابط API غير صالح',
            'custom_api_key.string' => 'مفتاح API يجب أن يكون نصاً',
        ];
    }
}

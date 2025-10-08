<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization is handled by middleware (auth:sanctum + role:admin)
        // So we just return true here
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
            'status' => [
                'required',
                'string',
                'in:pending,confirmed,processing,shipped,delivered,cancelled,refunded'
            ],
            'notes' => 'nullable|string|max:1000',
            'tracking_number' => 'nullable|string|max:100',
            'estimated_delivery' => 'nullable|date|after:today',
            'notify_customer' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'status.required' => 'حالة الطلب مطلوبة',
            'status.in' => 'حالة الطلب غير صحيحة',
            'notes.max' => 'الملاحظات يجب ألا تزيد عن 1000 حرف',
            'tracking_number.max' => 'رقم التتبع يجب ألا يزيد عن 100 حرف',
            'estimated_delivery.date' => 'تاريخ التسليم المتوقع غير صحيح',
            'estimated_delivery.after' => 'تاريخ التسليم المتوقع يجب أن يكون في المستقبل',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'status' => 'حالة الطلب',
            'notes' => 'الملاحظات',
            'tracking_number' => 'رقم التتبع',
            'estimated_delivery' => 'تاريخ التسليم المتوقع',
            'notify_customer' => 'إشعار العميل',
        ];
    }
}

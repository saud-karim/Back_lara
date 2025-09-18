<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactInfoResource;
use App\Models\ContactInfo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactInfoController extends Controller
{
    /**
     * Get contact information
     */
    public function index(): JsonResponse
    {
        try {
            $contactInfo = ContactInfo::getInstance();
            
            return response()->json([
                'success' => true,
                'data' => new ContactInfoResource($contactInfo)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في جلب معلومات الاتصال',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update contact information
     */
    public function update(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'main_phone' => 'nullable|string|max:20',
                'secondary_phone' => 'nullable|string|max:20',
                'toll_free' => 'nullable|string|max:100',
                'main_email' => 'nullable|email|max:100',
                'sales_email' => 'nullable|email|max:100',
                'support_email' => 'nullable|email|max:100',
                'address_street' => 'nullable|string|max:255',
                'address_district' => 'nullable|string|max:100',
                'address_city' => 'nullable|string|max:100',
                'address_country' => 'nullable|string|max:100',
                'working_hours_weekdays' => 'nullable|string|max:255',
                'working_hours_friday' => 'nullable|string|max:255',
                'working_hours_saturday' => 'nullable|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'بيانات غير صحيحة',
                    'errors' => $validator->errors()
                ], 422);
            }

            $contactInfo = ContactInfo::getInstance();
            $contactInfo->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث معلومات الاتصال بنجاح',
                'data' => new ContactInfoResource($contactInfo->fresh())
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في تحديث معلومات الاتصال',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

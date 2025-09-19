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
                // Phone and Email fields (no multilingual needed)
                'main_phone' => 'nullable|string|max:20',
                'secondary_phone' => 'nullable|string|max:20',
                'toll_free' => 'nullable|string|max:100',
                'main_email' => 'nullable|email|max:100',
                'sales_email' => 'nullable|email|max:100',
                'support_email' => 'nullable|email|max:100',
                'whatsapp' => 'nullable|string|max:20',
                
                // Multilingual Address fields
                'address_street_ar' => 'nullable|string|max:255',
                'address_street_en' => 'nullable|string|max:255',
                'address_district_ar' => 'nullable|string|max:100',
                'address_district_en' => 'nullable|string|max:100',
                'address_city_ar' => 'nullable|string|max:100',
                'address_city_en' => 'nullable|string|max:100',
                'address_country_ar' => 'nullable|string|max:100',
                'address_country_en' => 'nullable|string|max:100',
                
                // Multilingual Working Hours fields
                'working_hours_weekdays_ar' => 'nullable|string|max:255',
                'working_hours_weekdays_en' => 'nullable|string|max:255',
                'working_hours_friday_ar' => 'nullable|string|max:255',
                'working_hours_friday_en' => 'nullable|string|max:255',
                'working_hours_saturday_ar' => 'nullable|string|max:255',
                'working_hours_saturday_en' => 'nullable|string|max:255',
                
                // Multilingual Labels
                'emergency_phone_label_ar' => 'nullable|string|max:100',
                'emergency_phone_label_en' => 'nullable|string|max:100',
                'toll_free_label_ar' => 'nullable|string|max:100',
                'toll_free_label_en' => 'nullable|string|max:100',
                
                // Legacy fields for backward compatibility (deprecated)
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

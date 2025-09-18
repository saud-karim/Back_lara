<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyInfoResource;
use App\Models\CompanyInfo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyInfoController extends Controller
{
    /**
     * Get company information
     */
    public function index(): JsonResponse
    {
        try {
            $companyInfo = CompanyInfo::getInstance();
            
            return response()->json([
                'success' => true,
                'data' => new CompanyInfoResource($companyInfo)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في جلب معلومات الشركة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update company information
     */
    public function update(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                // Multilingual fields only (no duplicates)
                'company_name_ar' => 'required|string|max:255',
                'company_name_en' => 'required|string|max:255',
                'company_description_ar' => 'nullable|string|max:1000',
                'company_description_en' => 'nullable|string|max:1000',
                'mission_ar' => 'nullable|string|max:500',
                'mission_en' => 'nullable|string|max:500',
                'vision_ar' => 'nullable|string|max:500',
                'vision_en' => 'nullable|string|max:500',
                // Other fields
                'logo_text' => 'nullable|string|max:10',
                'founded_year' => 'nullable|string|max:10',
                'employees_count' => 'nullable|string|max:20'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'بيانات غير صحيحة',
                    'errors' => $validator->errors()
                ], 422);
            }

            $companyInfo = CompanyInfo::getInstance();
            $companyInfo->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث معلومات الشركة بنجاح',
                'data' => new CompanyInfoResource($companyInfo->fresh())
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في تحديث معلومات الشركة',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

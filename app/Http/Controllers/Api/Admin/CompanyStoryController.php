<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyStoryResource;
use App\Models\CompanyStory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyStoryController extends Controller
{
    /**
     * Get company story
     */
    public function index(): JsonResponse
    {
        try {
            $companyStory = CompanyStory::getInstance();
            
            return response()->json([
                'success' => true,
                'data' => new CompanyStoryResource($companyStory)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في جلب قصة الشركة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update company story
     */
    public function update(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'paragraph1_ar' => 'nullable|string',
                'paragraph1_en' => 'nullable|string',
                'paragraph2_ar' => 'nullable|string',
                'paragraph2_en' => 'nullable|string',
                'paragraph3_ar' => 'nullable|string',
                'paragraph3_en' => 'nullable|string',
                'features' => 'nullable|array',
                'features.*.name_ar' => 'required|string|max:255',
                'features.*.name_en' => 'required|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'بيانات غير صحيحة',
                    'errors' => $validator->errors()
                ], 422);
            }

            $companyStory = CompanyStory::getInstance();
            $companyStory->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث قصة الشركة بنجاح',
                'data' => new CompanyStoryResource($companyStory->fresh())
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في تحديث قصة الشركة',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

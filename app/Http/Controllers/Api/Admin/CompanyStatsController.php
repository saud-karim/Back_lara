<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyStatsResource;
use App\Models\CompanyStats;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyStatsController extends Controller
{
    /**
     * Get company statistics
     */
    public function index(): JsonResponse
    {
        try {
            $companyStats = CompanyStats::getInstance();
            
            return response()->json([
                'success' => true,
                'data' => new CompanyStatsResource($companyStats)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في جلب إحصائيات الشركة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update company statistics
     */
    public function update(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'years_experience' => 'required|string|max:20',
                'total_customers' => 'required|string|max:20',
                'completed_projects' => 'required|string|max:20',
                'support_availability' => 'required|string|max:20'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'بيانات غير صحيحة',
                    'errors' => $validator->errors()
                ], 422);
            }

            $companyStats = CompanyStats::getInstance();
            $companyStats->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الإحصائيات بنجاح',
                'data' => new CompanyStatsResource($companyStats->fresh())
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في تحديث الإحصائيات',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

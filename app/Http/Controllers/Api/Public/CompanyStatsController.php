<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\CompanyStats;
use App\Http\Resources\CompanyStatsResource;
use Illuminate\Http\JsonResponse;

class CompanyStatsController extends Controller
{
    /**
     * Get company statistics for public access
     */
    public function index(): JsonResponse
    {
        try {
            $companyStats = CompanyStats::first();
            
            if (!$companyStats) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم العثور على إحصائيات الشركة'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new CompanyStatsResource($companyStats)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب إحصائيات الشركة'
            ], 500);
        }
    }
} 
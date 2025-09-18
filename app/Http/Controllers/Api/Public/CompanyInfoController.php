<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\CompanyInfo;
use App\Http\Resources\CompanyInfoResource;
use Illuminate\Http\JsonResponse;

class CompanyInfoController extends Controller
{
    /**
     * Get company information for public access
     */
    public function index(): JsonResponse
    {
        try {
            $companyInfo = CompanyInfo::first();
            
            if (!$companyInfo) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم العثور على معلومات الشركة'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new CompanyInfoResource($companyInfo)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب معلومات الشركة'
            ], 500);
        }
    }
} 
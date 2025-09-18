<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\CompanyValue;
use App\Http\Resources\CompanyValueResource;
use Illuminate\Http\JsonResponse;

class CompanyValueController extends Controller
{
    /**
     * Get company values for public access
     */
    public function index(): JsonResponse
    {
        try {
            $companyValues = CompanyValue::where('is_active', true)
                                        ->orderBy('order')
                                        ->get();

            return response()->json([
                'success' => true,
                'data' => CompanyValueResource::collection($companyValues)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب قيم الشركة'
            ], 500);
        }
    }
} 
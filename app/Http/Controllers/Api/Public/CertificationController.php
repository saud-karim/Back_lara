<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Certification;
use App\Http\Resources\CertificationResource;
use Illuminate\Http\JsonResponse;

class CertificationController extends Controller
{
    /**
     * Get certifications for public access
     */
    public function index(): JsonResponse
    {
        try {
            $certifications = Certification::where('is_active', true)
                                         ->orderBy('order')
                                         ->get();

            return response()->json([
                'success' => true,
                'data' => CertificationResource::collection($certifications)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب الشهادات'
            ], 500);
        }
    }
} 
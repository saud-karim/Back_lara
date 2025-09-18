<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\FAQ;
use App\Http\Resources\FAQResource;
use Illuminate\Http\JsonResponse;

class FAQController extends Controller
{
    /**
     * Get FAQs for public access
     */
    public function index(): JsonResponse
    {
        try {
            $faqs = FAQ::where('is_active', true)
                      ->orderBy('order')
                      ->get();

            return response()->json([
                'success' => true,
                'data' => FAQResource::collection($faqs)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب الأسئلة الشائعة'
            ], 500);
        }
    }
} 
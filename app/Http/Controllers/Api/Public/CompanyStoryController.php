<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\CompanyStory;
use App\Http\Resources\CompanyStoryResource;
use Illuminate\Http\JsonResponse;

class CompanyStoryController extends Controller
{
    /**
     * Get company story for public access
     */
    public function index(): JsonResponse
    {
        try {
            $companyStory = CompanyStory::first();
            
            if (!$companyStory) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم العثور على قصة الشركة'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new CompanyStoryResource($companyStory)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب قصة الشركة'
            ], 500);
        }
    }
} 
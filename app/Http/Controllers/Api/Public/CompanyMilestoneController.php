<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\CompanyMilestone;
use App\Http\Resources\CompanyMilestoneResource;
use Illuminate\Http\JsonResponse;

class CompanyMilestoneController extends Controller
{
    /**
     * Get company milestones for public access
     */
    public function index(): JsonResponse
    {
        try {
            $milestones = CompanyMilestone::where('is_active', true)
                                        ->orderBy('order')
                                        ->get();

            return response()->json([
                'success' => true,
                'data' => CompanyMilestoneResource::collection($milestones)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب المعالم التاريخية للشركة'
            ], 500);
        }
    }
} 
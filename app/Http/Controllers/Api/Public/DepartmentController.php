<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Http\Resources\DepartmentResource;
use Illuminate\Http\JsonResponse;

class DepartmentController extends Controller
{
    /**
     * Get departments for public access
     */
    public function index(): JsonResponse
    {
        try {
            $departments = Department::where('is_active', true)
                                   ->orderBy('order')
                                   ->get();

            return response()->json([
                'success' => true,
                'data' => DepartmentResource::collection($departments)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب الأقسام'
            ], 500);
        }
    }
} 
<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyMilestoneResource;
use App\Models\CompanyMilestone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;

class CompanyMilestoneController extends Controller
{
    /**
     * Get all company milestones
     */
    public function index(): AnonymousResourceCollection
    {
        try {
            $milestones = CompanyMilestone::ordered()->get();
            
            return CompanyMilestoneResource::collection($milestones)->additional([
                'meta' => [
                    'total' => $milestones->count(),
                    'active_count' => $milestones->where('is_active', true)->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في جلب المعالم التاريخية',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new milestone
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'year' => 'required|string|max:10',
                'event_ar' => 'required|string|max:255',
                'event_en' => 'required|string|max:255',
                'description_ar' => 'nullable|string',
                'description_en' => 'nullable|string',
                'order' => 'nullable|integer|min:0',
                'is_active' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'بيانات غير صحيحة',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();
            
            // Auto-assign order if not provided
            if (!isset($data['order'])) {
                $data['order'] = CompanyMilestone::max('order') + 1;
            }

            $milestone = CompanyMilestone::create($data);

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء المعلم التاريخي بنجاح',
                'data' => new CompanyMilestoneResource($milestone)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في إنشاء المعلم التاريخي',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show a specific milestone
     */
    public function show(CompanyMilestone $companyMilestone): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => new CompanyMilestoneResource($companyMilestone)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في جلب بيانات المعلم التاريخي',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a milestone
     */
    public function update(Request $request, CompanyMilestone $companyMilestone): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'year' => 'required|string|max:10',
                'event_ar' => 'required|string|max:255',
                'event_en' => 'required|string|max:255',
                'description_ar' => 'nullable|string',
                'description_en' => 'nullable|string',
                'order' => 'nullable|integer|min:0',
                'is_active' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'بيانات غير صحيحة',
                    'errors' => $validator->errors()
                ], 422);
            }

            $companyMilestone->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث المعلم التاريخي بنجاح',
                'data' => new CompanyMilestoneResource($companyMilestone->fresh())
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في تحديث المعلم التاريخي',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a milestone
     */
    public function destroy(CompanyMilestone $companyMilestone): JsonResponse
    {
        try {
            $companyMilestone->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف المعلم التاريخي بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في حذف المعلم التاريخي',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

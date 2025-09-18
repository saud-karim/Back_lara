<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyValueResource;
use App\Models\CompanyValue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;

class CompanyValueController extends Controller
{
    /**
     * Get all company values
     */
    public function index(): AnonymousResourceCollection
    {
        try {
            $companyValues = CompanyValue::ordered()->get();
            
            return CompanyValueResource::collection($companyValues)->additional([
                'meta' => [
                    'total' => $companyValues->count(),
                    'active_count' => $companyValues->where('is_active', true)->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في جلب قيم الشركة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new company value
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'title_ar' => 'required|string|max:255',
                'title_en' => 'required|string|max:255',
                'description_ar' => 'nullable|string',
                'description_en' => 'nullable|string',
                'icon' => 'nullable|string|max:10',
                'color' => 'nullable|string|max:100',
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
                $data['order'] = CompanyValue::max('order') + 1;
            }

            $companyValue = CompanyValue::create($data);

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء قيمة الشركة بنجاح',
                'data' => new CompanyValueResource($companyValue)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في إنشاء قيمة الشركة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show a specific company value
     */
    public function show(CompanyValue $companyValue): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => new CompanyValueResource($companyValue)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في جلب بيانات قيمة الشركة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a company value
     */
    public function update(Request $request, CompanyValue $companyValue): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'title_ar' => 'required|string|max:255',
                'title_en' => 'required|string|max:255',
                'description_ar' => 'nullable|string',
                'description_en' => 'nullable|string',
                'icon' => 'nullable|string|max:10',
                'color' => 'nullable|string|max:100',
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

            $companyValue->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث قيمة الشركة بنجاح',
                'data' => new CompanyValueResource($companyValue->fresh())
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في تحديث قيمة الشركة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a company value
     */
    public function destroy(CompanyValue $companyValue): JsonResponse
    {
        try {
            $companyValue->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف قيمة الشركة بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في حذف قيمة الشركة',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

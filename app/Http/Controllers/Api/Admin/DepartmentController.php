<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{
    /**
     * Get all departments
     */
    public function index(): AnonymousResourceCollection
    {
        try {
            $departments = Department::ordered()->get();
            
            return DepartmentResource::collection($departments)->additional([
                'meta' => [
                    'total' => $departments->count(),
                    'active_count' => $departments->where('is_active', true)->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في جلب الأقسام',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new department
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name_ar' => 'required|string|max:255',
                'name_en' => 'required|string|max:255',
                'description_ar' => 'nullable|string|max:500',
                'description_en' => 'nullable|string|max:500',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:100',
                'icon' => 'nullable|string|max:10',
                'color' => 'nullable|string|max:50',
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
                $data['order'] = Department::max('order') + 1;
            }

            $department = Department::create($data);

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء القسم بنجاح',
                'data' => new DepartmentResource($department)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في إنشاء القسم',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show a specific department
     */
    public function show(Department $department): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => new DepartmentResource($department)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في جلب بيانات القسم',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a department
     */
    public function update(Request $request, Department $department): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name_ar' => 'required|string|max:255',
                'name_en' => 'required|string|max:255',
                'description_ar' => 'nullable|string|max:500',
                'description_en' => 'nullable|string|max:500',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:100',
                'icon' => 'nullable|string|max:10',
                'color' => 'nullable|string|max:50',
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

            $department->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث القسم بنجاح',
                'data' => new DepartmentResource($department->fresh())
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في تحديث القسم',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a department
     */
    public function destroy(Department $department): JsonResponse
    {
        try {
            $department->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف القسم بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في حذف القسم',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update departments order
     */
    public function updateOrder(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'departments' => 'required|array',
                'departments.*.id' => 'required|exists:departments,id',
                'departments.*.order' => 'required|integer|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'بيانات غير صحيحة',
                    'errors' => $validator->errors()
                ], 422);
            }

            foreach ($request->departments as $item) {
                Department::where('id', $item['id'])->update(['order' => $item['order']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث ترتيب الأقسام بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في تحديث الترتيب',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

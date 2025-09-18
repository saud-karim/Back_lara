<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\FAQResource;
use App\Models\FAQ;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;

class FAQController extends Controller
{
    /**
     * Get all FAQs
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        try {
            $query = FAQ::query();
            
            // Filter by category if provided
            if ($request->has('category') && $request->category !== 'all') {
                $query->byCategory($request->category);
            }
            
            $faqs = $query->ordered()->get();
            
            return FAQResource::collection($faqs)->additional([
                'meta' => [
                    'total' => $faqs->count(),
                    'active_count' => $faqs->where('is_active', true)->count(),
                    'categories' => FAQ::distinct()->pluck('category')->values()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في جلب الأسئلة الشائعة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new FAQ
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'question_ar' => 'required|string|max:500',
                'question_en' => 'required|string|max:500',
                'answer_ar' => 'required|string',
                'answer_en' => 'required|string',
                'category' => 'nullable|string|max:100',
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
                $data['order'] = FAQ::max('order') + 1;
            }

            // Default category
            if (!isset($data['category'])) {
                $data['category'] = 'general';
            }

            $faq = FAQ::create($data);

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء السؤال بنجاح',
                'data' => new FAQResource($faq)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في إنشاء السؤال',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show a specific FAQ
     */
    public function show(FAQ $faq): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => new FAQResource($faq)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في جلب بيانات السؤال',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a FAQ
     */
    public function update(Request $request, FAQ $faq): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'question_ar' => 'required|string|max:500',
                'question_en' => 'required|string|max:500',
                'answer_ar' => 'required|string',
                'answer_en' => 'required|string',
                'category' => 'nullable|string|max:100',
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

            $faq->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث السؤال بنجاح',
                'data' => new FAQResource($faq->fresh())
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في تحديث السؤال',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a FAQ
     */
    public function destroy(FAQ $faq): JsonResponse
    {
        try {
            $faq->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف السؤال بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في حذف السؤال',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

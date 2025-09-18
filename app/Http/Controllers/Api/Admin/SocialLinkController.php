<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SocialLinkResource;
use App\Models\SocialLink;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;

class SocialLinkController extends Controller
{
    /**
     * Get all social links
     */
    public function index(): AnonymousResourceCollection
    {
        try {
            $socialLinks = SocialLink::ordered()->get();
            
            return SocialLinkResource::collection($socialLinks)->additional([
                'meta' => [
                    'total' => $socialLinks->count(),
                    'active_count' => $socialLinks->where('is_active', true)->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في جلب روابط التواصل',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new social link
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'platform' => 'required|string|max:50',
                'url' => 'required|url|max:255',
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
                $data['order'] = SocialLink::max('order') + 1;
            }

            $socialLink = SocialLink::create($data);

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء رابط التواصل بنجاح',
                'data' => new SocialLinkResource($socialLink)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في إنشاء رابط التواصل',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show a specific social link
     */
    public function show(SocialLink $socialLink): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => new SocialLinkResource($socialLink)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في جلب بيانات الرابط',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a social link
     */
    public function update(Request $request, SocialLink $socialLink): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'platform' => 'required|string|max:50',
                'url' => 'required|url|max:255',
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

            $socialLink->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث رابط التواصل بنجاح',
                'data' => new SocialLinkResource($socialLink->fresh())
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في تحديث رابط التواصل',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a social link
     */
    public function destroy(SocialLink $socialLink): JsonResponse
    {
        try {
            $socialLink->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف رابط التواصل بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في حذف رابط التواصل',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update social links order
     */
    public function updateOrder(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'social_links' => 'required|array',
                'social_links.*.id' => 'required|exists:social_links,id',
                'social_links.*.order' => 'required|integer|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'بيانات غير صحيحة',
                    'errors' => $validator->errors()
                ], 422);
            }

            foreach ($request->social_links as $item) {
                SocialLink::where('id', $item['id'])->update(['order' => $item['order']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث ترتيب روابط التواصل بنجاح'
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

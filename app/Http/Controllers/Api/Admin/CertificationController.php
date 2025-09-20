<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CertificationResource;
use App\Models\Certification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CertificationController extends Controller
{
    /**
     * Get all certifications
     */
    public function index(): AnonymousResourceCollection
    {
        try {
            $certifications = Certification::ordered()->get();
            
            return CertificationResource::collection($certifications)->additional([
                'meta' => [
                    'total' => $certifications->count(),
                    'active_count' => $certifications->where('is_active', true)->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في جلب الشهادات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new certification
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name_ar' => 'required|string|max:255',
                'name_en' => 'required|string|max:255',
                'description_ar' => 'nullable|string',
                'description_en' => 'nullable|string',
                'issuer_ar' => 'required|string|max:255',
                'issuer_en' => 'required|string|max:255',
                'issue_date' => 'required|date',
                'expiry_date' => 'nullable|date|after:issue_date',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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
            
            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->storeAs('public/certifications', $imageName);
                $data['image'] = '/storage/certifications/' . $imageName;
            }
            
            // Auto-assign order if not provided
            if (!isset($data['order'])) {
                $data['order'] = Certification::max('order') + 1;
            }

            $certification = Certification::create($data);

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء الشهادة بنجاح',
                'data' => new CertificationResource($certification)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في إنشاء الشهادة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show a specific certification
     */
    public function show(Certification $certification): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => new CertificationResource($certification)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في جلب بيانات الشهادة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a certification
     */
    public function update(Request $request, Certification $certification): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name_ar' => 'required|string|max:255',
                'name_en' => 'required|string|max:255',
                'description_ar' => 'nullable|string',
                'description_en' => 'nullable|string',
                'issuer_ar' => 'required|string|max:255',
                'issuer_en' => 'required|string|max:255',
                'issue_date' => 'required|date',
                'expiry_date' => 'nullable|date|after:issue_date',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($certification->image) {
                    $oldImagePath = str_replace('/storage/', 'public/', $certification->image);
                    Storage::delete($oldImagePath);
                }

                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->storeAs('public/certifications', $imageName);
                $data['image'] = '/storage/certifications/' . $imageName;
            }

            $certification->update($data);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الشهادة بنجاح',
                'data' => new CertificationResource($certification->fresh())
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في تحديث الشهادة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a certification
     */
    public function destroy(Certification $certification): JsonResponse
    {
        try {
            // Delete image if exists
            if ($certification->image) {
                $imagePath = str_replace('/storage/', 'public/', $certification->image);
                Storage::delete($imagePath);
            }

            $certification->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الشهادة بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في حذف الشهادة',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageContent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PageContentController extends Controller
{
    /**
     * Get page content
     */
    public function index(): JsonResponse
    {
        try {
            $pageContent = PageContent::getInstance();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $pageContent->id,
                    'about_page' => $pageContent->about_page ?? [],
                    'contact_page' => $pageContent->contact_page ?? [],
                    'created_at' => $pageContent->created_at?->format('Y-m-d H:i:s'),
                    'updated_at' => $pageContent->updated_at?->format('Y-m-d H:i:s')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في جلب محتوى الصفحات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update page content
     */
    public function update(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'about_page' => 'nullable|array',
                'contact_page' => 'nullable|array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'بيانات غير صحيحة',
                    'errors' => $validator->errors()
                ], 422);
            }

            $pageContent = PageContent::getInstance();
            
            $data = [];
            if ($request->has('about_page')) {
                $data['about_page'] = $request->about_page;
            }
            if ($request->has('contact_page')) {
                $data['contact_page'] = $request->contact_page;
            }

            $pageContent->update($data);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث محتوى الصفحات بنجاح',
                'data' => [
                    'id' => $pageContent->id,
                    'about_page' => $pageContent->fresh()->about_page ?? [],
                    'contact_page' => $pageContent->fresh()->contact_page ?? [],
                    'updated_at' => $pageContent->fresh()->updated_at?->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في تحديث محتوى الصفحات',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\PageContent;
use Illuminate\Http\JsonResponse;

class PageContentController extends Controller
{
    /**
     * Get page content for public access
     */
    public function index(): JsonResponse
    {
        try {
            $pageContent = PageContent::first();
            
            if (!$pageContent) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم العثور على محتوى الصفحات'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'about_page' => $pageContent->about_page,
                    'contact_page' => $pageContent->contact_page
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب محتوى الصفحات'
            ], 500);
        }
    }
} 
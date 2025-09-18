<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\ContactInfo;
use App\Http\Resources\ContactInfoResource;
use Illuminate\Http\JsonResponse;

class ContactInfoController extends Controller
{
    /**
     * Get contact information for public access
     */
    public function index(): JsonResponse
    {
        try {
            $contactInfo = ContactInfo::first();
            
            if (!$contactInfo) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم العثور على معلومات الاتصال'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new ContactInfoResource($contactInfo)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب معلومات الاتصال'
            ], 500);
        }
    }
} 
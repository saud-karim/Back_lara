<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\SocialLink;
use App\Http\Resources\SocialLinkResource;
use Illuminate\Http\JsonResponse;

class SocialLinksController extends Controller
{
    /**
     * Get social links for public access
     */
    public function index(): JsonResponse
    {
        try {
            $socialLinks = SocialLink::where('is_active', true)
                                   ->orderBy('order')
                                   ->get();

            return response()->json([
                'success' => true,
                'data' => SocialLinkResource::collection($socialLinks)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب روابط التواصل الاجتماعي'
            ], 500);
        }
    }
} 
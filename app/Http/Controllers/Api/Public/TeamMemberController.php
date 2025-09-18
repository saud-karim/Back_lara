<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use App\Http\Resources\TeamMemberResource;
use Illuminate\Http\JsonResponse;

class TeamMemberController extends Controller
{
    /**
     * Get team members for public access
     */
    public function index(): JsonResponse
    {
        try {
            $teamMembers = TeamMember::where('is_active', true)
                                   ->orderBy('order')
                                   ->get();

            return response()->json([
                'success' => true,
                'data' => TeamMemberResource::collection($teamMembers)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب أعضاء الفريق'
            ], 500);
        }
    }
} 
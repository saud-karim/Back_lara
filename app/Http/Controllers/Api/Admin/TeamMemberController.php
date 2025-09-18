<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeamMemberResource;
use App\Models\TeamMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;

class TeamMemberController extends Controller
{
    /**
     * Get all team members
     */
    public function index(): AnonymousResourceCollection
    {
        try {
            $teamMembers = TeamMember::ordered()->get();
            
            return TeamMemberResource::collection($teamMembers)->additional([
                'meta' => [
                    'total' => $teamMembers->count(),
                    'active_count' => $teamMembers->where('is_active', true)->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في جلب أعضاء الفريق',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new team member
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name_ar' => 'required|string|max:255',
                'name_en' => 'required|string|max:255',
                'role_ar' => 'nullable|string|max:255',
                'role_en' => 'nullable|string|max:255',
                'experience_ar' => 'nullable|string',
                'experience_en' => 'nullable|string',
                'specialty_ar' => 'nullable|string|max:255',
                'specialty_en' => 'nullable|string|max:255',
                'image' => 'nullable|string|max:10',
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
                $data['order'] = TeamMember::max('order') + 1;
            }

            $teamMember = TeamMember::create($data);

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء عضو الفريق بنجاح',
                'data' => new TeamMemberResource($teamMember)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في إنشاء عضو الفريق',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show a specific team member
     */
    public function show(TeamMember $teamMember): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => new TeamMemberResource($teamMember)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في جلب بيانات عضو الفريق',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a team member
     */
    public function update(Request $request, TeamMember $teamMember): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name_ar' => 'required|string|max:255',
                'name_en' => 'required|string|max:255',
                'role_ar' => 'nullable|string|max:255',
                'role_en' => 'nullable|string|max:255',
                'experience_ar' => 'nullable|string',
                'experience_en' => 'nullable|string',
                'specialty_ar' => 'nullable|string|max:255',
                'specialty_en' => 'nullable|string|max:255',
                'image' => 'nullable|string|max:10',
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

            $teamMember->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث عضو الفريق بنجاح',
                'data' => new TeamMemberResource($teamMember->fresh())
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في تحديث عضو الفريق',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a team member
     */
    public function destroy(TeamMember $teamMember): JsonResponse
    {
        try {
            $teamMember->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف عضو الفريق بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في حذف عضو الفريق',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

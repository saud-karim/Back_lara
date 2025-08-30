<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    /**
     * Get user profile.
     */
    public function profile(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'user' => new UserResource($request->user()->load('supplier'))
            ]
        ]);
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'address' => 'sometimes|string|max:1000',
            'phone' => 'sometimes|string|max:20',
            'password' => 'sometimes|string|min:8|confirmed',
        ]);

        $user = $this->authService->updateProfile($request->user(), $validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => new UserResource($user->load('supplier')),
        ]);
    }

    /**
     * Upload user documents.
     */
    public function uploadDocuments(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'document' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        // Handle file upload logic here
        // For now, just return success message

        return response()->json([
            'message' => 'Document uploaded successfully',
        ]);
    }
} 
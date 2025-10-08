<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Try different auth guards
        $user = auth('sanctum')->user() ?? auth()->user();
        
        if (!$user) {
            return response()->json([
                "success" => false,
                "message" => "يجب تسجيل الدخول أولاً"
            ], 401);
        }
        
        // تحويل الأدوار إلى array
        $allowedRoles = is_array($roles) ? $roles : [$roles];
        
        // فحص role من users table مباشرة
        if (!in_array($user->role, $allowedRoles)) {
            return response()->json([
                "success" => false,
                "message" => "ليس لديك الصلاحية للوصول إلى هذا المحتوى",
                "user_role" => $user->role,
                "user_email" => $user->email,
                "required_roles" => $allowedRoles,
                "debug" => "Direct role check from users.role column"
            ], 403);
        }

        return $next($request);
    }
}
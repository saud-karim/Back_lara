<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            return response()->json([
                "success" => false,
                "message" => "غير مسجل الدخول"
            ], 401);
        }

        $user = auth()->user();
        
        // تحويل الأدوار إلى array
        $allowedRoles = is_array($roles) ? $roles : [$roles];
        
        // فحص إذا كان المستخدم لديه أي من الأدوار المطلوبة
        if (!in_array($user->role, $allowedRoles)) {
            return response()->json([
                "success" => false,
                "message" => "ليس لديك الصلاحية للوصول إلى هذا المحتوى",
                "user_role" => $user->role,
                "required_roles" => $allowedRoles
            ], 403);
        }

        return $next($request);
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscription;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NewsletterController extends Controller
{
    /**
     * الاشتراك في النشرة البريدية
     */
    public function subscribe(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'preferences' => 'nullable|array',
            'preferences.*' => 'in:new_products,offers,industry_news,tips'
        ]);

        $subscription = NewsletterSubscription::where('email', $request->email)->first();

        if ($subscription) {
            if ($subscription->isActive()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email is already subscribed to newsletter'
                ], 422);
            } else {
                // إعادة تفعيل الاشتراك
                $subscription->subscribe();
                $subscription->updatePreferences($request->preferences ?? []);
            }
        } else {
            $subscription = NewsletterSubscription::create([
                'email' => $request->email,
                'preferences' => $request->preferences ?? [],
                'status' => NewsletterSubscription::STATUS_ACTIVE
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم الاشتراك في النشرة البريدية بنجاح',
            'data' => [
                'subscription' => [
                    'email' => $subscription->email,
                    'preferences' => $subscription->preferences,
                    'status' => $subscription->status
                ]
            ]
        ], 201);
    }

    /**
     * إلغاء الاشتراك في النشرة البريدية
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $subscription = NewsletterSubscription::where('email', $request->email)->first();

        if (!$subscription || $subscription->isUnsubscribed()) {
            return response()->json([
                'success' => false,
                'message' => 'Email is not subscribed to newsletter'
            ], 404);
        }

        $subscription->unsubscribe();

        return response()->json([
            'success' => true,
            'message' => 'تم إلغاء الاشتراك في النشرة البريدية بنجاح'
        ]);
    }

    /**
     * تحديث تفضيلات النشرة البريدية
     */
    public function updatePreferences(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'preferences' => 'required|array',
            'preferences.*' => 'in:new_products,offers,industry_news,tips'
        ]);

        $subscription = NewsletterSubscription::where('email', $request->email)
                                            ->active()
                                            ->first();

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'Email is not subscribed to newsletter'
            ], 404);
        }

        $subscription->updatePreferences($request->preferences);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث تفضيلات النشرة البريدية بنجاح',
            'data' => [
                'subscription' => [
                    'email' => $subscription->email,
                    'preferences' => $subscription->preferences,
                    'status' => $subscription->status
                ]
            ]
        ]);
    }

    /**
     * جلب حالة الاشتراك
     */
    public function status(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $subscription = NewsletterSubscription::where('email', $request->email)->first();

        if (!$subscription) {
            return response()->json([
                'success' => true,
                'data' => [
                    'subscribed' => false,
                    'preferences' => []
                ]
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'subscribed' => $subscription->isActive(),
                'preferences' => $subscription->preferences ?? [],
                'status' => $subscription->status
            ]
        ]);
    }

    /**
     * جلب التفضيلات المتاحة
     */
    public function availablePreferences(): JsonResponse
    {
        $preferences = NewsletterSubscription::getAvailablePreferences();

        return response()->json([
            'success' => true,
            'data' => [
                'preferences' => $preferences
            ]
        ]);
    }
} 
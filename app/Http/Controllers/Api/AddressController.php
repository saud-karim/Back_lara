<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    /**
     * جلب عناوين المستخدم
     */
    public function index(): JsonResponse
    {
        $addresses = Auth::user()->addresses()
                             ->orderBy('is_default', 'desc')
                             ->orderBy('created_at', 'desc')
                             ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'addresses' => $addresses
            ]
        ]);
    }

    /**
     * عرض تفاصيل عنوان محدد
     */
    public function show($id): JsonResponse
    {
        $address = Auth::user()->addresses()->find($id);

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'address' => $address
            ]
        ]);
    }

    /**
     * إضافة عنوان جديد
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:home,work,other',
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'street' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'required|string|max:100',
            'is_default' => 'boolean'
        ]);

        $address = Auth::user()->addresses()->create([
            'type' => $request->type,
            'name' => $request->name,
            'phone' => $request->phone,
            'street' => $request->street,
            'city' => $request->city,
            'state' => $request->state,
            'postal_code' => $request->postal_code,
            'country' => $request->country,
            'is_default' => $request->boolean('is_default', false)
        ]);

        // إذا كان العنوان افتراضي، اجعله الوحيد الافتراضي
        if ($address->is_default) {
            $address->makeDefault();
        }

        return response()->json([
            'success' => true,
            'message' => 'Address created successfully',
            'data' => [
                'address' => $address
            ]
        ], 201);
    }

    /**
     * تحديث عنوان
     */
    public function update(Request $request, $id): JsonResponse
    {
        $address = Auth::user()->addresses()->find($id);

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found'
            ], 404);
        }

        $request->validate([
            'type' => 'required|in:home,work,other',
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'street' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'required|string|max:100',
            'is_default' => 'boolean'
        ]);

        $address->update([
            'type' => $request->type,
            'name' => $request->name,
            'phone' => $request->phone,
            'street' => $request->street,
            'city' => $request->city,
            'state' => $request->state,
            'postal_code' => $request->postal_code,
            'country' => $request->country,
            'is_default' => $request->boolean('is_default', $address->is_default)
        ]);

        // إذا كان العنوان افتراضي، اجعله الوحيد الافتراضي
        if ($address->is_default) {
            $address->makeDefault();
        }

        // إعادة تحميل العنوان للحصول على أحدث البيانات
        $address->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Address updated successfully',
            'data' => [
                'address' => $address
            ]
        ]);
    }

    /**
     * حذف عنوان
     */
    public function destroy($id): JsonResponse
    {
        $address = Auth::user()->addresses()->find($id);

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found'
            ], 404);
        }

        // لا يمكن حذف العنوان الافتراضي إذا كان هناك عناوين أخرى
        if ($address->is_default && Auth::user()->addresses()->count() > 1) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete default address. Please set another address as default first.'
            ], 422);
        }

        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully'
        ]);
    }

    /**
     * جعل عنوان افتراضي
     */
    public function makeDefault($id): JsonResponse
    {
        $address = Auth::user()->addresses()->find($id);

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found'
            ], 404);
        }

        $address->makeDefault();

        return response()->json([
            'success' => true,
            'message' => 'Address set as default successfully',
            'data' => [
                'address' => $address
            ]
        ]);
    }
} 
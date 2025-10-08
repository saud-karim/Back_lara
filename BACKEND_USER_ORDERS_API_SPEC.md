# 📦 User Orders API - Backend Specification

## 🎯 **المطلوب من الـ Backend:**

الـ Frontend محتاج API endpoint واحد لعرض طلبات المستخدم في صفحة `/account/orders`

---

## 📍 **API Endpoint:**

```
GET /api/v1/orders
```

**Authorization:** Bearer Token (Required)

---

## 📥 **Request:**

### **Headers:**
```http
GET /api/v1/orders HTTP/1.1
Host: 127.0.0.1:8000
Accept: application/json
Content-Type: application/json
Authorization: Bearer {user_token}
```

### **Query Parameters (Optional):**

```javascript
{
  page: 1,              // رقم الصفحة (Default: 1)
  per_page: 10,         // عدد الطلبات في الصفحة (Default: 10)
  status: 'pending'     // Filter حسب الحالة (Optional)
}
```

**Status values:**
- `pending` - في الانتظار
- `confirmed` - تم التأكيد
- `processing` - قيد المعالجة
- `shipped` - تم الشحن
- `delivered` - تم التسليم
- `cancelled` - ملغي
- `refunded` - مسترد

### **مثال Request كامل:**

```bash
curl -X GET "http://127.0.0.1:8000/api/v1/orders?page=1&per_page=10&status=pending" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 11|xxxxx"
```

---

## 📤 **Response المطلوب:**

### **Success Response (200 OK):**

```json
{
  "success": true,
  "message": "Orders retrieved successfully",
  "data": [
    {
      "id": 42,
      "order_number": "ORD-2025-00042",
      "user_id": 2,
      
      "status": "pending",
      "status_ar": "في الانتظار",
      
      "payment_method": "cash_on_delivery",
      "payment_method_ar": "الدفع عند الاستلام",
      "payment_status": "pending",
      "payment_status_ar": "في الانتظار",
      
      "subtotal": 100.00,
      "shipping_cost": 50.00,
      "tax_amount": 0.00,
      "discount_amount": 0.00,
      "total_amount": 150.00,
      "currency": "EGP",
      
      "items_count": 3,
      
      "shipping_address": {
        "name": "محمد أحمد",
        "phone": "+201234567890",
        "governorate": "القاهرة",
        "city": "مدينة نصر",
        "district": "الحي السابع",
        "street": "شارع مكرم عبيد",
        "building_number": "15",
        "floor": "3",
        "apartment": "5",
        "postal_code": "11371"
      },
      
      "estimated_delivery_date": "2025-10-15",
      
      "can_be_cancelled": true,
      
      "created_at": "2025-10-08T13:25:07.000000Z",
      "updated_at": "2025-10-08T13:25:07.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 3,
    "per_page": 10,
    "total": 28,
    "from": 1,
    "to": 10
  }
}
```

---

## 📋 **Required Fields (الحقول المطلوبة):**

### **1. Order Info (معلومات الطلب):**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `id` | integer | ✅ Yes | Order ID |
| `order_number` | string | ✅ Yes | رقم الطلب (e.g., "ORD-2025-00042") |
| `user_id` | integer | ✅ Yes | User ID |

---

### **2. Status (الحالة):**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `status` | string | ✅ Yes | حالة الطلب (English) |
| `status_ar` | string | ✅ Yes | حالة الطلب (Arabic) |

**Status Mapping:**
```javascript
{
  'pending': 'في الانتظار',
  'confirmed': 'تم التأكيد',
  'processing': 'قيد المعالجة',
  'shipped': 'تم الشحن',
  'delivered': 'تم التسليم',
  'cancelled': 'ملغي',
  'refunded': 'مسترد'
}
```

---

### **3. Payment (الدفع):**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `payment_method` | string | ✅ Yes | طريقة الدفع (English) |
| `payment_method_ar` | string | ✅ Yes | طريقة الدفع (Arabic) |
| `payment_status` | string | ✅ Yes | حالة الدفع (English) |
| `payment_status_ar` | string | ✅ Yes | حالة الدفع (Arabic) |

**Payment Method Mapping:**
```javascript
{
  'cash_on_delivery': 'الدفع عند الاستلام',
  'card': 'بطاقة ائتمانية',
  'wallet': 'محفظة إلكترونية',
  'installment': 'تقسيط'
}
```

**Payment Status Mapping:**
```javascript
{
  'pending': 'في الانتظار',
  'paid': 'مدفوع',
  'failed': 'فشل',
  'refunded': 'مسترد'
}
```

---

### **4. Amounts (المبالغ):**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `subtotal` | decimal(10,2) | ✅ Yes | مجموع المنتجات |
| `shipping_cost` | decimal(10,2) | ✅ Yes | تكلفة الشحن |
| `tax_amount` | decimal(10,2) | ✅ Yes | الضريبة |
| `discount_amount` | decimal(10,2) | ✅ Yes | الخصم |
| `total_amount` | decimal(10,2) | ✅ Yes | الإجمالي النهائي |
| `currency` | string | ✅ Yes | العملة (e.g., "EGP") |

---

### **5. Items Info:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `items_count` | integer | ✅ Yes | عدد المنتجات في الطلب |

---

### **6. Shipping Address (عنوان الشحن):**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `shipping_address` | object | ✅ Yes | عنوان الشحن (JSON object) |
| `shipping_address.name` | string | ✅ Yes | الاسم |
| `shipping_address.phone` | string | ✅ Yes | الهاتف |
| `shipping_address.governorate` | string | ✅ Yes | المحافظة |
| `shipping_address.city` | string | ✅ Yes | المدينة |
| `shipping_address.street` | string | ✅ Yes | الشارع |
| `shipping_address.district` | string | ❌ No | الحي (optional) |
| `shipping_address.building_number` | string | ❌ No | رقم العقار (optional) |
| `shipping_address.floor` | string | ❌ No | الطابق (optional) |
| `shipping_address.apartment` | string | ❌ No | الشقة (optional) |
| `shipping_address.postal_code` | string | ❌ No | الرمز البريدي (optional) |

---

### **7. Other Fields:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `estimated_delivery_date` | string\|null | ❌ No | موعد التسليم المتوقع (YYYY-MM-DD) |
| `can_be_cancelled` | boolean | ✅ Yes | هل يمكن إلغاء الطلب؟ |
| `created_at` | string | ✅ Yes | تاريخ الإنشاء (ISO 8601) |
| `updated_at` | string | ✅ Yes | تاريخ آخر تحديث (ISO 8601) |

---

### **8. Pagination Meta:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `meta.current_page` | integer | ✅ Yes | الصفحة الحالية |
| `meta.last_page` | integer | ✅ Yes | آخر صفحة |
| `meta.per_page` | integer | ✅ Yes | عدد العناصر في الصفحة |
| `meta.total` | integer | ✅ Yes | إجمالي عدد الطلبات |
| `meta.from` | integer | ✅ Yes | من رقم |
| `meta.to` | integer | ✅ Yes | إلى رقم |

---

## 🔧 **Business Logic:**

### **1. can_be_cancelled Logic:**

```php
// في الـ Backend
$order->can_be_cancelled = in_array($order->status, ['pending', 'confirmed']) 
                           && $order->payment_status !== 'paid';
```

**القاعدة:**
- ✅ `pending` → يمكن الإلغاء
- ✅ `confirmed` → يمكن الإلغاء
- ❌ `processing` → لا يمكن الإلغاء
- ❌ `shipped` → لا يمكن الإلغاء
- ❌ `delivered` → لا يمكن الإلغاء
- ❌ `cancelled` → مُلغى بالفعل
- ❌ إذا `payment_status = paid` → لا يمكن الإلغاء

---

### **2. items_count Calculation:**

```php
// في الـ Backend
$order->items_count = $order->items()->sum('quantity');

// OR if you want distinct products count:
$order->items_count = $order->items()->count();
```

---

### **3. estimated_delivery_date:**

```php
// في الـ Backend
if ($order->status === 'shipped') {
    $order->estimated_delivery_date = Carbon::parse($order->shipped_at)
        ->addDays(3)
        ->format('Y-m-d');
} else {
    $order->estimated_delivery_date = null;
}
```

---

## 🧪 **Test Examples:**

### **Test 1: Get All Orders (No Filter)**

**Request:**
```bash
GET /api/v1/orders?page=1&per_page=10
Authorization: Bearer {token}
```

**Expected:**
- ✅ Return all user orders (any status)
- ✅ 10 orders per page
- ✅ Pagination meta included

---

### **Test 2: Filter by Status**

**Request:**
```bash
GET /api/v1/orders?status=pending
Authorization: Bearer {token}
```

**Expected:**
- ✅ Return only pending orders
- ✅ All orders have `status: "pending"`

---

### **Test 3: Empty Orders**

**Scenario:** User has no orders

**Expected Response:**
```json
{
  "success": true,
  "message": "Orders retrieved successfully",
  "data": [],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 10,
    "total": 0,
    "from": 0,
    "to": 0
  }
}
```

---

### **Test 4: Unauthorized**

**Request:** No Bearer token

**Expected Response:**
```json
{
  "success": false,
  "message": "Unauthenticated.",
  "data": null
}
```
**Status Code:** 401

---

## ⚠️ **Important Notes:**

### **1. Authorization:**
```php
// في الـ Backend - لازم user يشوف طلباته بس!
$orders = Order::where('user_id', auth()->id())
    ->orderBy('created_at', 'desc')
    ->paginate($perPage);
```

**❌ DON'T:**
```php
// هذا خطأ! - المستخدم هيشوف طلبات الكل!
$orders = Order::paginate($perPage);
```

---

### **2. Order of Results:**
```php
// الأحدث أولاً
->orderBy('created_at', 'desc')
```

---

### **3. Decimal Precision:**
```php
// كل المبالغ decimal(10, 2)
'subtotal' => 100.00,      // ✅ Good
'subtotal' => 100,         // ❌ Bad (missing decimals)
'subtotal' => "100.00",    // ❌ Bad (string instead of number)
```

---

### **4. Boolean Values:**
```php
'can_be_cancelled' => true,    // ✅ Good (boolean)
'can_be_cancelled' => 1,       // ❌ Bad (integer)
'can_be_cancelled' => "true",  // ❌ Bad (string)
```

---

### **5. Date Format:**
```php
'created_at' => '2025-10-08T13:25:07.000000Z',  // ✅ ISO 8601
'created_at' => '2025-10-08 13:25:07',          // ❌ Not ISO 8601
```

---

## 🚀 **Implementation Example (Laravel):**

### **Route:**
```php
// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/orders', [OrderController::class, 'index']);
});
```

---

### **Controller:**
```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $status = $request->get('status');
        
        // Get user's orders only
        $query = Order::where('user_id', auth()->id())
            ->with('items') // To calculate items_count
            ->orderBy('created_at', 'desc');
        
        // Apply status filter if provided
        if ($status) {
            $query->where('status', $status);
        }
        
        // Paginate
        $orders = $query->paginate($perPage);
        
        // Transform data
        $data = $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'user_id' => $order->user_id,
                
                'status' => $order->status,
                'status_ar' => $this->translateStatus($order->status),
                
                'payment_method' => $order->payment_method,
                'payment_method_ar' => $this->translatePaymentMethod($order->payment_method),
                'payment_status' => $order->payment_status,
                'payment_status_ar' => $this->translatePaymentStatus($order->payment_status),
                
                'subtotal' => (float) $order->subtotal,
                'shipping_cost' => (float) $order->shipping_cost,
                'tax_amount' => (float) $order->tax_amount,
                'discount_amount' => (float) $order->discount_amount,
                'total_amount' => (float) $order->total_amount,
                'currency' => $order->currency ?? 'EGP',
                
                'items_count' => $order->items->sum('quantity'),
                
                'shipping_address' => $order->shipping_address, // Already JSON
                
                'estimated_delivery_date' => $order->estimated_delivery_date,
                
                'can_be_cancelled' => $this->canBeCancelled($order),
                
                'created_at' => $order->created_at->toIso8601String(),
                'updated_at' => $order->updated_at->toIso8601String(),
            ];
        });
        
        return response()->json([
            'success' => true,
            'message' => 'Orders retrieved successfully',
            'data' => $data,
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
                'from' => $orders->firstItem() ?? 0,
                'to' => $orders->lastItem() ?? 0,
            ]
        ]);
    }
    
    private function canBeCancelled($order)
    {
        return in_array($order->status, ['pending', 'confirmed']) 
               && $order->payment_status !== 'paid';
    }
    
    private function translateStatus($status)
    {
        $translations = [
            'pending' => 'في الانتظار',
            'confirmed' => 'تم التأكيد',
            'processing' => 'قيد المعالجة',
            'shipped' => 'تم الشحن',
            'delivered' => 'تم التسليم',
            'cancelled' => 'ملغي',
            'refunded' => 'مسترد',
        ];
        
        return $translations[$status] ?? $status;
    }
    
    private function translatePaymentMethod($method)
    {
        $translations = [
            'cash_on_delivery' => 'الدفع عند الاستلام',
            'card' => 'بطاقة ائتمانية',
            'wallet' => 'محفظة إلكترونية',
            'installment' => 'تقسيط',
        ];
        
        return $translations[$method] ?? $method;
    }
    
    private function translatePaymentStatus($status)
    {
        $translations = [
            'pending' => 'في الانتظار',
            'paid' => 'مدفوع',
            'failed' => 'فشل',
            'refunded' => 'مسترد',
        ];
        
        return $translations[$status] ?? $status;
    }
}
```

---

## ✅ **Checklist للـ Backend:**

- [ ] ✅ Route: `GET /api/v1/orders`
- [ ] ✅ Middleware: `auth:sanctum`
- [ ] ✅ Filter by user_id: `where('user_id', auth()->id())`
- [ ] ✅ Optional status filter
- [ ] ✅ Pagination: 10 items per page
- [ ] ✅ Order by: `created_at DESC` (الأحدث أولاً)
- [ ] ✅ Return all required fields
- [ ] ✅ `status_ar` translation
- [ ] ✅ `payment_method_ar` translation
- [ ] ✅ `payment_status_ar` translation
- [ ] ✅ `items_count` calculation
- [ ] ✅ `can_be_cancelled` logic
- [ ] ✅ Decimal amounts (not strings)
- [ ] ✅ Boolean values (not integers)
- [ ] ✅ ISO 8601 dates
- [ ] ✅ Pagination meta
- [ ] ✅ Handle empty results
- [ ] ✅ Handle unauthorized (401)

---

## 📞 **Contact:**

إذا في أي استفسار عن الـ API Specification:
- Frontend team جاهز للمساعدة
- هذا الملف يحتوي على كل التفاصيل المطلوبة

---

**Last Updated:** October 8, 2025 - 18:00  
**Version:** 1.0  
**Status:** 🚀 Ready for Backend Implementation


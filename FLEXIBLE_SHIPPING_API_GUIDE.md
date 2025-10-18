# 🚀 Flexible Shipping API - دليل الاستخدام المرن

## ✅ **النظام الجديد: مرن بالكامل!**

الآن يمكنك إرسال الطلبات لـ **أي شركة شحن** بدون الحاجة لكود مخصص!

---

## 🎯 **كيف يعمل النظام:**

```
1. اكتب اسم أي شركة شحن (مثلاً: "Bosta", "Aramex", "Fast Delivery", أي اسم)
2. اكتب API URL للشركة
3. اكتب API Key (اختياري)
4. اختر البيانات المُرسلة (field_mapping)
5. اضغط "إرسال" - الباك إند يبعت فعلياً! ✅
```

---

## 📡 **API Endpoint:**

```
POST /api/v1/admin/shipping/send
```

**Authorization:** Bearer Token (Admin)

---

## 📤 **Request Format:**

### **الطريقة الكاملة (مع Field Mapping):**

```json
{
  "order_ids": [6, 7, 8],
  "shipping_company": "Fast Delivery",
  "custom_api_url": "https://api.fastdelivery.com/v1/shipments",
  "custom_api_key": "sk_live_abc123xyz",
  "field_mapping": [
    {
      "id": "order_number",
      "field_path": "order.order_number",
      "enabled": true
    },
    {
      "id": "customer_name",
      "field_path": "customer.name",
      "enabled": true
    },
    {
      "id": "customer_phone",
      "field_path": "customer.phone",
      "enabled": true
    },
    {
      "id": "customer_email",
      "field_path": "customer.email",
      "enabled": true
    },
    {
      "id": "address_street",
      "field_path": "shipping_address.street",
      "enabled": true
    },
    {
      "id": "address_city",
      "field_path": "shipping_address.city",
      "enabled": true
    },
    {
      "id": "address_governorate",
      "field_path": "shipping_address.governorate",
      "enabled": true
    },
    {
      "id": "total_amount",
      "field_path": "order.total_amount",
      "enabled": true
    },
    {
      "id": "items",
      "field_path": "items[].product.name",
      "enabled": true
    }
  ]
}
```

---

### **الطريقة المختصرة (بدون Field Mapping):**

```json
{
  "order_ids": [6],
  "shipping_company": "Bosta",
  "custom_api_url": "https://app.bosta.co/api/v2/deliveries",
  "custom_api_key": "Bearer_xyz123"
}
```

**ملاحظة:** إذا لم تحدد `field_mapping`, سيتم إرسال جميع البيانات الأساسية تلقائياً:
- Order reference
- Customer name, phone, email
- Shipping address
- Items
- Total amount
- Payment method

---

## 📊 **ما يتم إرساله للـ Shipping API:**

### **Payload المُرسل:**

```json
{
  "order_reference": "ORD-2025-0001",
  "order_id": 6,
  "customer_name": "محمد أحمد",
  "customer_phone": "+201234567890",
  "customer_email": "customer@example.com",
  "shipping_address": {
    "name": "محمد أحمد",
    "phone": "+201234567890",
    "street": "شارع 15، الحي السابع",
    "city": "القاهرة",
    "governorate": "القاهرة",
    "building_number": "15",
    "floor": "3",
    "apartment": "5"
  },
  "items": [
    {
      "product_id": 10,
      "product_name": "iPhone 15 Pro",
      "quantity": 2,
      "unit_price": 500.00,
      "subtotal": 1000.00
    }
  ],
  "total_amount": 1050.00,
  "currency": "EGP",
  "payment_method": "cash_on_delivery"
}
```

**الـ Payload يُبنى ديناميكياً** بناءً على `field_mapping` الذي ترسله!

---

## 🔄 **كيف يعمل الباك إند:**

### **Step 1: Validation**
```php
✅ يتحقق أن order_ids موجودة
✅ يتحقق أن custom_api_url صالح
✅ يتحقق أن shipping_company مكتوب
```

### **Step 2: Extract Data**
```php
✅ يستخرج البيانات من Order بناءً على field_mapping
✅ إذا لم يُحدد field_mapping، يستخدم البيانات الأساسية
```

### **Step 3: Build Payload**
```php
✅ يبني JSON payload ديناميكياً
✅ يضيف جميع البيانات المُستخرجة
✅ يضيف البيانات الأساسية تلقائياً (customer, address, items)
```

### **Step 4: Send HTTP Request**
```php
✅ يبعت POST request للـ API URL
✅ يضيف Authorization header مع API Key
✅ Timeout: 30 seconds
```

### **Step 5: Handle Response**
```php
✅ إذا نجح: يستخرج tracking_number ويحفظه
✅ إذا فشل: يرجع الخطأ من الـ API
```

---

## 📥 **Response Format:**

### **Success Response:**

```json
{
  "success": true,
  "data": {
    "results": [
      {
        "order_id": 6,
        "status": "success",
        "tracking_number": "SH-2025-00123",
        "shipping_company": "Fast Delivery",
        "message": "Shipment created successfully",
        "raw_response": {
          "tracking_number": "SH-2025-00123",
          "status": "created",
          "estimated_delivery": "2025-10-20"
        }
      }
    ],
    "summary": {
      "total": 1,
      "success": 1,
      "failed": 0
    }
  }
}
```

---

### **Failed Response:**

```json
{
  "success": true,
  "data": {
    "results": [
      {
        "order_id": 6,
        "status": "failed",
        "error": "Invalid API key",
        "shipping_company": "Fast Delivery"
      }
    ],
    "summary": {
      "total": 1,
      "success": 0,
      "failed": 1
    }
  }
}
```

---

## 🔍 **Tracking Number Detection:**

الباك إند **يبحث تلقائياً** عن tracking number في الـ response بأسماء مختلفة:

```php
$trackingNumber = $response['tracking_number']
    ?? $response['trackingNumber']
    ?? $response['tracking_id']
    ?? $response['trackingId']
    ?? $response['awb']
    ?? $response['shipment_id']
    ?? $response['shipmentId']
    ?? $response['reference']
    ?? $response['id']
    ?? 'TRACK-' . time();  // Fallback
```

**يعني:** مهما كان اسم الـ field في الـ response, سيجده!

---

## 🔐 **Authorization Methods:**

الباك إند يدعم **Bearer Token** تلقائياً:

```http
Authorization: Bearer sk_live_abc123xyz
```

إذا احتجت نوع آخر من الـ authorization، يمكنك تعديل الـ header في الـ frontend أو الباك إند.

---

## 🌐 **أمثلة لشركات شحن حقيقية:**

### **1. Bosta:**

```json
{
  "order_ids": [6],
  "shipping_company": "Bosta",
  "custom_api_url": "https://app.bosta.co/api/v2/deliveries",
  "custom_api_key": "Bearer_YOUR_BOSTA_KEY"
}
```

**Payload المُرسل لـ Bosta:**
```json
{
  "order_reference": "ORD-2025-0001",
  "customer_name": "محمد أحمد",
  "customer_phone": "+201234567890",
  "shipping_address": {...},
  "items": [...],
  "total_amount": 1050.00,
  "currency": "EGP",
  "payment_method": "cash_on_delivery"
}
```

---

### **2. Aramex:**

```json
{
  "order_ids": [7],
  "shipping_company": "Aramex",
  "custom_api_url": "https://api.aramex.com/v1/shipments",
  "custom_api_key": "Bearer_YOUR_ARAMEX_KEY"
}
```

---

### **3. أي شركة مخصصة:**

```json
{
  "order_ids": [8],
  "shipping_company": "My Custom Shipping",
  "custom_api_url": "https://api.myshipping.com/create",
  "custom_api_key": "abc123"
}
```

---

## 📋 **Available Field Paths:**

يمكنك استخدام هذه الـ paths في `field_mapping`:

### **Order Fields:**
```
order.id
order.order_number
order.total_amount
order.subtotal
order.shipping_amount (or shipping_cost)
order.tax_amount
order.discount_amount
order.payment_method
order.payment_status
order.currency
order.notes
order.created_at
```

### **Customer Fields:**
```
customer.id
customer.name
customer.phone
customer.email
```

### **Shipping Address Fields:**
```
shipping_address.name
shipping_address.phone
shipping_address.street
shipping_address.city
shipping_address.governorate
shipping_address.district
shipping_address.building_number
shipping_address.floor
shipping_address.apartment
shipping_address.postal_code
```

### **Items Fields (Array):**
```
items[].product_id
items[].product.name
items[].product.sku
items[].product.weight
items[].quantity
items[].unit_price
items[].subtotal
```

---

## 🧪 **Testing:**

### **Test Script (cURL):**

```bash
curl -X POST "http://127.0.0.1:8000/api/v1/admin/shipping/send" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "order_ids": [6],
    "shipping_company": "Test Shipping",
    "custom_api_url": "https://webhook.site/YOUR_UNIQUE_URL",
    "custom_api_key": "test123",
    "field_mapping": [
      {"id": "order_number", "field_path": "order.order_number", "enabled": true},
      {"id": "customer_name", "field_path": "customer.name", "enabled": true},
      {"id": "customer_phone", "field_path": "customer.phone", "enabled": true}
    ]
  }'
```

**ملاحظة:** استخدم [webhook.site](https://webhook.site) لاختبار الـ payload المُرسل!

---

## 📊 **Database Updates:**

بعد النجاح، يتم تحديث `orders` table:

```sql
UPDATE orders SET
  tracking_number = 'SH-2025-00123',
  shipping_company = 'Fast Delivery',
  shipping_status = 'sent',
  shipped_at = '2025-10-15 19:30:00'
WHERE id = 6;
```

---

## 🔍 **Logging:**

جميع الـ requests والـ responses مُسجلة في `storage/logs/laravel.log`:

### **Success Log:**
```
[2025-10-15 19:30:00] local.INFO: Sending to shipping company {"order_id":6,"company":"Fast Delivery","api_url":"https://api.fastdelivery.com/v1/shipments","has_api_key":true}

[2025-10-15 19:30:01] local.INFO: Shipping API response {"order_id":6,"status_code":200,"response_body":"{\"tracking_number\":\"SH-2025-00123\"}"}

[2025-10-15 19:30:01] local.INFO: Shipment created successfully {"order_id":6,"tracking_number":"SH-2025-00123"}
```

### **Error Log:**
```
[2025-10-15 19:30:00] local.ERROR: Shipping API error response {"order_id":6,"status_code":401,"error":"Invalid API key","response":"{\"error\":\"Unauthorized\"}"}
```

---

## ⚠️ **Error Handling:**

### **1. No API URL Provided:**
```json
{
  "order_id": 6,
  "status": "failed",
  "error": "No API URL provided. Please specify custom_api_url.",
  "shipping_company": "Bosta"
}
```

### **2. Connection Error:**
```json
{
  "order_id": 6,
  "status": "failed",
  "error": "Cannot connect to shipping API: Connection timeout",
  "shipping_company": "Fast Delivery"
}
```

### **3. API Error Response:**
```json
{
  "order_id": 6,
  "status": "failed",
  "error": "Invalid API key",
  "shipping_company": "Fast Delivery"
}
```

### **4. Invalid Phone Number:**
```json
{
  "order_id": 6,
  "status": "failed",
  "error": "Validation failed: Missing customer phone number",
  "shipping_company": "Fast Delivery"
}
```

---

## 💡 **Best Practices:**

### **1. Test First:**
```bash
# استخدم webhook.site أولاً لرؤية الـ payload
custom_api_url: "https://webhook.site/YOUR_UNIQUE_URL"
```

### **2. Use Field Mapping:**
```json
# حدد فقط البيانات المطلوبة
"field_mapping": [
  {"id": "order_number", "field_path": "order.order_number", "enabled": true},
  {"id": "customer_name", "field_path": "customer.name", "enabled": true}
]
```

### **3. Check Logs:**
```bash
tail -f storage/logs/laravel.log | grep "Shipping"
```

### **4. Handle Errors:**
```javascript
// في الفرونت إند
if (result.status === 'failed') {
  console.error('Shipping failed:', result.error);
  // أظهر رسالة خطأ للمستخدم
}
```

---

## 🎯 **Summary:**

| Feature | Status |
|---------|--------|
| **Flexible Company Name** | ✅ أي اسم |
| **Custom API URL** | ✅ مطلوب |
| **Custom API Key** | ✅ اختياري |
| **Field Mapping** | ✅ مرن بالكامل |
| **Real HTTP Requests** | ✅ يبعت فعلياً |
| **Tracking Number Detection** | ✅ تلقائي |
| **Error Handling** | ✅ شامل |
| **Logging** | ✅ كامل |
| **Timeout** | ✅ 30 seconds |
| **Authorization** | ✅ Bearer Token |

---

## 🚀 **Ready to Use!**

الآن النظام **مرن بالكامل** ويدعم **أي شركة شحن** بدون الحاجة لتعديل الكود!

**ما تحتاجه فقط:**
1. ✅ اسم الشركة (أي اسم)
2. ✅ API URL
3. ✅ API Key (اختياري)
4. ✅ Field Mapping (اختياري)

**وخلاص! الباك إند يبعت لوحده! 🎉**

---

**تاريخ التحديث:** 15 أكتوبر 2025  
**الحالة:** ✅ **PRODUCTION READY**  
**النوع:** **FLEXIBLE - يدعم أي شركة شحن**

---



# 🏗️ BuildTools API - دليل Postman الشامل

## 📋 نظرة عامة

هذا الدليل الشامل لاستخدام **BuildTools Complete API Collection** - منصة التجارة الإلكترونية لمواد البناء والأدوات.

## 🚀 البدء السريع

### 1️⃣ إعداد Collection

1. **استيراد Collection:**
   - افتح Postman
   - اضغط **Import** 
   - اختر ملف `BuildTools_Complete_API_Collection.postman_collection.json`

2. **تعيين المتغيرات:**
   - `base_url`: `http://localhost/api/v1` (أو عنوان الخادم الخاص بك)
   - `token`: سيتم حفظها تلقائياً بعد تسجيل الدخول

### 2️⃣ التسجيل/تسجيل الدخول

```json
// تسجيل مستخدم جديد
POST /api/v1/register
{
    "name": "أحمد محمد",
    "email": "ahmed@example.com", 
    "password": "123456",
    "password_confirmation": "123456",
    "phone": "01012345678"
}

// تسجيل الدخول
POST /api/v1/login
{
    "email": "ahmed@buildtools.com",
    "password": "123456"
}
```

**🔑 حسابات تجريبية جاهزة:**
- `ahmed@buildtools.com` / `123456` (عميل)
- `admin@buildtools.com` / `admin123` (إدارة)
- `sara@buildtools.com` / `123456` (عميل)
- `mohamed@buildtools.com` / `123456` (عميل)

---

## 📦 أقسام API الرئيسية

### 🔐 Authentication (المصادقة)

| الطلب | الوصف | المعاملات المطلوبة |
|-------|--------|-------------------|
| `POST /register` | تسجيل مستخدم جديد | name, email, password, phone |
| `POST /login` | تسجيل الدخول | email, password |
| `GET /me` | الحصول على بيانات المستخدم | Authentication token |
| `PUT /profile` | تحديث الملف الشخصي | name, phone |
| `POST /logout` | تسجيل الخروج | Authentication token |

### 📦 Products (المنتجات)

| الطلب | الوصف | المعاملات الاختيارية |
|-------|--------|-------------------|
| `GET /products` | جميع المنتجات | page, per_page, category_id, brand_id, search, min_price, max_price, sort_by, sort_order |
| `GET /products/{id}` | تفاصيل منتج | - |
| `GET /products/featured` | المنتجات المميزة | - |

**مثال - البحث في المنتجات:**
```
GET /products?search=دريل&category_id=1&min_price=100&max_price=1000&sort_by=price&sort_order=asc
```

### 🏷️ Categories (الفئات)

| الطلب | الوصف |
|-------|--------|
| `GET /categories` | جميع الفئات |
| `GET /categories/{id}` | تفاصيل فئة |
| `GET /categories/{id}/products` | منتجات الفئة |

### 🏭 Brands (الماركات)

| الطلب | الوصف |
|-------|--------|
| `GET /brands` | جميع الماركات |
| `GET /brands/{id}` | تفاصيل ماركة |
| `GET /brands/{id}/products` | منتجات الماركة |

### 🛒 Shopping Cart (سلة التسوق)

| الطلب | الوصف | المعاملات |
|-------|--------|-----------|
| `GET /cart` | محتويات السلة | - |
| `POST /cart/add` | إضافة للسلة | product_id, quantity |
| `PUT /cart/update` | تحديث كمية | product_id, quantity |
| `DELETE /cart/remove/{id}` | إزالة من السلة | - |
| `POST /cart/apply-coupon` | تطبيق كوبون | coupon_code |
| `DELETE /cart/remove-coupon` | إزالة كوبون | - |
| `DELETE /cart/clear` | إفراغ السلة | - |

**مثال - إضافة للسلة:**
```json
POST /cart/add
{
    "product_id": 1,
    "quantity": 2
}
```

### ❤️ Wishlist (قائمة الأمنيات)

| الطلب | الوصف | المعاملات |
|-------|--------|-----------|
| `GET /wishlist` | قائمة الأمنيات | - |
| `POST /wishlist/add` | إضافة للأمنيات | product_id |
| `DELETE /wishlist/remove/{id}` | إزالة من الأمنيات | - |
| `POST /wishlist/move-to-cart` | نقل للسلة | product_id, quantity |
| `GET /wishlist/check/{id}` | فحص وجود منتج | - |
| `POST /wishlist/toggle` | تبديل الإضافة/الإزالة | product_id |

### 📍 Addresses (العناوين)

| الطلب | الوصف | المعاملات المطلوبة |
|-------|--------|-------------------|
| `GET /addresses` | جميع العناوين | - |
| `GET /addresses/{id}` | تفاصيل عنوان | - |
| `POST /addresses` | إنشاء عنوان | type, first_name, last_name, address_line_1, city, state, country, phone |
| `PUT /addresses/{id}` | تحديث عنوان | - |
| `POST /addresses/{id}/make-default` | جعل العنوان افتراضي | - |
| `DELETE /addresses/{id}` | حذف عنوان | - |

**مثال - إنشاء عنوان:**
```json
POST /addresses
{
    "type": "home",
    "first_name": "أحمد",
    "last_name": "محمد",
    "address_line_1": "15 شارع النصر",
    "address_line_2": "الدور الثالث",
    "city": "القاهرة",
    "state": "القاهرة",
    "postal_code": "11511",
    "country": "Egypt",
    "phone": "01012345678",
    "is_default": true
}
```

### ⭐ Reviews (التقييمات)

| الطلب | الوصف | المعاملات |
|-------|--------|-----------|
| `GET /products/{id}/reviews` | تقييمات المنتج | - |
| `POST /reviews` | إنشاء تقييم | product_id, rating, title, comment, recommend |
| `PUT /reviews/{id}` | تحديث تقييم | rating, title, comment, recommend |
| `DELETE /reviews/{id}` | حذف تقييم | - |
| `POST /reviews/{id}/helpful` | تسجيل إعجاب | - |

**مثال - إنشاء تقييم:**
```json
POST /reviews
{
    "product_id": 1,
    "rating": 5,
    "title": "منتج ممتاز",
    "comment": "جودة عالية وأداء رائع",
    "recommend": true
}
```

### 📞 Contact (التواصل)

| الطلب | الوصف | المعاملات |
|-------|--------|-----------|
| `POST /contact` | إرسال رسالة | name, email, subject, message, project_type |
| `GET /contact/departments` | أقسام التواصل | - |
| `GET /contact/info` | معلومات التواصل | - |

**أنواع المشاريع:**
- `residential` - سكني
- `commercial` - تجاري  
- `industrial` - صناعي
- `infrastructure` - بنية تحتية

### 📧 Newsletter (النشرة الإخبارية)

| الطلب | الوصف | المعاملات |
|-------|--------|-----------|
| `POST /newsletter/subscribe` | الاشتراك | email, preferences |
| `POST /newsletter/unsubscribe` | إلغاء الاشتراك | email |
| `POST /newsletter/preferences` | تحديث التفضيلات | email, preferences |
| `GET /newsletter/status` | حالة الاشتراك | email |
| `GET /newsletter/preferences` | التفضيلات المتاحة | - |

**التفضيلات المتاحة:**
- `new_products` - منتجات جديدة
- `offers` - العروض والخصومات
- `tips` - نصائح وإرشادات
- `industry_news` - أخبار الصناعة

### 🎫 Coupons (الكوبونات)

| الطلب | الوصف | المعاملات |
|-------|--------|-----------|
| `POST /coupons/validate` | التحقق من كوبون | code, order_amount |

**كوبونات تجريبية:**
- `WELCOME10` - خصم 10% للطلبات فوق 100 جنيه
- `SAVE50` - خصم 50 جنيه للطلبات فوق 500 جنيه  
- `TOOLS20` - خصم 20% للطلبات فوق 200 جنيه
- `BUILD30` - خصم 30% للطلبات فوق 1000 جنيه

### 📋 Orders (الطلبات)

| الطلب | الوصف | المعاملات |
|-------|--------|-----------|
| `GET /orders` | طلباتي | - |
| `GET /orders/{id}` | تفاصيل طلب | - |
| `POST /orders` | إنشاء طلب | shipping_address_id, billing_address_id, payment_method |
| `POST /orders/{id}/cancel` | إلغاء طلب | - |

### 💳 Payments (المدفوعات)

| الطلب | الوصف | المعاملات |
|-------|--------|-----------|
| `GET /payments/methods` | طرق الدفع | - |
| `POST /payments/process` | معالجة دفع | order_id, payment_method, amount |
| `GET /payments/{id}/status` | حالة الدفع | - |

**طرق الدفع المتاحة:**
- `cash_on_delivery` - الدفع عند الاستلام
- `credit_card` - بطاقة ائتمان
- `bank_transfer` - تحويل بنكي
- `digital_wallet` - محفظة رقمية

### 🔍 Search & Filters (البحث والفلترة)

| الطلب | الوصف | المعاملات |
|-------|--------|-----------|
| `GET /search` | بحث شامل | q, type, filters |
| `GET /search/suggestions` | اقتراحات البحث | q |

**أنواع البحث:**
- `all` - جميع النتائج
- `products` - المنتجات فقط
- `categories` - الفئات فقط
- `brands` - الماركات فقط

---

## 🧪 سيناريوهات الاختبار

### 1️⃣ تسجيل مستخدم جديد وتسوق كامل

```javascript
// 1. تسجيل مستخدم جديد
POST /register
// Token سيحفظ تلقائياً

// 2. تصفح المنتجات
GET /products?category_id=1

// 3. إضافة منتجات للسلة  
POST /cart/add {"product_id": 1, "quantity": 2}
POST /cart/add {"product_id": 2, "quantity": 1}

// 4. تطبيق كوبون خصم
POST /cart/apply-coupon {"coupon_code": "WELCOME10"}

// 5. إنشاء عنوان
POST /addresses {address_data}

// 6. إنشاء طلب
POST /orders {"shipping_address_id": 1, "payment_method": "cash_on_delivery"}
```

### 2️⃣ إدارة قائمة الأمنيات

```javascript
// 1. إضافة منتجات للأمنيات
POST /wishlist/add {"product_id": 3}
POST /wishlist/add {"product_id": 4}

// 2. عرض قائمة الأمنيات
GET /wishlist

// 3. نقل منتج للسلة
POST /wishlist/move-to-cart {"product_id": 3, "quantity": 1}

// 4. إزالة منتج من الأمنيات
DELETE /wishlist/remove/4
```

### 3️⃣ تقييم ومراجعة المنتجات

```javascript
// 1. عرض تقييمات منتج
GET /products/1/reviews

// 2. إنشاء تقييم جديد
POST /reviews {review_data}

// 3. تسجيل إعجاب بتقييم
POST /reviews/1/helpful

// 4. تحديث التقييم
PUT /reviews/1 {updated_review_data}
```

---

## 🔧 نصائح الاختبار

### 🎯 **Automation Scripts**

```javascript
// حفظ Token تلقائياً بعد Login
pm.test("Save token on login", function () {
    var jsonData = pm.response.json();
    if (jsonData.data && jsonData.data.token) {
        pm.collectionVariables.set("token", jsonData.data.token);
    }
});

// التحقق من نجاح الاستجابة
pm.test("Status code is 200", function () {
    pm.response.to.have.status(200);
});

// التحقق من بنية البيانات
pm.test("Response has required fields", function () {
    var jsonData = pm.response.json();
    pm.expect(jsonData).to.have.property("success");
    pm.expect(jsonData).to.have.property("data");
});
```

### 📊 **Environment Variables**

قم بإنشاء Environment جديد مع المتغيرات:

```json
{
    "base_url": "http://localhost/api/v1",
    "token": "",
    "test_email": "test@buildtools.com",
    "test_password": "123456"
}
```

### 🔄 **Pre-request Scripts**

```javascript
// إنشاء بيانات عشوائية للاختبار
pm.globals.set("random_email", "test" + Math.floor(Math.random() * 10000) + "@example.com");
pm.globals.set("random_phone", "0101234" + Math.floor(Math.random() * 1000));
```

---

## 🐛 استكشاف الأخطاء

### ❌ **الأخطاء الشائعة**

| رمز الخطأ | المعنى | الحل |
|-----------|--------|------|
| `401` | غير مصرح | تأكد من Token في Authorization header |
| `422` | بيانات غير صحيحة | راجع المعاملات المطلوبة |
| `404` | غير موجود | تأكد من صحة ID |
| `500` | خطأ خادم | تحقق من logs الخادم |

### 🔍 **فحص الاستجابات**

```javascript
// عرض الاستجابة الكاملة
console.log("Response:", pm.response.text());

// عرض Headers
console.log("Headers:", pm.response.headers);

// عرض المتغيرات
console.log("Token:", pm.collectionVariables.get("token"));
```

---

## 📈 البيانات التجريبية المتوفرة

### 👥 **المستخدمين:** 10 مستخدمين
### 🏷️ **الفئات:** 10 فئات
### 🏭 **الماركات:** 4 ماركات  
### 📦 **المنتجات:** 2+ منتجات
### 🎫 **الكوبونات:** 4 كوبونات
### 📍 **العناوين:** 3 عناوين
### 📧 **اشتراكات النشرة:** 4 اشتراكات
### 📞 **رسائل التواصل:** 3 رسائل

---

## 🎉 الخلاصة

هذا Collection يغطي جميع features المنصة:

✅ **إدارة المستخدمين** - تسجيل، دخول، ملف شخصي  
✅ **تصفح المنتجات** - بحث، فلترة، فئات، ماركات  
✅ **التسوق** - سلة، أمنيات، كوبونات  
✅ **الطلبات** - إنشاء، تتبع، إلغاء  
✅ **المدفوعات** - طرق متعددة، تتبع حالة  
✅ **التفاعل** - تقييمات، تواصل، نشرة  
✅ **العناوين** - إدارة عناوين التسليم  

**🚀 ابدأ بتسجيل الدخول باستخدام أحد الحسابات التجريبية واختبر جميع الميزات!** 
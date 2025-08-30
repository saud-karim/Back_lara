# 🏗️ BuildTools BS - Laravel Backend API

![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![API](https://img.shields.io/badge/REST_API-Complete-00D9FF?style=for-the-badge)

## 🎯 مقدمة

**BuildTools BS Backend** هو نظام إدارة متكامل لمتجر إلكتروني متخصص في مواد البناء والإنشاءات، مطور باستخدام Laravel 10 مع دعم كامل للغة العربية والإنجليزية.

### ✨ المميزات الرئيسية

- 🔐 **نظام مصادقة متقدم** - JWT + Laravel Sanctum
- 🛍️ **إدارة شاملة للمنتجات** - مع رفع الصور والمواصفات
- 📂 **تصنيفات ديناميكية** - مع إحصائيات المنتجات
- 🛒 **سلة تسوق ذكية** - مع كوبونات الخصم
- 📦 **إدارة الطلبات** - من الإنشاء للتتبع
- 👑 **لوحة تحكم إدارية** - مع إحصائيات شاملة
- 🌍 **دعم متعدد اللغات** - عربي/إنجليزي
- 📤 **FormData Upload** - رفع الصور والملفات
- 🔒 **أمان متقدم** - RBAC + CORS + Validation

---

## 🚀 البدء السريع

### المتطلبات

- **PHP** 8.2 أو أحدث
- **MySQL** 8.0 أو أحدث  
- **Composer** 2.0+
- **Node.js** 18+ (للـ frontend assets)

### التثبيت

```bash
# 1. استنساخ المشروع
git clone https://github.com/saud-karim/Back_lara.git
cd Back_lara

# 2. تثبيت dependencies
composer install

# 3. إعداد البيئة
cp .env.example .env
php artisan key:generate

# 4. إعداد قاعدة البيانات
# عدّل .env مع بيانات قاعدة البيانات
php artisan migrate --seed

# 5. إنشاء symbolic link للتخزين
php artisan storage:link

# 6. إنشاء مجلد الصور
mkdir -p public/images/products
chmod 755 public/images/products

# 7. تشغيل الخادم
php artisan serve
```

### ⚙️ إعداد .env

```bash
APP_NAME="BuildTools BS API"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=buildtools_bs
DB_USERNAME=root
DB_PASSWORD=

# CORS للـ Frontend
FRONTEND_URL=http://localhost:3000
CORS_ALLOWED_ORIGINS="http://localhost:3000,http://localhost:3001,http://localhost:3002"

# Laravel Sanctum
SANCTUM_STATEFUL_DOMAINS=localhost:3000,localhost:3001,localhost:3002,127.0.0.1:3000
```

---

## 📚 التوثيق الشامل

### 📖 دليل APIs للـ Frontend
- **[Frontend API Guide](Frontend_API_Guide.md)** - دليل شامل 4800+ سطر
- **[Postman Collection](BuildTools_BS_API_Collection.postman_collection.json)** - مجموعة كاملة للاختبار
- **[Testing Guide](TESTING_GUIDE.md)** - دليل الاختبار
- **[FormData Guide](FormData_Update_Guide.md)** - دليل رفع الصور

### 🏗️ الهيكل التقني
- **[Laravel Structure](laravel_structure.md)** - بنية المشروع
- **[Backend Plan](bk_plane.md)** - خطة التطوير

---

## 🎯 APIs الجاهزة

### 🔐 Authentication
- ✅ `POST /api/v1/register` - تسجيل حساب جديد
- ✅ `POST /api/v1/login` - تسجيل الدخول
- ✅ `POST /api/v1/logout` - تسجيل الخروج
- ✅ `GET /api/v1/profile` - معلومات المستخدم
- ✅ `PUT /api/v1/profile` - تحديث المعلومات

### 🛍️ Products
- ✅ `GET /api/v1/products` - قائمة المنتجات مع فلاتر
- ✅ `GET /api/v1/products/{id}` - تفاصيل منتج
- ✅ `GET /api/v1/products/featured` - المنتجات المميزة
- ✅ `GET /api/v1/products/search` - البحث المتقدم

### 📂 Categories  
- ✅ `GET /api/v1/categories` - قائمة التصنيفات
- ✅ `GET /api/v1/categories/{id}` - تفاصيل تصنيف + منتجاته
- ✅ `GET /api/v1/categories/statistics` - إحصائيات التصنيفات

### 🛒 Cart & Wishlist
- ✅ `GET /api/v1/cart` - محتويات السلة
- ✅ `POST /api/v1/cart/add` - إضافة للسلة
- ✅ `PUT /api/v1/cart/update` - تحديث الكمية
- ✅ `DELETE /api/v1/cart/remove/{id}` - إزالة من السلة
- ✅ `GET /api/v1/wishlist` - قائمة الأمنيات
- ✅ `POST /api/v1/wishlist/add` - إضافة للأمنيات

### 📦 Orders
- ✅ `POST /api/v1/orders` - إنشاء طلب جديد
- ✅ `GET /api/v1/orders` - طلبات المستخدم
- ✅ `GET /api/v1/orders/{id}` - تفاصيل طلب
- ✅ `POST /api/v1/orders/{id}/cancel` - إلغاء طلب

### 🏢 Admin Dashboard
- ✅ `GET /api/v1/admin/dashboard/stats` - إحصائيات عامة
- ✅ `GET /api/v1/admin/dashboard/recent-activity` - النشاط الحديث
- ✅ `GET /api/v1/admin/dashboard/overview` - نظرة شاملة

### 👑 Admin Products **[FormData مدعوم]**
- ✅ `GET /api/v1/admin/products` - إدارة المنتجات
- ✅ `POST /api/v1/admin/products` - إنشاء منتج (مع رفع صور)
- ✅ `POST /api/v1/admin/products/{id}` + `_method=PUT` - تحديث منتج
- ✅ `PATCH /api/v1/admin/products/{id}/toggle-status` - تبديل الحالة
- ✅ `PATCH /api/v1/admin/products/{id}/toggle-featured` - تبديل المميز
- ✅ `DELETE /api/v1/admin/products/{id}` - حذف منتج

### 📂 Admin Categories
- ✅ `GET /api/v1/admin/categories` - إدارة التصنيفات
- ✅ `POST /api/v1/admin/categories` - إنشاء تصنيف
- ✅ `PUT /api/v1/admin/categories/{id}` - تحديث تصنيف
- ✅ `PATCH /api/v1/admin/categories/{id}/toggle-status` - تبديل الحالة
- ✅ `DELETE /api/v1/admin/categories/{id}` - حذف تصنيف

### 📞 Contact & Newsletter
- ✅ `POST /api/v1/contact` - إرسال رسالة
- ✅ `POST /api/v1/newsletter/subscribe` - اشتراك النشرة
- ✅ `GET /api/v1/suppliers` - قائمة الموردين
- ✅ `GET /api/v1/brands` - قائمة العلامات التجارية

---

## 🗄️ Database Schema

### جداول أساسية
- **users** - المستخدمين والأدوار
- **categories** - تصنيفات المنتجات (متعددة اللغات)
- **products** - المنتجات (متعددة اللغات مع images JSON)
- **product_features** - مميزات المنتجات
- **product_specifications** - مواصفات المنتجات
- **suppliers** - الموردين
- **brands** - العلامات التجارية

### جداول التجارة الإلكترونية
- **orders** - الطلبات
- **order_items** - عناصر الطلبات
- **cart_items** - سلة التسوق
- **wishlist_items** - قائمة الأمنيات
- **coupons** - كوبونات الخصم
- **payments** - المدفوعات
- **shipments** - الشحنات

### جداول أخرى
- **contact_messages** - رسائل التواصل
- **newsletter_subscriptions** - اشتراكات النشرة
- **product_reviews** - تقييمات المنتجات
- **addresses** - عناوين المستخدمين
- **notifications** - الإشعارات
- **cost_calculations** - حاسبة التكلفة

---

## 🔧 حسابات الاختبار

### Admin Account
```
Email: admin@construction.com
Password: password
Role: admin
```

### User Accounts
```
1. أحمد محمد الطيب - ahmed@gmail.com - password
2. فاطمة أحمد السيد - fatima@yahoo.com - password  
3. محمد علي حسن - mohammed@hotmail.com - password
4. مريم سالم عبدالله - mariam@gmail.com - password
5. يوسف خالد أحمد - youssef@outlook.com - password
```

---

## 🧪 الاختبار

### باستخدام Postman
1. استيراد **[BuildTools_BS_API_Collection.postman_collection.json](BuildTools_BS_API_Collection.postman_collection.json)**
2. تعديل `base_url` في variables إلى `http://localhost:8000`
3. تشغيل اختبارات Authentication أولاً للحصول على token
4. اختبار باقي APIs

### باستخدام cURL
```bash
# تسجيل دخول Admin
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@construction.com","password":"password"}'

# جلب إحصائيات Dashboard
curl -X GET http://localhost:8000/api/v1/admin/dashboard/stats \
  -H "Authorization: Bearer YOUR_TOKEN"

# جلب المنتجات
curl -X GET "http://localhost:8000/api/v1/products?lang=ar&page=1" \
  -H "Accept: application/json"
```

---

## 📁 الملفات المهمة

### 🔧 Configuration
- `config/cors.php` - إعدادات CORS للـ Frontend
- `config/sanctum.php` - إعدادات المصادقة
- `app/Http/Middleware/VerifyCsrfToken.php` - استثناءات CSRF للـ APIs

### 🎮 Controllers
- `app/Http/Controllers/Api/` - Public APIs
- `app/Http/Controllers/Admin/` - Admin APIs  
- `app/Http/Resources/` - API Resources للتحويل

### 🗃️ Models
- `app/Models/Product.php` - مع relationships للفيتشرز والمواصفات
- `app/Models/Category.php` - مع product counts
- `app/Models/ProductFeature.php` - **جديد!**
- `app/Models/ProductSpecification.php` - **جديد!**

### 🛣️ Routes
- `routes/api.php` - جميع API routes منظمة بالإصدارات والأدوار

---

## 🔐 الأمان والصلاحيات

### نظام الأدوار (RBAC)
```php
// Admin فقط
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::prefix('admin')->group(function () {
        // Admin APIs
    });
});

// المستخدمين المُسجلين
Route::middleware(['auth:sanctum'])->group(function () {
    // User-specific APIs
});

// عام (بدون مصادقة)
Route::prefix('v1')->group(function () {
    // Public APIs
});
```

### الحماية المُطبقة
- ✅ **JWT Authentication** مع Laravel Sanctum
- ✅ **Role-Based Access Control** باستخدام Spatie Laravel Permission
- ✅ **CORS Configuration** للـ Next.js Frontend
- ✅ **CSRF Disabled** لـ API routes
- ✅ **Request Validation** شامل لجميع endpoints
- ✅ **Rate Limiting** للحماية من التجسس
- ✅ **SQL Injection Protection** عبر Eloquent ORM

---

## 🖼️ FormData Upload System

### إنشاء منتج مع صور
```javascript
const formData = new FormData();

// البيانات الأساسية
formData.append('name_ar', 'اسم المنتج');
formData.append('name_en', 'Product Name');
formData.append('price', '299.99');
formData.append('stock', '50');
formData.append('category_id', '2');
formData.append('supplier_id', '1');

// الصور الجديدة
formData.append('new_images[0]', fileInput.files[0]);
formData.append('new_images[1]', fileInput.files[1]);

// الفيتشرز والمواصفات
formData.append('features', JSON.stringify(['جودة عالية', 'ضمان شامل']));
formData.append('specifications', JSON.stringify([
  {"key": "الوزن", "value": "2.5 كيلو"}
]));

// إرسال
const response = await fetch('/api/v1/admin/products', {
  method: 'POST',
  headers: { 'Authorization': `Bearer ${token}` },
  body: formData
});
```

### تحديث منتج
```javascript
// ⚠️ استخدم POST مع _method=PUT
formData.append('_method', 'PUT');

fetch(`/api/v1/admin/products/${id}`, {
  method: 'POST', // POST وليس PUT!
  headers: { 'Authorization': `Bearer ${token}` },
  body: formData
});
```

---

## 🌍 الدعم متعدد اللغات

### في قاعدة البيانات
```sql
-- المنتجات
name_ar VARCHAR(255) -- الاسم بالعربية
name_en VARCHAR(255) -- الاسم بالإنجليزية  
description_ar TEXT  -- الوصف بالعربية
description_en TEXT  -- الوصف بالإنجليزية
```

### في API Response
```json
{
  "id": 1,
  "name": "مثقاب ديوالت", // Based on lang parameter
  "name_ar": "مثقاب ديوالت",
  "name_en": "DeWalt Drill",
  "description": "مثقاب احترافي...", // Based on lang parameter  
  "description_ar": "مثقاب احترافي...",
  "description_en": "Professional drill..."
}
```

### استخدام اللغة
```javascript
// في الطلبات
GET /api/v1/products?lang=ar  // العربية
GET /api/v1/products?lang=en  // الإنجليزية
GET /api/v1/products          // افتراضي: العربية
```

---

## 📊 إحصائيات المشروع

### 📈 أرقام التطوير
- **207 ملف** تم رفعه
- **35,170+ سطر كود** مكتوب
- **50+ API endpoint** جاهز
- **15+ Model** مع relationships
- **25+ Migration** لقاعدة البيانات
- **4,800+ سطر توثيق** في API Guide

### ✅ حالة APIs
```
Authentication APIs    ✅ 100% Complete
Products APIs          ✅ 100% Complete  
Categories APIs        ✅ 100% Complete
Cart & Wishlist APIs   ✅ 100% Complete
Orders APIs           ✅ 100% Complete
Admin Dashboard APIs   ✅ 100% Complete
Admin Products APIs    ✅ 100% Complete (FormData ✅)
Admin Categories APIs  ✅ 100% Complete
Contact APIs          ✅ 100% Complete
Suppliers & Brands    ✅ 100% Complete
```

---

## 🔧 الصيانة والدعم

### مراقبة الأداء
```bash
# فحص حالة قاعدة البيانات
php artisan db:show

# مراقبة الذاكرة
php artisan queue:monitor

# تنظيف Cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### Logs والتشخيص
```bash
# عرض logs Laravel
tail -f storage/logs/laravel.log

# فحص حالة التطبيق
php artisan about
```

---

## 🚀 الانتشار (Deployment)

### Requirements للـ Production
- **PHP** 8.2+ مع extensions: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML
- **MySQL** 8.0+ أو **PostgreSQL** 14+
- **Redis** 6.0+ للـ caching والـ sessions
- **Web Server**: Nginx أو Apache مع URL rewriting

### خطوات الانتشار
```bash
# 1. استنساخ المشروع
git clone https://github.com/saud-karim/Back_lara.git
cd Back_lara

# 2. Production dependencies
composer install --optimize-autoloader --no-dev

# 3. إعداد البيئة
cp .env.example .env
# تعديل .env للـ production settings

# 4. تحسين للـ production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. قاعدة البيانات
php artisan migrate --force
php artisan db:seed --force

# 6. صلاحيات الملفات
chmod -R 755 storage bootstrap/cache
```

---

## 🤝 المساهمة

### تطوير المميزات
1. Fork الـ repository
2. إنشاء feature branch (`git checkout -b feature/new-feature`)
3. Commit التغييرات (`git commit -am 'Add new feature'`)
4. Push للـ branch (`git push origin feature/new-feature`)
5. إنشاء Pull Request

### معايير الكود
- اتبع **Laravel Coding Standards**
- استخدم **PSR-12** coding style
- اكتب **unit tests** للـ features الجديدة
- وثّق APIs في `Frontend_API_Guide.md`

---

## 📞 الدعم والتواصل

### مشاكل شائعة

#### CORS Errors
```bash
# في .env
CORS_ALLOWED_ORIGINS="http://localhost:3000,http://localhost:3001"

# أو تحديث config/cors.php
'allowed_origins' => ['*'] // للتطوير فقط
```

#### Database Connection
```bash
# فحص الاتصال
php artisan tinker
>>> DB::connection()->getPdo();
```

#### File Permissions
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod -R 755 public/images
```

---

## 📄 الترخيص

هذا المشروع مرخص تحت **MIT License** - راجع [LICENSE](LICENSE) للتفاصيل.

---

## 🏆 المطورون

- **Backend Developer**: [saud-karim](https://github.com/saud-karim)
- **API Design**: Complete REST API with OpenAPI documentation
- **Frontend Support**: Comprehensive guide with React/Next.js examples

---

## 📈 الإصدارات

### v1.0.0 - Initial Release ✅
- ✅ Complete Authentication System
- ✅ Products & Categories Management  
- ✅ Admin Dashboard with Statistics
- ✅ FormData Upload for Images
- ✅ RBAC Security System
- ✅ Multilingual Support (AR/EN)
- ✅ Comprehensive API Documentation

### المميزات القادمة 🔮
- 🚀 Advanced Search with Elasticsearch
- 🚀 Real-time Notifications with WebSockets  
- 🚀 Advanced Analytics Dashboard
- 🚀 Mobile App API Extensions
- 🚀 AI-powered Recommendations
- 🚀 Advanced Inventory Management

---

**🎯 Ready for Production - تم تطوير Backend بالكامل وجاهز للاستخدام!** 🚀

![BuildTools BS](https://img.shields.io/badge/BuildTools_BS-Backend_Complete-28a745?style=for-the-badge&logo=laravel)

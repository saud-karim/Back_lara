# 🛒 **Shop Management System**
## نظام إدارة المتجر الإلكتروني المتكامل

---

## 📋 **نظرة عامة على المشروع**

نظام إدارة متجر إلكتروني متكامل مبني بـ Laravel Backend يدعم:
- 🌐 **متعدد اللغات** (العربية والإنجليزية)
- 👥 **إدارة العملاء والمنتجات**
- 🛒 **نظام الطلبات والمراجعات**
- 📊 **لوحة تحكم شاملة**
- 🔐 **نظام مصادقة متقدم**

---

## 🚀 **متطلبات التشغيل**

### **المتطلبات الأساسية:**
```
✅ PHP >= 8.1
✅ Composer
✅ MySQL >= 8.0
✅ Laravel >= 10.x
✅ Node.js >= 16.x (للـ Frontend)
```

### **الأدوات المُوصى بها:**
```
🔧 Laragon/XAMPP/WAMP للتطوير المحلي
🔧 Postman لاختبار APIs
🔧 VS Code مع ملحقات Laravel
```

---

## ⚡ **إعداد المشروع - دليل سريع**

### **1️⃣ تحميل وإعداد المشروع:**
```bash
# استنساخ المشروع
git clone [repository-url] shop
cd shop

# تثبيت التبعيات
composer install
npm install

# إعداد ملف البيئة
cp .env.example .env
php artisan key:generate
```

### **2️⃣ إعداد قاعدة البيانات:**
```bash
# إنشاء قاعدة البيانات
mysql -u root -p
CREATE DATABASE laravel;
exit

# تحديث ملف .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=your_password

# تشغيل المايجريشن
php artisan migrate
php artisan db:seed
```

### **3️⃣ تشغيل المشروع:**
```bash
# تشغيل الخادم
php artisan serve

# في نافذة جديدة (إذا كان هناك frontend)
npm run dev
```

---

## 👤 **حسابات الاختبار الجاهزة**

### **🔑 حسابات الإدارة:**
```
👑 المدير الرئيسي:
   📧 admin@construction.com
   🔑 password

👑 مدير فرعي:
   📧 user1@outlook.com  
   🔑 password
```

### **👤 حسابات العملاء:**
```
👤 عميل اختبار:
   📧 customer@example.com
   🔑 password

👤 أحمد محمد:
   📧 ahmed@example.com
   🔑 password
```

---

## 📊 **البيانات التجريبية المتوفرة**

### **📈 الإحصائيات:**
```
✅ المستخدمين: 93 حساب (5 أدمن + 88 عميل)
✅ المنتجات: 31 منتج متنوع
✅ الفئات: 22 فئة مختلفة  
✅ البراندات: 13 براند مشهور
✅ الموردين: 3 موردين رئيسيين
✅ الطلبات: 26 طلب للاختبار
✅ التقييمات: بيانات تقييمات شاملة
```

### **🌐 البيانات متعددة اللغات:**
```
📝 جميع المنتجات لها أسماء ووصف بالعربية والإنجليزية
📝 الفئات والبراندات مترجمة بالكامل
📝 واجهة المستخدم تدعم تبديل اللغة ديناميكياً
```

---

## 🔗 **APIs المتاحة والموثقة**

### **📚 الأدلة الشاملة:**
- 📖 `Frontend_Wishlist_Complete_Guide.md` - دليل التفاعل مع نظام المفضلة
- 📖 `Frontend_API_Guide.md` - دليل شامل لجميع APIs
- 📖 ملفات أخرى موثقة في المجلد

### **🚀 APIs الرئيسية:**

#### **🔐 المصادقة:**
```http
POST /api/v1/register    # تسجيل مستخدم جديد
POST /api/v1/login       # تسجيل الدخول
POST /api/v1/logout      # تسجيل الخروج
GET  /api/v1/profile     # ملف المستخدم
```

#### **🛍️ المنتجات:**
```http
GET  /api/v1/products           # قائمة المنتجات
GET  /api/v1/products/{id}      # تفاصيل منتج
GET  /api/v1/categories         # الفئات
GET  /api/v1/brands            # البراندات
```

#### **👑 إدارة المنتجات (Admin):**
```http
GET    /api/v1/admin/products        # قائمة إدارية للمنتجات
POST   /api/v1/admin/products        # إضافة منتج جديد
GET    /api/v1/admin/products/{id}   # تفاصيل منتج للإدارة
PUT    /api/v1/admin/products/{id}   # تحديث منتج
DELETE /api/v1/admin/products/{id}   # حذف منتج
```

#### **👥 إدارة العملاء (Admin):**
```http
GET  /api/v1/admin/customers         # قائمة العملاء
GET  /api/v1/admin/customers/stats   # إحصائيات العملاء
GET  /api/v1/admin/customers/{id}    # تفاصيل عميل
PUT  /api/v1/admin/customers/{id}    # تحديث بيانات عميل
```

#### **⭐ المراجعات:**
```http
GET   /api/v1/reviews                # جميع المراجعات
POST  /api/v1/reviews                # إضافة مراجعة
GET   /api/v1/products/{id}/reviews  # مراجعات منتج محدد
```

---

## 🧪 **اختبار المشروع**

### **🔧 Curl Commands للاختبار:**

#### **1️⃣ تسجيل الدخول:**
```bash
curl -X POST "http://127.0.0.1:8000/api/v1/login" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"admin@construction.com","password":"password"}'
```

#### **2️⃣ جلب المنتجات:**
```bash
curl -X GET "http://127.0.0.1:8000/api/v1/products" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

#### **3️⃣ إدارة المنتجات (Admin):**
```bash
curl -X GET "http://127.0.0.1:8000/api/v1/admin/products" \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -H "Accept: application/json"
```

### **📱 Postman Collection:**
يمكن استيراد جميع APIs في Postman باستخدام:
```
Base URL: http://127.0.0.1:8000/api/v1
Authorization: Bearer Token
```

---

## 🔧 **إعدادات متقدمة**

### **📁 هيكل المشروع:**
```
shop/
├── app/
│   ├── Http/Controllers/     # المتحكمات
│   ├── Models/              # النماذج
│   ├── Http/Middleware/     # الوسطاء
│   └── Http/Resources/      # موارد API
├── database/
│   ├── migrations/          # قواعد البيانات
│   └── seeders/            # البيانات التجريبية
├── routes/
│   └── api.php             # مسارات API
└── storage/                # ملفات التخزين
```

### **🔐 إعدادات الأمان:**
```php
# في config/cors.php
'paths' => ['api/*'],
'allowed_methods' => ['*'],
'allowed_origins' => ['*'], // في الإنتاج: حدد النطاقات المسموحة
'allowed_headers' => ['*'],
```

### **📊 قاعدة البيانات:**
```sql
-- الجداول الرئيسية:
users, products, categories, brands, suppliers
orders, order_items, reviews, payments
```

---

## 🚨 **استكشاف الأخطاء**

### **❌ أخطاء شائعة وحلولها:**

#### **1️⃣ خطأ 404 في APIs:**
```bash
# تأكد من تشغيل الخادم
php artisan serve

# تحقق من ملف routes/api.php
```

#### **2️⃣ خطأ 500 في قاعدة البيانات:**
```bash
# تحقق من اتصال قاعدة البيانات
php artisan migrate:status

# إعادة تشغيل المايجريشن
php artisan migrate:fresh --seed
```

#### **3️⃣ خطأ 403 Forbidden:**
```bash
# تأكد من صحة Token
# تأكد من صلاحيات المستخدم (admin/customer)
```

#### **4️⃣ مشاكل CORS:**
```bash
# تأكد من إعدادات CORS في config/cors.php
# أو استخدم هذا الأمر:
php artisan config:clear
```

---

## 📞 **الدعم والتوثيق**

### **📚 موارد إضافية:**
- 📖 Laravel Documentation: https://laravel.com/docs
- 📖 API Documentation: موجودة في ملفات MD المرفقة
- 📖 Frontend Integration Guides: أدلة شاملة متوفرة

### **🛠️ أدوات التطوير:**
```bash
# أوامر Laravel مفيدة:
php artisan route:list        # عرض جميع المسارات
php artisan tinker           # وحدة تحكم تفاعلية
php artisan migrate:status   # حالة قاعدة البيانات
php artisan config:clear     # مسح الذاكرة المؤقتة
```

---

## 📈 **ميزات المشروع المتقدمة**

### **✨ الميزات المميزة:**
```
🌐 دعم كامل للغة العربية والإنجليزية
🔐 نظام مصادقة JWT متقدم
📊 لوحة تحكم شاملة مع إحصائيات
🛒 نظام طلبات متكامل
⭐ نظام مراجعات وتقييمات
📱 APIs موثقة ومختبرة
🔧 أكواد خطأ واضحة ومعالجة شاملة
📂 رفع وإدارة الصور
🔍 بحث وتصفية متقدم
📄 pagination للبيانات الكبيرة
```

### **🎯 جاهز للإنتاج:**
```
✅ البيانات التجريبية مكتملة ومفيدة
✅ حسابات اختبار متعددة ومتنوعة  
✅ APIs مختبرة وتعمل بشكل صحيح
✅ توثيق شامل ومفصل
✅ معالجة أخطاء احترافية
✅ هيكل كود منظم وقابل للصيانة
```

---

## 🎉 **ملاحظات نهائية**

### **🚀 للبدء السريع:**
1. ✅ **اتبع خطوات الإعداد** أعلاه
2. ✅ **استخدم حسابات الاختبار** المتوفرة
3. ✅ **اقرأ الأدلة المرفقة** للتفاصيل
4. ✅ **اختبر APIs** باستخدام Postman أو curl

### **📋 قائمة مراجعة ما قبل التسليم:**
- [x] ✅ قاعدة البيانات مُعبأة بالبيانات التجريبية
- [x] ✅ جميع APIs تعمل وتم اختبارها
- [x] ✅ حسابات Admin و Customer متوفرة  
- [x] ✅ التوثيق شامل ومحدث
- [x] ✅ معالجة الأخطاء مناسبة
- [x] ✅ الدعم متعدد اللغات يعمل
- [x] ✅ أمان APIs وتشفير كلمات المرور

---

**🎯 المشروع جاهز 100% للتسليم والاستخدام!**

*آخر تحديث: ديسمبر 2024*

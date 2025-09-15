# ⚡ **دليل الإعداد السريع - 5 دقائق**
## Quick Setup Guide for Shop Management System

---

## 🚀 **خطوات الإعداد السريع (5 دقائق فقط!)**

### **1️⃣ متطلبات أساسية (تأكد من وجودها):**
```bash
✅ PHP >= 8.1
✅ Composer  
✅ MySQL
✅ Laragon/XAMPP (أو أي خادم محلي)
```

### **2️⃣ إعداد المشروع:**
```bash
# انتقل لمجلد المشروع
cd shop

# تثبيت التبعيات
composer install

# نسخ ملف البيئة
copy .env.example .env

# توليد مفتاح التطبيق
php artisan key:generate
```

### **3️⃣ إعداد قاعدة البيانات:**

#### **أ) إنشاء قاعدة البيانات:**
```sql
-- في MySQL Command Line أو phpMyAdmin:
CREATE DATABASE laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

#### **ب) تحديث ملف .env:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=          # ضع كلمة مرور MySQL هنا (فارغة في Laragon)
```

#### **ج) تشغيل قاعدة البيانات:**
```bash
# تشغيل المايجريشن مع البيانات التجريبية
php artisan migrate --seed
```

### **4️⃣ تشغيل المشروع:**
```bash
# تشغيل الخادم
php artisan serve

# النتيجة: http://127.0.0.1:8000
```

---

## 🧪 **اختبار سريع - تأكد من عمل كل شيء:**

### **1️⃣ اختبار تسجيل الدخول:**
```bash
curl -X POST "http://127.0.0.1:8000/api/v1/login" \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"admin@construction.com\",\"password\":\"password\"}"
```

**النتيجة المتوقعة:** رسالة نجاح مع token

### **2️⃣ اختبار جلب المنتجات:**
```bash
# استبدل YOUR_TOKEN بالـ token من الخطوة السابقة
curl -X GET "http://127.0.0.1:8000/api/v1/products" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**النتيجة المتوقعة:** قائمة بـ 31 منتج

---

## 👤 **حسابات الاختبار الجاهزة:**

### **🔑 حسابات Admin:**
```
📧 admin@construction.com
🔑 password
---
📧 user1@outlook.com  
🔑 password
```

### **👤 حسابات Customer:**
```
📧 customer@example.com
🔑 password
---
📧 ahmed@example.com
🔑 password
```

---

## 📊 **البيانات التجريبية المتوفرة:**

```
✅ 93 مستخدم (5 أدمن + 88 عميل)
✅ 31 منتج متنوع  
✅ 22 فئة مختلفة
✅ 13 براند مشهور
✅ 26 طلب للاختبار
✅ بيانات تقييمات ومراجعات
✅ جميع البيانات بالعربية والإنجليزية
```

---

## 🔗 **URLs مهمة للاختبار:**

### **🌐 Base URLs:**
```
🖥️  Backend API: http://127.0.0.1:8000/api/v1
📱 Admin Panel: http://127.0.0.1:8000/admin (إذا كان متوفر)
```

### **📋 APIs للاختبار السريع:**
```
GET  /api/v1/products              # المنتجات
GET  /api/v1/categories            # الفئات  
POST /api/v1/login                 # تسجيل دخول
GET  /api/v1/admin/products        # إدارة منتجات (Admin)
GET  /api/v1/admin/customers       # إدارة عملاء (Admin)
```

---

## 🚨 **حل المشاكل الشائعة:**

### **❌ خطأ: "could not find driver"**
```bash
# تأكد من تفعيل MySQL extension في PHP
# في ملف php.ini، تأكد من وجود:
extension=pdo_mysql
extension=mysqli
```

### **❌ خطأ: "Access denied for user"**
```bash
# تحقق من بيانات قاعدة البيانات في .env
# في Laragon: المستخدم root بدون كلمة مرور
# في XAMPP: المستخدم root بدون كلمة مرور (عادة)
```

### **❌ خطأ: "Specified key was too long"**
```bash
# في ملف AppServiceProvider.php، تأكد من وجود:
Schema::defaultStringLength(191);
```

### **❌ APIs تعطي 404**
```bash
# تأكد من تشغيل:
php artisan route:clear
php artisan config:clear
php artisan serve
```

---

## ⚡ **إعداد فائق السرعة (دقيقة واحدة!):**

إذا كان لديك Laragon مُثبت ويعمل:

```bash
# 1. ضع المشروع في C:\laragon\www\shop
# 2. افتح terminal في المجلد:
composer install && copy .env.example .env && php artisan key:generate

# 3. إنشاء DB من Laragon UI أو:
mysql -u root -e "CREATE DATABASE laravel"

# 4. تشغيل قاعدة البيانات:
php artisan migrate --seed

# 5. ✅ جاهز! اختبر: http://shop.test/api/v1/products
```

---

## 🎯 **التحقق من نجاح الإعداد:**

### **✅ علامات النجاح:**
```
1. ✅ php artisan serve يعمل بدون أخطاء
2. ✅ http://127.0.0.1:8000/api/v1/products يُرجع بيانات
3. ✅ تسجيل الدخول مع admin@construction.com يعمل
4. ✅ Database يحتوي على 31 منتج و 93 مستخدم
```

### **🔧 لاستكشاف المزيد:**
```
📖 اقرأ README.md للتفاصيل الكاملة
📖 اقرأ Frontend_API_Guide.md لدليل APIs
🧪 استخدم Postman لاختبار شامل
```

---

**🎉 مبروك! المشروع جاهز للعمل والتطوير!**

*وقت الإعداد الإجمالي: 5 دقائق أو أقل* 
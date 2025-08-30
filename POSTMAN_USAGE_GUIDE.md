# 🚀 دليل استخدام Postman Collection - BuildTools API

## 📥 التحضير

### 1. استيراد الملف
- افتح Postman
- اضغط **Import**
- اختر ملف `BuildTools_Complete_API.postman_collection.json`

### 2. المتغيرات الجاهزة
```
base_url = http://localhost:8000
test_email = user@buildtools.com  
test_password = password123
lang = ar
```

---

## 🔐 الخطوات الأساسية

### الخطوة 1: تسجيل الدخول
```
POST {{base_url}}/api/v1/login
```
- **Body:**
```json
{
    "email": "{{test_email}}",
    "password": "{{test_password}}"
}
```
- **النتيجة:** Token يحفظ تلقائياً في متغير `auth_token`

### الخطوة 2: الحصول على معلومات المستخدم
```
GET {{base_url}}/api/v1/me
Authorization: Bearer {{auth_token}}
```

**✅ هذا الـ endpoint يحتاج Token!**

---

## 🛡️ Authentication المطلوب

### APIs تحتاج Token:
- ✅ `/api/v1/me` - معلومات المستخدم
- ✅ `/api/v1/cart/*` - سلة التسوق  
- ✅ `/api/v1/wishlist/*` - قائمة الأمنيات
- ✅ `/api/v1/orders/*` - الطلبات
- ✅ `/api/v1/addresses/*` - العناوين

### APIs عامة (بدون Token):
- ✅ `/api/v1/products` - المنتجات
- ✅ `/api/v1/categories` - الفئات
- ✅ `/api/v1/brands` - العلامات التجارية
- ✅ `/api/v1/contact` - التواصل

---

## 🚨 حل مشكلة "Unauthenticated"

### السبب:
```json
{
    "message": "Unauthenticated."
}
```

### الحل:
1. **تأكد من تسجيل الدخول أولاً:**
   - شغل `POST /api/v1/login`
   - تأكد من حصولك على `token` في الـ response

2. **تأكد من وجود Header:**
   ```
   Authorization: Bearer {{auth_token}}
   ```

3. **تأكد من صحة الـ token:**
   - Token يبدأ بـ: `number|long_string`
   - مثال: `7|LW6mn5v2cPFGDkWNAH8tDqIebBtJinPXTOPej5So6fb9be27`

---

## 🔄 إعادة الحصول على Token

إذا انتهت صلاحية Token:

### الطريقة 1: تسجيل دخول جديد
```
POST {{base_url}}/api/v1/login
```

### الطريقة 2: Refresh Token  
```
POST {{base_url}}/api/v1/refresh
Authorization: Bearer {{auth_token}}
```

---

## 💡 نصائح للاستخدام

### 1. ترتيب التنفيذ:
1. `POST /api/v1/login` ← احصل على Token
2. `GET /api/v1/me` ← تأكد من المصادقة  
3. باقي APIs حسب الحاجة

### 2. فحص المتغيرات:
- اضغط على **Environment** لرؤية القيم الحالية
- تأكد من `auth_token` ليس فارغاً

### 3. في حالة الأخطاء:
- تأكد من تشغيل Laravel Server: `php artisan serve`
- تأكد من `base_url = http://localhost:8000`

---

## 📊 الـ Collections المتاحة

1. **🔐 Authentication** - تسجيل دخول وخروج
2. **🛍️ Products** - عرض المنتجات
3. **🏷️ Brands** - العلامات التجارية  
4. **🛒 Cart** - سلة التسوق (يحتاج Token)
5. **❤️ Wishlist** - قائمة الأمنيات (يحتاج Token)
6. **📞 Contact** - رسائل التواصل

---

**🎯 الخلاصة:** تأكد من تسجيل الدخول قبل استخدام `/api/v1/me` أو أي API محمي! 
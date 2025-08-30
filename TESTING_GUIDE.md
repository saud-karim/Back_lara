# 🧪 دليل اختبار BuildTools APIs

## 📥 **الخطوة 1: استيراد Collection**
1. افتح **Postman**
2. اضغط **Import**
3. اختر ملف `BuildTools_API_Collection.postman_collection.json`

---

## 🚀 **الخطوة 2: بدء الاختبار**

### 1️⃣ **تسجيل الدخول (إجباري أولاً)**
```
🔐 Authentication → Login - تسجيل الدخول
```
- **سيحفظ Token تلقائياً**
- ✅ بعدها يمكنك استخدام باقي APIs المحمية

### 2️⃣ **اختبار المعلومات الشخصية**
```
🔐 Authentication → Get Profile - معلومات المستخدم
```
- **يتطلب Token من الخطوة السابقة**
- ✅ سيعرض بياناتك

---

## 📋 **APIs المتاحة للاختبار**

### 🔓 **APIs عامة (بدون تسجيل دخول):**
- 🛍️ **Products** - عرض المنتجات
- 📂 **Categories** - فئات المنتجات  
- 🏷️ **Brands** - العلامات التجارية
- 📞 **Contact** - إرسال رسائل تواصل
- 📧 **Newsletter** - الاشتراك في النشرة

### 🔒 **APIs محمية (تحتاج تسجيل دخول):**
- 🔐 **Get Profile** - معلومات المستخدم
- 🛒 **Cart** - سلة التسوق
- ❤️ **Wishlist** - قائمة الأمنيات

---

## ⚙️ **المتغيرات الجاهزة**

```json
{
  "base_url": "http://localhost:8000",
  "test_email": "user@buildtools.com",
  "test_password": "password123",
  "lang": "ar",
  "auth_token": "(يملأ تلقائياً)"
}
```

---

## 🎯 **ترتيب الاختبار المُوصى**

1. **Login** ← احصل على Token
2. **Get Profile** ← تأكد من المصادقة  
3. **Products** ← اختبر APIs العامة
4. **Cart/Wishlist** ← اختبر APIs المحمية

---

## 🚨 **حل المشاكل الشائعة**

### ❌ "Unauthenticated"
- **الحل:** سجل دخول أولاً باستخدام Login API

### ❌ "Connection Error"  
- **الحل:** تأكد من تشغيل Laravel: `php artisan serve`

### ❌ "404 Not Found"
- **الحل:** تأكد من `base_url = http://localhost:8000`

---

## 📊 **ميزات Collection**

✅ **Token Management تلقائي** - يحفظ Token بعد Login  
✅ **Console Logging** - رسائل مفيدة في Console  
✅ **Error Handling** - رسائل خطأ واضحة  
✅ **Auto Variables** - متغيرات جاهزة للاستخدام  

---

**🎉 جاهز للاختبار! ابدأ بـ Login ثم استكشف باقي APIs** 
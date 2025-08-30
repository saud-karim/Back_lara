# 📋 دليل اختبار APIs - مشروع BuildTools

## 🚀 ملفات Postman

### 📁 الملف المتوفر:
**`BuildTools_Complete_API.postman_collection.json`** - ملف واحد شامل يحتوي على كل شيء!

---

## 🔧 إعداد Postman

### **تحميل الملف (خطوة واحدة فقط!)**
```bash
# استيراد الملف الواحد في Postman
1. افتح Postman
2. اضغط على "Import" 
3. اسحب ملف BuildTools_Complete_API.postman_collection.json
4. ✅ تم! جميع المتغيرات والإعدادات جاهزة
```

### 3. **تكوين المتغيرات**
```bash
# المتغيرات الأساسية:
- base_url: http://localhost:8000 (أو عنوان الخادم)
- auth_token: (سيتم ملؤه تلقائياً بعد تسجيل الدخول)
- lang: ar (العربية) أو en (الإنجليزية)
```

---

## 🔐 تسلسل الاختبار الموصى به

### **المرحلة 1: المصادقة**
```bash
1. 🔓 Register - إنشاء حساب جديد
2. 🔑 Login - تسجيل الدخول (سيحفظ Token تلقائياً)
3. 👤 Get Profile - جلب معلومات المستخدم
```

### **المرحلة 2: المنتجات والفئات**
```bash
1. 📂 Get All Categories - جلب الفئات
2. 🛍️ Get All Products - جلب المنتجات
3. 🔍 Search Products - البحث في المنتجات
4. 🏷️ Get All Brands - جلب العلامات التجارية
```

### **المرحلة 3: سلة التسوق**
```bash
1. 🛒 Add to Cart - إضافة منتج للسلة
2. 📋 Get Cart - عرض محتويات السلة
3. ✏️ Update Cart Item - تحديث كمية منتج
4. 🎫 Apply Coupon - تطبيق كوبون خصم
```

### **المرحلة 4: قائمة الأمنيات**
```bash
1. ❤️ Add to Wishlist - إضافة للقائمة
2. 📝 Get Wishlist - عرض القائمة
3. 🔄 Move to Cart - نقل للسلة
4. 🗑️ Remove from Wishlist - إزالة من القائمة
```

### **المرحلة 5: العناوين**
```bash
1. 📍 Add Address - إضافة عنوان
2. 📋 Get User Addresses - عرض العناوين
3. ⭐ Set Default Address - تعيين عنوان افتراضي
```

### **المرحلة 6: الطلبات**
```bash
1. 📦 Create Order - إنشاء طلب
2. 📋 Get User Orders - عرض الطلبات
3. 🔍 Get Order Details - تفاصيل طلب
4. 📍 Track Order - تتبع الشحنة
```

### **المرحلة 7: المدفوعات**
```bash
1. 💳 Process Payment - معالجة الدفع
2. 📊 Payment Status - حالة الدفع
```

### **المرحلة 8: التقييمات**
```bash
1. ⭐ Add Review - إضافة تقييم
2. 📝 Get Product Reviews - عرض تقييمات المنتج
3. 👍 Mark Review Helpful - تقييم مفيد
```

---

## 🌍 اختبار APIs العامة (بدون مصادقة)

### **معلومات الاتصال**
```bash
1. 📞 Get Contact Info - معلومات التواصل
2. 🏢 Get Departments - الأقسام المتاحة
3. 📧 Send Contact Message - إرسال رسالة
```

### **النشرة البريدية**
```bash
1. 📬 Subscribe to Newsletter - الاشتراك
2. ⚙️ Get Available Preferences - التفضيلات المتاحة
3. 📊 Get Newsletter Status - حالة الاشتراك
```

### **الموردين والعلامات التجارية**
```bash
1. 🏭 Get All Suppliers - جلب الموردين
2. 🔍 Get Supplier Details - تفاصيل مورد
3. 🏷️ Get All Brands - العلامات التجارية
4. 📊 Get Brand Products - منتجات علامة تجارية
```

### **حاسبة التكلفة**
```bash
1. 🧮 Calculate Project Cost - حساب تكلفة مشروع
```

---

## 📊 نماذج البيانات للاختبار

### **تسجيل مستخدم جديد:**
```json
{
    "name": "أحمد محمد علي",
    "email": "ahmed.mohamed@example.com",
    "password": "SecurePass123!",
    "password_confirmation": "SecurePass123!",
    "phone": "+201234567890",
    "company": "شركة البناء المتقدم المحدودة"
}
```

### **إضافة عنوان:**
```json
{
    "type": "home",
    "name": "المنزل الرئيسي",
    "phone": "+201234567890",
    "street": "شارع التحرير، حي النصر، برج السكن رقم 15، الطابق الثالث",
    "city": "المنصورة",
    "state": "الدقهلية",
    "postal_code": "35511",
    "country": "مصر",
    "is_default": true
}
```

### **إنشاء طلب:**
```json
{
    "shipping_address": {
        "street": "شارع التحرير، حي النصر",
        "city": "المنصورة",
        "state": "الدقهلية",
        "postal_code": "35511",
        "country": "مصر"
    },
    "payment_method": "credit_card",
    "notes": "الرجاء التوصيل في الفترة الصباحية من 9 ص إلى 12 ظ"
}
```

### **إضافة تقييم:**
```json
{
    "product_id": 1,
    "rating": 5,
    "review": "منتج ممتاز جداً! جودة التصنيع عالية والأداء يفوق التوقعات. التسليم كان سريع والتعبئة ممتازة. أنصح بشرائه بشدة.",
    "images": ["review_photo_1.jpg", "review_photo_2.jpg"]
}
```

---

## 🔍 نصائح للاختبار

### **1. المصادقة التلقائية:**
- عند تسجيل الدخول، سيتم حفظ Token تلقائياً
- تأكد من تفعيل Environment قبل الاختبار

### **2. اختبار اللغات:**
```bash
# تغيير اللغة في Environment:
lang = "ar"  # للعربية
lang = "en"  # للإنجليزية
```

### **3. اختبار البحث:**
```bash
# أمثلة للبحث:
- "مثقاب" (بالعربية)
- "drill" (بالإنجليزية)
- "DeWalt" (علامة تجارية)
```

### **4. رموز الاستجابة:**
```bash
✅ 200 - نجح الطلب
✅ 201 - تم الإنشاء بنجاح
❌ 400 - خطأ في البيانات المرسلة
❌ 401 - غير مُصرح (تحتاج تسجيل دخول)
❌ 403 - ممنوع (لا تملك الصلاحية)
❌ 404 - غير موجود
❌ 422 - خطأ في التحقق من البيانات
❌ 500 - خطأ في الخادم
```

---

## 🚀 البدء السريع

### **خطوات سريعة للاختبار:**

```bash
1. 🔄 استورد Collection + Environment
2. 🔧 فعّل Environment "BuildTools Environment"
3. 🔓 سجل حساب جديد (Register)
4. 🔑 سجل دخول (Login) - سيحفظ Token تلقائياً
5. 🛍️ اختبر المنتجات (Get All Products)
6. 🛒 أضف منتج للسلة (Add to Cart)
7. 📦 أنشئ طلب (Create Order)
8. ⭐ أضف تقييم (Add Review)
```

### **اختبار سريع للـ APIs العامة:**
```bash
1. 🏷️ Get All Brands
2. 📞 Get Contact Info
3. 📧 Get Available Newsletter Preferences
4. 🧮 Calculate Project Cost
```

---

## 📝 ملاحظات مهمة

- ✅ تأكد من تشغيل الخادم قبل الاختبار
- ✅ راجع قاعدة البيانات للتأكد من وجود بيانات تجريبية
- ✅ استخدم بيانات واقعية للاختبار
- ✅ اختبر السيناريوهات المختلفة (نجاح وفشل)
- ✅ راقب Logs الخادم للأخطاء

---

## 🎯 مؤشرات النجاح

### **APIs تعمل بنجاح إذا:**
- ✅ تسجيل الدخول يعيد Token صالح
- ✅ جلب المنتجات يعيد بيانات صحيحة
- ✅ إضافة للسلة تحدث البيانات
- ✅ إنشاء الطلب يعمل بدون أخطاء
- ✅ التبديل بين اللغات يعمل

**🎉 مبروك! APIs جاهزة للاستخدام في الإنتاج** 
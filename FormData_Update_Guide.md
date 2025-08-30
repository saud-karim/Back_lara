# 🖼️ دليل FormData للمنتجات - Backend API Update

## ✅ تم إصلاح مشكلة FormData Upload!

تم إصلاح مشكلة رفع الصور والبيانات باستخدام **FormData** في Laravel Backend. الآن يمكن للـ Frontend إرسال:
- ✅ **الصور الجديدة** كملفات فعلية
- ✅ **الصور الموجودة** كـ JSON array
- ✅ **الفيتشرز** كـ JSON array
- ✅ **المواصفات** كـ JSON array
- ✅ **البيانات النصية** كـ form fields

---

## 🔧 التحديثات المُنجزة

### 1. **AdminProductController**
- ✅ إصلاح `store()` method لدعم FormData
- ✅ إصلاح `update()` method لدعم FormData (باستخدام POST + _method=PUT)
- ✅ إضافة معالجة رفع الصور الجديدة
- ✅ إضافة معالجة الفيتشرز والمواصفات
- ✅ إضافة Database Transactions للأمان

### 2. **Models الجديدة**
- ✅ `ProductFeature` model مع العلاقات
- ✅ `ProductSpecification` model مع العلاقات
- ✅ إضافة relationships في `Product` model

### 3. **Database Schema**
- ✅ `product_features` table
- ✅ `product_specifications` table  
- ✅ Foreign key constraints

### 4. **File Storage**
- ✅ إنشاء مجلد `public/images/products/`
- ✅ تسمية الملفات بـ `timestamp_uniqid.extension`
- ✅ دعم صيغ: JPEG, PNG, JPG, GIF, WebP (حتى 2MB)

---

## 📤 كيفية الاستخدام من Frontend

### إنشاء منتج جديد
```javascript
// إنشاء FormData
const formData = new FormData();

// البيانات الأساسية (مطلوبة)
formData.append('name_ar', 'اسم المنتج بالعربية');
formData.append('name_en', 'Product Name in English');
formData.append('description_ar', 'وصف المنتج بالعربية');
formData.append('description_en', 'Product description in English');
formData.append('price', '299.99');
formData.append('stock', '50');
formData.append('category_id', '2');
formData.append('supplier_id', '1');
formData.append('status', 'active');
formData.append('featured', '1'); // '1' for true, '0' for false

// البيانات الاختيارية
formData.append('original_price', '399.99');
formData.append('brand_id', '1');

// الصور الموجودة (JSON string)
formData.append('existing_images', JSON.stringify([
  '/images/products/existing1.jpg',
  '/images/products/existing2.jpg'
]));

// الصور الجديدة (Files من input)
const fileInput = document.getElementById('images');
for (let i = 0; i < fileInput.files.length; i++) {
  formData.append(`new_images[${i}]`, fileInput.files[i]);
}

// الفيتشرز (JSON string)
formData.append('features', JSON.stringify([
  'جودة ممتازة',
  'ضمان شامل',
  'مقاوم للصدأ'
]));

// المواصفات (JSON string)
formData.append('specifications', JSON.stringify([
  {"key": "الوزن", "value": "2.5 كيلو"},
  {"key": "الأبعاد", "value": "30x20x15 سم"},
  {"key": "المادة", "value": "ستانلس ستيل"}
]));

// إرسال الطلب
const response = await fetch('/api/v1/admin/products', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${adminToken}`
    // ⚠️ لا تضع Content-Type! سيتم تعيينه تلقائياً
  },
  body: formData
});

const result = await response.json();
```

### تحديث منتج موجود
```javascript
// ⚠️ مهم: استخدم POST مع _method=PUT للتحديث
const formData = new FormData();

// إضافة method spoofing
formData.append('_method', 'PUT');

// باقي البيانات نفس الطريقة السابقة...
formData.append('name_ar', 'اسم محدث');
formData.append('name_en', 'Updated Name');
// ... إلخ

// إرسال للـ endpoint نفسه لكن بـ POST
const response = await fetch(`/api/v1/admin/products/${productId}`, {
  method: 'POST', // ⚠️ POST وليس PUT!
  headers: {
    'Authorization': `Bearer ${adminToken}`
  },
  body: formData
});
```

---

## 📋 Validation Rules

### الحقول المطلوبة
- `name_ar` - string, max:255
- `name_en` - string, max:255  
- `description_ar` - string (مطلوب للـ create/update)
- `description_en` - string (مطلوب للـ create/update)
- `price` - numeric, min:0
- `stock` - integer, min:0
- `category_id` - exists:categories,id
- `supplier_id` - exists:suppliers,id
- `status` - in:active,inactive

### الحقول الاختيارية
- `original_price` - numeric, min:0
- `brand_id` - exists:brands,id
- `featured` - boolean (كـ string: '1' أو '0')
- `existing_images` - JSON string
- `new_images.*` - image files (JPEG,PNG,JPG,GIF,WebP, max:2MB)
- `features` - JSON string
- `specifications` - JSON string

---

## 🎯 Response Format

### نجاح العملية
```json
{
  "success": true,
  "message": "تم إنشاء المنتج بنجاح",
  "data": {
    "product": {
      "id": 23,
      "name_ar": "منتج كامل FormData",
      "name_en": "Complete FormData Product",
      "description_ar": "منتج شامل مع صور وفيتشرز",
      "description_en": "Complete product with images and features",
      "price": "599.99",
      "original_price": null,
      "stock": 100,
      "sku": "PRD-1756152744-789",
      "status": "active",
      "featured": true,
      "rating": "0.00",
      "reviews_count": 0,
      "images": [
        "/images/products/old1.jpg",
        "/images/products/old2.jpg",
        "/images/products/1756152745_68acc3a99b7f2.png",
        "/images/products/1756152745_68acc3a99bfde.png"
      ],
      "category": {
        "id": 2,
        "name": "الأدوات والمعدات"
      },
      "supplier": {
        "id": 1,
        "name": "شركة الأدوات المتقدمة"
      },
      "brand": {
        "id": 1,
        "name": "بوش"
      },
      "features": [
        {
          "id": 1,
          "product_id": 23,
          "feature_ar": "جودة ممتازة",
          "feature_en": "جودة ممتازة",
          "sort_order": 1,
          "created_at": "2024-01-15T10:30:00.000000Z",
          "updated_at": "2024-01-15T10:30:00.000000Z"
        }
      ],
      "specifications": [
        {
          "id": 1,
          "product_id": 23,
          "spec_key": "الوزن",
          "spec_value_ar": "3.5 كيلو",
          "spec_value_en": "3.5 كيلو",
          "created_at": "2024-01-15T10:30:00.000000Z",
          "updated_at": "2024-01-15T10:30:00.000000Z"
        }
      ],
      "created_at": "2024-01-15T10:30:00.000000Z",
      "updated_at": "2024-01-15T10:30:00.000000Z"
    }
  }
}
```

### فشل العملية
```json
{
  "success": false,
  "message": "بيانات غير صحيحة",
  "errors": {
    "name_ar": ["اسم المنتج باللغة العربية مطلوب"],
    "price": ["السعر مطلوب"],
    "new_images.0": ["يجب أن تكون الصورة من نوع: jpeg,png,jpg,gif,webp"]
  }
}
```

---

## 🧪 اختبار APIs

### استخدام Postman
1. اختر `POST` method
2. URL: `http://localhost:8000/api/v1/admin/products`
3. Headers: 
   - `Authorization: Bearer {admin_token}`
   - **لا تضع Content-Type!** سيتم تعيينه تلقائياً
4. Body: اختر `form-data`
5. أضف الحقول كما هو موضح أعلاه

### للتحديث
1. اختر `POST` method (وليس PUT!)
2. URL: `http://localhost:8000/api/v1/admin/products/{id}`
3. أضف `_method` = `PUT` في form-data
4. باقي البيانات نفس الطريقة

---

## ⚠️ ملاحظات مهمة

### Boolean Fields
```javascript
// ✅ صحيح
formData.append('featured', '1'); // for true
formData.append('featured', '0'); // for false

// ❌ خطأ
formData.append('featured', true);  // سيتم تحويله لـ "true" string
formData.append('featured', false); // سيتم تحويله لـ "false" string
```

### JSON Fields
```javascript
// ✅ صحيح - JSON string
formData.append('features', JSON.stringify(['feature1', 'feature2']));

// ❌ خطأ - array مباشرة
formData.append('features', ['feature1', 'feature2']);
```

### File Upload
```javascript
// ✅ صحيح
formData.append('new_images[0]', fileObject);

// ❌ خطأ
formData.append('new_images', fileObject); // بدون index
```

### Content-Type Header
```javascript
// ✅ صحيح - لا تضع Content-Type
headers: {
  'Authorization': `Bearer ${token}`
}

// ❌ خطأ - سيكسر multipart boundary
headers: {
  'Authorization': `Bearer ${token}`,
  'Content-Type': 'multipart/form-data'
}
```

---

## 🎉 النتيجة

✅ **تم إصلاح مشكلة FormData بالكامل!**
- ✅ رفع الصور يعمل بنجاح
- ✅ إنشاء المنتجات يعمل بنجاح  
- ✅ تحديث المنتجات يعمل بنجاح
- ✅ الفيتشرز والمواصفات تعمل بنجاح
- ✅ Validation شامل وآمن
- ✅ Error handling كامل

**الـ Frontend الآن يمكنه استخدام FormData بدون أي مشاكل!** 🎉 
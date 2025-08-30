# 🔐 Test Accounts - BuildTools BS

## 📋 جميع حسابات المستخدمين للاختبار

### 🔑 **كلمة المرور الموحدة لجميع الحسابات:**
```
password
```

---

## 👥 **قائمة المستخدمين:**

### 🛡️ **1. Admin User (المدير)**
```json
{
    "id": 1,
    "name": "Admin User",
    "email": "admin@construction.com",
    "password": "password",
    "role": "admin"
}
```
**الصلاحيات:**
- ✅ إدارة جميع المنتجات والفئات
- ✅ إدارة الطلبات والمستخدمين  
- ✅ عرض التقارير والإحصائيات
- ✅ إدارة الكوبونات والعروض

---

### 👤 **2. Customer User (العميل الأساسي)**
```json
{
    "id": 2,
    "name": "Customer User", 
    "email": "customer@construction.com",
    "password": "password",
    "role": "customer"
}
```
**الصلاحيات:**
- ✅ تصفح وشراء المنتجات
- ✅ إدارة السلة وقائمة الأمنيات
- ✅ تقديم الطلبات ومتابعتها
- ✅ إضافة المراجعات والتقييمات
- ✅ إدارة العناوين والملف الشخصي

---

### 🏭 **3. Supplier User (المورد)**
```json
{
    "id": 3,
    "name": "Supplier User",
    "email": "supplier@construction.com", 
    "password": "password",
    "role": "supplier"
}
```
**الصلاحيات:**
- ✅ إدارة منتجات الشركة
- ✅ عرض الطلبات الخاصة بالمنتجات
- ✅ إدارة المخزون

---

### 👷 **4. Ahmed Construction Manager**
```json
{
    "id": 4,
    "name": "Ahmed Construction Manager",
    "email": "ahmed.manager@construction.com",
    "password": "password", 
    "role": "customer"
}
```
**التخصص:** مدير مشاريع إنشائية

---

### 👩‍💼 **5. Fatma Architect**
```json
{
    "id": 5,
    "name": "Fatma Architect",
    "email": "fatma.architect@design.com",
    "password": "password",
    "role": "customer"
}
```
**التخصص:** مهندسة معمارية

---

### 👨‍🔧 **6. Mohamed Contractor**
```json
{
    "id": 6,
    "name": "Mohamed Contractor", 
    "email": "mohamed.contractor@build.com",
    "password": "password",
    "role": "customer"
}
```
**التخصص:** مقاول عام

---

### 🧪 **7. Test User Tressa**
```json
{
    "id": 7,
    "name": "Test User Tressa",
    "email": "test510@example.com", 
    "password": "password",
    "role": "customer"
}
```
**الوصف:** حساب اختبار إضافي

---

## 🚀 **Quick Login للـ APIs:**

### **Admin Login:**
```bash
curl -X POST http://localhost:8000/api/v1/admin/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@construction.com",
    "password": "password"
  }'
```

### **Customer Login:**
```bash
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "customer@construction.com", 
    "password": "password"
  }'
```

### **Supplier Login:**
```bash
curl -X POST http://localhost:8000/api/v1/supplier/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "supplier@construction.com",
    "password": "password"
  }'
```

---

## 📊 **إحصائيات النظام:**

### **إجمالي المستخدمين:** 7
### **الأدوار المتاحة:**
- **👑 Admin:** 1 مستخدم
- **🏭 Supplier:** 1 مستخدم
- **👥 Customer:** 5 مستخدمين

---

## 🛒 **بيانات اختبار إضافية:**

### **🎫 كوبونات الخصم المتاحة:**
```json
{
    "SAVE10": "خصم 10% (الحد الأدنى 100 جنيه)",
    "DISCOUNT20": "خصم 20% (الحد الأدنى 200 جنيه)", 
    "FLAT50": "خصم 50 جنيه (الحد الأدنى 300 جنيه)",
    "WELCOME25": "خصم ترحيبي 25% (الحد الأدنى 150 جنيه)",
    "SUMMER15": "خصم صيفي 15% (الحد الأدنى 250 جنيه)"
}
```

### **📍 عناوين مُضافة للعميل الأساسي:**
```json
{
    "address_id": 2,
    "user_id": 2,
    "type": "home",
    "name": "Home Address", 
    "street": "123 Main Street",
    "city": "Cairo",
    "country": "Egypt",
    "is_default": true
}
```

---

## 🔧 **ملاحظات مهمة:**

⚠️ **أمني:** هذه حسابات اختبار فقط - لا تستخدم في الإنتاج
✅ **تحديث:** جميع الحسابات محدثة ومتزامنة مع قاعدة البيانات
🔄 **إعادة تعيين:** يمكن إعادة إنشاء البيانات بـ `php artisan migrate:fresh --seed`
🚀 **جاهزة:** جميع الحسابات تعمل مع الـ APIs فوراً

---

*آخر تحديث: يناير 2025* 
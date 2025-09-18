# 🔒 **الـ Dynamic Content Admin APIs الموجودة حالياً**

## 🎯 **حالة النظام**

**Base URL:** `http://localhost:8000/api/v1/admin/`

**🔐 Authentication Required:** `Bearer Token` (Admin Role)

---

## 📋 **Admin APIs Status**

من خلال الاختبار الفعلي، هذه هي حالة الـ Admin APIs:

| # | API | Status | Methods | Notes |
|---|-----|--------|---------|-------|
| 1️⃣ | `company-info` | ✅ Working | GET, PUT | Singleton |
| 2️⃣ | `company-stats` | ✅ Working | GET, PUT | Singleton |
| 3️⃣ | `contact-info` | ✅ Working | GET, PUT | Singleton |
| 4️⃣ | `social-links` | ❌ Controller Missing | N/A | Needs Implementation |
| 5️⃣ | `page-content` | ✅ Working | GET, PUT | Singleton |
| 6️⃣ | `company-values` | ⚠️ Response Issues | GET, POST, PUT, DELETE | Collection |
| 7️⃣ | `company-milestones` | ⚠️ Response Issues | GET, POST, PUT, DELETE | Collection |
| 8️⃣ | `company-story` | ✅ Working | GET, PUT | Singleton |
| 9️⃣ | `team-members` | ⚠️ Response Issues | GET, POST, PUT, DELETE | Collection |
| 🔟 | `departments` | ⚠️ Response Issues | GET, POST, PUT, DELETE | Collection |
| 1️⃣1️⃣ | `faqs` | ⚠️ Method Issues | GET, POST, PUT, DELETE | Collection |
| 1️⃣2️⃣ | `certifications` | ⚠️ Response Issues | GET, POST, PUT, DELETE | Collection |

**✅ Working: 5/12 APIs (42%)**
**⚠️ Issues: 6/12 APIs (50%)**  
**❌ Missing: 1/12 APIs (8%)**

---

## ✅ **Working Admin APIs (البيانات الحقيقية)**

### **1️⃣ Company Info Admin API**

#### **Endpoints:**
- `GET /api/v1/admin/company-info` - عرض البيانات
- `PUT /api/v1/admin/company-info` - تحديث البيانات

#### **GET Response (الحقيقية):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "company_name_ar": "بي إس تولز",
    "company_name_en": "BS Tools",
    "company_description_ar": "شركة رائدة في مجال أدوات ومواد البناء منذ أكثر من 15 عاماً، تقدم حلولاً شاملة ومبتكرة لقطاع الإنشاءات والبناء",
    "company_description_en": "Leading company in construction tools and materials for over 15 years, providing comprehensive and innovative solutions for the construction and building sector",
    "mission_ar": "نسعى لتوفير أفضل أدوات ومواد البناء بأعلى جودة وأسعار تنافسية، مع تقديم خدمة عملاء استثنائية وحلول مبتكرة لجميع احتياجات البناء والتشييد",
    "mission_en": "We strive to provide the best construction tools and materials with the highest quality and competitive prices, while delivering exceptional customer service and innovative solutions for all construction and building needs",
    "vision_ar": "أن نكون الشركة الرائدة في منطقة الشرق الأوسط في توفير أدوات ومواد البناء عالية الجودة، والشريك المفضل لكل مقاول ومهندس",
    "vision_en": "To be the leading company in the Middle East for providing high-quality construction tools and materials, and the preferred partner for every contractor and engineer",
    "logo_text": "BS",
    "founded_year": "2009",
    "employees_count": "150+",
    "created_at": "2025-09-16 16:49:26",
    "updated_at": "2025-09-18 11:44:24"
  }
}
```

#### **PUT Request Example:**
```json
{
  "company_name_ar": "بي إس تولز المحدثة",
  "company_name_en": "BS Tools Updated",
  "company_description_ar": "وصف الشركة المحدث بالعربية",
  "company_description_en": "Updated company description in English",
  "mission_ar": "الرسالة المحدثة بالعربية",
  "mission_en": "Updated mission in English",
  "vision_ar": "الرؤية المحدثة بالعربية",
  "vision_en": "Updated vision in English",
  "logo_text": "BS",
  "founded_year": "2009",
  "employees_count": "200+"
}
```

---

### **2️⃣ Company Stats Admin API**

#### **Endpoints:**
- `GET /api/v1/admin/company-stats` - عرض الإحصائيات
- `PUT /api/v1/admin/company-stats` - تحديث الإحصائيات

#### **GET Response (الحقيقية):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "years_experience": "15+",
    "total_customers": "50K+",
    "completed_projects": "1000+",
    "support_availability": "24/7",
    "created_at": "2025-09-16 16:49:26",
    "updated_at": "2025-09-18 11:44:24"
  }
}
```

#### **PUT Request Example:**
```json
{
  "years_experience": "16+",
  "total_customers": "60K+",
  "completed_projects": "1200+",
  "support_availability": "24/7"
}
```

---

### **3️⃣ Contact Info Admin API**

#### **Endpoints:**
- `GET /api/v1/admin/contact-info` - عرض معلومات الاتصال
- `PUT /api/v1/admin/contact-info` - تحديث معلومات الاتصال

#### **GET Response (الحقيقية):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "main_phone": "+20 123 456 7890",
    "secondary_phone": "+20 987 654 3210",
    "toll_free": "+20 800 123 456",
    "main_email": "info@bstools.com",
    "sales_email": "sales@bstools.com",
    "support_email": "support@bstools.com",
    "address": {
      "street": "شارع التحرير، المعادي",
      "district": "المعادي",
      "city": "القاهرة",
      "country": "مصر",
      "full_address": "شارع التحرير، المعادي, المعادي, القاهرة, مصر"
    },
    "working_hours": {
      "weekdays": "الأحد - الخميس: 9:00 ص - 6:00 م",
      "friday": "الجمعة: مغلق",
      "saturday": "السبت: 9:00 ص - 2:00 م"
    },
    "created_at": "2025-09-16 16:49:26",
    "updated_at": "2025-09-17 08:56:47"
  }
}
```

#### **PUT Request Example:**
```json
{
  "main_phone": "+20 123 456 7890",
  "secondary_phone": "+20 987 654 3210",
  "toll_free": "+20 800 123 456",
  "main_email": "info@bstools.com",
  "sales_email": "sales@bstools.com",
  "support_email": "support@bstools.com",
  "address": {
    "street": "شارع جديد، المعادي",
    "district": "المعادي",
    "city": "القاهرة",
    "country": "مصر"
  },
  "working_hours": {
    "weekdays": "الأحد - الخميس: 9:00 ص - 7:00 م",
    "friday": "الجمعة: مغلق",
    "saturday": "السبت: 9:00 ص - 3:00 م"
  }
}
```

---

### **4️⃣ Page Content Admin API**

#### **Endpoints:**
- `GET /api/v1/admin/page-content` - عرض محتوى الصفحات
- `PUT /api/v1/admin/page-content` - تحديث محتوى الصفحات

#### **GET Response (الحقيقية):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "about_page": {
      "badge_ar": "من نحن",
      "badge_en": "About Us",
      "title_ar": "نحن بي إس تولز",
      "title_en": "We are BS Tools",
      "subtitle_ar": "شركة رائدة في مجال أدوات ومواد البناء منذ أكثر من 15 عاماً",
      "subtitle_en": "A leading company in construction tools and materials for over 15 years"
    },
    "contact_page": {
      "badge_ar": "تواصل معنا",
      "badge_en": "Contact Us",
      "title_ar": "اتصل بنا",
      "title_en": "Contact Us",
      "subtitle_ar": "نحن هنا لمساعدتك. تواصل معنا في أي وقت",
      "subtitle_en": "We are here to help you. Contact us anytime"
    },
    "created_at": "2025-09-16 16:49:55",
    "updated_at": "2025-09-17 08:56:47"
  }
}
```

#### **PUT Request Example:**
```json
{
  "about_page": {
    "badge_ar": "من نحن - محدث",
    "badge_en": "About Us - Updated",
    "title_ar": "نحن بي إس تولز الجديدة",
    "title_en": "We are BS Tools New",
    "subtitle_ar": "شركة رائدة ومحدثة في مجال أدوات ومواد البناء",
    "subtitle_en": "Leading and updated company in construction tools and materials"
  },
  "contact_page": {
    "badge_ar": "تواصل معنا الآن",
    "badge_en": "Contact Us Now",
    "title_ar": "اتصل بنا الآن",
    "title_en": "Contact Us Now",
    "subtitle_ar": "نحن متاحون 24/7 لخدمتك",
    "subtitle_en": "We are available 24/7 to serve you"
  }
}
```

---

### **5️⃣ Company Story Admin API**

#### **Endpoints:**
- `GET /api/v1/admin/company-story` - عرض قصة الشركة
- `PUT /api/v1/admin/company-story` - تحديث قصة الشركة

#### **GET Response (الحقيقية):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "paragraph1_ar": "بدأنا رحلتنا في عام 2009 برؤية واضحة: توفير أدوات ومواد بناء عالية الجودة بأسعار معقولة. منذ ذلك الحين، نمونا لنصبح واحدة من أكبر الشركات المتخصصة في هذا المجال.",
    "paragraph1_en": "We started our journey in 2009 with a clear vision: to provide high-quality construction tools and materials at reasonable prices. Since then, we have grown to become one of the largest specialized companies in this field.",
    "paragraph2_ar": "خلال رحلتنا، ساعدنا في إنجاز آلاف المشاريع، من المنازل السكنية البسيطة إلى المجمعات التجارية الضخمة. نفخر بالثقة التي منحها لنا عملاؤنا عبر السنين.",
    "paragraph2_en": "During our journey, we helped complete thousands of projects, from simple residential homes to huge commercial complexes. We are proud of the trust our customers have given us over the years.",
    "paragraph3_ar": "نواصل استثمارنا في أحدث التقنيات والمعدات لضمان تقديم أفضل الخدمات والمنتجات. هدفنا هو أن نكون شريككم الموثوق في كل مشروع.",
    "paragraph3_en": "We continue to invest in the latest technologies and equipment to ensure the delivery of the best services and products. Our goal is to be your trusted partner in every project.",
    "features": [
      {
        "name_ar": "منتجات عالية الجودة",
        "name_en": "High Quality Products"
      },
      {
        "name_ar": "معايير أمان صارمة",
        "name_en": "Strict Safety Standards"
      },
      {
        "name_ar": "تطوير مستمر",
        "name_en": "Continuous Development"
      },
      {
        "name_ar": "سعي للتميز",
        "name_en": "Pursuit of Excellence"
      }
    ],
    "created_at": "2025-09-16 16:49:26",
    "updated_at": "2025-09-17 08:56:47"
  }
}
```

#### **PUT Request Example:**
```json
{
  "paragraph1_ar": "قصة جديدة للفقرة الأولى...",
  "paragraph1_en": "New story for first paragraph...",
  "paragraph2_ar": "قصة جديدة للفقرة الثانية...",
  "paragraph2_en": "New story for second paragraph...",
  "paragraph3_ar": "قصة جديدة للفقرة الثالثة...",
  "paragraph3_en": "New story for third paragraph...",
  "features": [
    {
      "name_ar": "ميزة جديدة 1",
      "name_en": "New Feature 1"
    },
    {
      "name_ar": "ميزة جديدة 2",
      "name_en": "New Feature 2"
    }
  ]
}
```

---

## ⚠️ **APIs with Issues (تحتاج إصلاح)**

### **المشاكل الشائعة:**

1. **Response Type Issues:** بعض Controllers ترجع `Collection` بدلاً من `JsonResponse`
2. **Method Parameters:** بعض `index` methods تتطلب `Request` parameter
3. **Missing Controllers:** بعض Admin Controllers غير موجودة

### **APIs تحتاج إصلاح:**

- ❌ **Social Links Admin** - Controller غير موجود
- ⚠️ **Company Values Admin** - مشاكل في Response format  
- ⚠️ **Company Milestones Admin** - مشاكل في Response format
- ⚠️ **Team Members Admin** - مشاكل في Response format
- ⚠️ **Departments Admin** - مشاكل في Response format  
- ⚠️ **FAQs Admin** - مشاكل في Method parameters
- ⚠️ **Certifications Admin** - مشاكل في Response format

---

## 💻 **JavaScript Usage Examples (للـ Working APIs)**

### **🔐 Headers Required:**
```javascript
const headers = {
  'Authorization': 'Bearer YOUR_ADMIN_TOKEN',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
};
```

### **1️⃣ Company Info Management:**

```javascript
// Get company info
const getCompanyInfo = async (token) => {
  try {
    const response = await fetch('http://localhost:8000/api/v1/admin/company-info', {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    });
    
    const data = await response.json();
    return data;
  } catch (error) {
    console.error('Error:', error);
  }
};

// Update company info
const updateCompanyInfo = async (token, companyData) => {
  try {
    const response = await fetch('http://localhost:8000/api/v1/admin/company-info', {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(companyData)
    });
    
    const result = await response.json();
    return result;
  } catch (error) {
    console.error('Error:', error);
  }
};
```

### **2️⃣ Company Stats Management:**

```javascript
// Update company stats
const updateCompanyStats = async (token, statsData) => {
  try {
    const response = await fetch('http://localhost:8000/api/v1/admin/company-stats', {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        years_experience: "16+",
        total_customers: "60K+",
        completed_projects: "1200+",
        support_availability: "24/7"
      })
    });
    
    const result = await response.json();
    return result;
  } catch (error) {
    console.error('Error:', error);
  }
};
```

### **3️⃣ Contact Info Management:**

```javascript
// Update contact info
const updateContactInfo = async (token, contactData) => {
  try {
    const response = await fetch('http://localhost:8000/api/v1/admin/contact-info', {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        main_phone: "+20 123 456 7890",
        secondary_phone: "+20 987 654 3210",
        toll_free: "+20 800 123 456",
        main_email: "info@bstools.com",
        sales_email: "sales@bstools.com",
        support_email: "support@bstools.com",
        address: {
          street: "شارع محدث",
          district: "المعادي", 
          city: "القاهرة",
          country: "مصر"
        },
        working_hours: {
          weekdays: "الأحد - الخميس: 9:00 ص - 7:00 م",
          friday: "الجمعة: مغلق",
          saturday: "السبت: 9:00 ص - 3:00 م"
        }
      })
    });
    
    const result = await response.json();
    return result;
  } catch (error) {
    console.error('Error:', error);
  }
};
```

---

## 📊 **Summary**

### **✅ Working Admin APIs (5):**
1. **Company Info** - GET, PUT (Singleton)
2. **Company Stats** - GET, PUT (Singleton)  
3. **Contact Info** - GET, PUT (Singleton)
4. **Page Content** - GET, PUT (Singleton)
5. **Company Story** - GET, PUT (Singleton)

### **⚠️ Need Fixes (7):**
- Social Links Admin (Missing Controller)
- Company Values Admin (Response Issues)
- Company Milestones Admin (Response Issues)
- Team Members Admin (Response Issues)
- Departments Admin (Response Issues)
- FAQs Admin (Method Issues)
- Certifications Admin (Response Issues)

### **📈 Current Status:**
- **Working:** 5/12 APIs (42%)
- **Need Fixes:** 7/12 APIs (58%)

### **🎯 Recommendation:**
Focus on fixing the **Collection-based APIs** (company-values, team-members, departments, etc.) to make them return proper JsonResponse format for admin panel usage.

---

## 🔧 **Admin Token Generation**

```bash
# Create admin user and get token
php artisan tinker
```

```php
$admin = App\Models\User::create([
    'name' => 'Admin User',
    'email' => 'admin@bstools.com', 
    'password' => bcrypt('admin123')
]);

$admin->assignRole('admin');
$token = $admin->createToken('admin-panel')->plainTextToken;
echo "Admin Token: " . $token;
```

---

**🎉 5 Admin APIs جاهزة للاستخدام في لوحة التحكم!** 
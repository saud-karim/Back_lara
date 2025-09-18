/**
 * Script لإرسال البيانات الحالية من صفحات About Us & Contact إلى Backend APIs
 * 
 * تشغيل Script:
 * node scripts/seed-content-data.js
 */

const API_BASE_URL = 'http://127.0.0.1:8000/api/v1/admin';

// البيانات المستخرجة من الصفحات الحالية
const contentData = {
  // 1. Company Info
  companyInfo: {
    company_name: 'BS Tools',
    company_description: 'شركة رائدة في مجال أدوات ومواد البناء منذ أكثر من 15 عاماً',
    mission_ar: 'نسعى لتوفير أفضل أدوات ومواد البناء بأعلى جودة وأسعار تنافسية، مع تقديم خدمة عملاء استثنائية وحلول مبتكرة لجميع احتياجات البناء والتشييد.',
    mission_en: 'We strive to provide the best construction tools and materials with the highest quality and competitive prices, while providing exceptional customer service and innovative solutions for all construction needs.',
    vision_ar: 'أن نكون الشركة الرائدة في منطقة الشرق الأوسط في توفير أدوات ومواد البناء عالية الجودة',
    vision_en: 'To be the leading company in the Middle East in providing high-quality construction tools and materials',
    founded_year: '2009',
    employees_count: 150
  },

  // 2. Company Stats
  companyStats: {
    years_experience: 15,
    happy_customers: 50000,
    completed_projects: 1000,
    support_available: true
  },

  // 3. Contact Info
  contactInfo: {
    main_phone: '+20 123 456 7890',
    secondary_phone: '+20 987 654 3210',
    whatsapp: '+20 123 456 7890',
    main_email: 'info@bstools.com',
    support_email: 'support@bstools.com',
    address_ar: 'شارع التحرير، المعادي، القاهرة، مصر',
    address_en: 'Tahrir Street, Maadi, Cairo, Egypt',
    working_hours_ar: 'الأحد - الخميس: 9:00 ص - 6:00 م، السبت: 9:00 ص - 2:00 م، الجمعة: مغلق',
    working_hours_en: 'Sunday - Thursday: 9:00 AM - 6:00 PM, Saturday: 9:00 AM - 2:00 PM, Friday: Closed'
  },

  // 4. Departments
  departments: [
    {
      name_ar: 'المبيعات',
      name_en: 'Sales',
      description_ar: 'للاستفسار عن المنتجات والأسعار',
      description_en: 'For product inquiries and pricing',
      phone: '+20 123 456 7891',
      email: 'sales@bstools.com',
      icon: '💼',
      order_index: 1
    },
    {
      name_ar: 'الدعم الفني',
      name_en: 'Technical Support',
      description_ar: 'للمساعدة التقنية وحل المشاكل',
      description_en: 'For technical assistance and problem solving',
      phone: '+20 123 456 7892',
      email: 'support@bstools.com',
      icon: '🔧',
      order_index: 2
    },
    {
      name_ar: 'خدمة العملاء',
      name_en: 'Customer Service',
      description_ar: 'للشكاوى واقتراحات التحسين',
      description_en: 'For complaints and improvement suggestions',
      phone: '+20 123 456 7893',
      email: 'service@bstools.com',
      icon: '👥',
      order_index: 3
    },
    {
      name_ar: 'الشراكات',
      name_en: 'Partnerships',
      description_ar: 'للشراكات التجارية والتعاون',
      description_en: 'For business partnerships and cooperation',
      phone: '+20 123 456 7894',
      email: 'partners@bstools.com',
      icon: '🤝',
      order_index: 4
    }
  ],

  // 5. Social Links
  socialLinks: [
    {
      platform: 'Facebook',
      url: 'https://facebook.com/bstools',
      icon: '📘',
      is_active: true,
      order_index: 1
    },
    {
      platform: 'Twitter',
      url: 'https://twitter.com/bstools',
      icon: '🐦',
      is_active: true,
      order_index: 2
    },
    {
      platform: 'LinkedIn',
      url: 'https://linkedin.com/company/bstools',
      icon: '💼',
      is_active: true,
      order_index: 3
    },
    {
      platform: 'Instagram',
      url: 'https://instagram.com/bstools',
      icon: '📷',
      is_active: true,
      order_index: 4
    },
    {
      platform: 'YouTube',
      url: 'https://youtube.com/bstools',
      icon: '📺',
      is_active: true,
      order_index: 5
    },
    {
      platform: 'WhatsApp',
      url: 'https://wa.me/201234567890',
      icon: '💬',
      is_active: true,
      order_index: 6
    }
  ],

  // 6. Team Members
  teamMembers: [
    {
      name_ar: 'أحمد محمد',
      name_en: 'Ahmed Mohamed',
      position_ar: 'مدير عام',
      position_en: 'General Manager',
      bio_ar: '15 سنة خبرة في إدارة المشاريع والتطوير',
      bio_en: '15 years of experience in project management and development',
      image_url: null,
      email: 'ahmed@bstools.com',
      phone: '+20 123 456 7801',
      linkedin_url: null,
      is_featured: true,
      order_index: 1
    },
    {
      name_ar: 'سارة أحمد',
      name_en: 'Sarah Ahmed',
      position_ar: 'مديرة المبيعات',
      position_en: 'Sales Manager',
      bio_ar: '12 سنة خبرة في علاقات العملاء والتسويق',
      bio_en: '12 years of experience in customer relations and marketing',
      image_url: null,
      email: 'sarah@bstools.com',
      phone: '+20 123 456 7802',
      linkedin_url: null,
      is_featured: true,
      order_index: 2
    },
    {
      name_ar: 'عمر حسن',
      name_en: 'Omar Hassan',
      position_ar: 'مهندس فني',
      position_en: 'Technical Engineer',
      bio_ar: '10 سنوات خبرة في الاستشارات التقنية والتركيب',
      bio_en: '10 years of experience in technical consulting and installation',
      image_url: null,
      email: 'omar@bstools.com',
      phone: '+20 123 456 7803',
      linkedin_url: null,
      is_featured: false,
      order_index: 3
    },
    {
      name_ar: 'فاطمة علي',
      name_en: 'Fatima Ali',
      position_ar: 'مديرة التقنية',
      position_en: 'Technology Manager',
      bio_ar: '8 سنوات خبرة في تطوير الأنظمة والتقنيات',
      bio_en: '8 years of experience in systems and technology development',
      image_url: null,
      email: 'fatima@bstools.com',
      phone: '+20 123 456 7804',
      linkedin_url: null,
      is_featured: false,
      order_index: 4
    }
  ],

  // 7. Company Values
  companyValues: [
    {
      title_ar: 'الجودة',
      title_en: 'Quality',
      description_ar: 'نضمن أعلى مستويات الجودة في جميع منتجاتنا وخدماتنا',
      description_en: 'We guarantee the highest levels of quality in all our products and services',
      icon: '⭐',
      order_index: 1
    },
    {
      title_ar: 'الدعم',
      title_en: 'Support',
      description_ar: 'نقدم دعماً شاملاً لعملائنا قبل وبعد البيع',
      description_en: 'We provide comprehensive support to our customers before and after sales',
      icon: '🎯',
      order_index: 2
    },
    {
      title_ar: 'الابتكار',
      title_en: 'Innovation',
      description_ar: 'نسعى دائماً للتطوير وتقديم حلول مبتكرة',
      description_en: 'We always strive for development and providing innovative solutions',
      icon: '🚀',
      order_index: 3
    },
    {
      title_ar: 'الموثوقية',
      title_en: 'Reliability',
      description_ar: 'نبني علاقات طويلة الأمد مع عملائنا بناءً على الثقة',
      description_en: 'We build long-term relationships with our customers based on trust',
      icon: '🛡️',
      order_index: 4
    }
  ],

  // 8. Milestones
  milestones: [
    {
      title_ar: 'تأسيس الشركة',
      title_en: 'Company Foundation',
      description_ar: 'بداية رحلتنا في عالم أدوات البناء',
      description_en: 'The beginning of our journey in the world of construction tools',
      date: '2009-01-01',
      icon: '🏗️',
      order_index: 1
    },
    {
      title_ar: 'أول فرع',
      title_en: 'First Branch',
      description_ar: 'افتتاح أول فرع لنا في القاهرة',
      description_en: 'Opening our first branch in Cairo',
      date: '2012-06-01',
      icon: '🏢',
      order_index: 2
    },
    {
      title_ar: 'شراكات دولية',
      title_en: 'International Partnerships',
      description_ar: 'توقيع أول اتفاقيات مع موردين عالميين',
      description_en: 'Signing first agreements with international suppliers',
      date: '2015-03-01',
      icon: '🤝',
      order_index: 3
    },
    {
      title_ar: 'التوسع الإقليمي',
      title_en: 'Regional Expansion',
      description_ar: 'افتتاح فروع في 5 محافظات جديدة',
      description_en: 'Opening branches in 5 new governorates',
      date: '2018-09-01',
      icon: '📍',
      order_index: 4
    },
    {
      title_ar: 'المنصة الرقمية',
      title_en: 'Digital Platform',
      description_ar: 'إطلاق موقعنا الإلكتروني ومنصة التجارة',
      description_en: 'Launching our website and e-commerce platform',
      date: '2021-01-01',
      icon: '💻',
      order_index: 5
    },
    {
      title_ar: 'القيادة السوقية',
      title_en: 'Market Leadership',
      description_ar: 'أصبحنا أكبر موزع لأدوات البناء في المنطقة',
      description_en: 'We became the largest distributor of construction tools in the region',
      date: '2024-01-01',
      icon: '👑',
      order_index: 6
    }
  ],

  // 9. Company Story
  companyStory: {
    paragraph1_ar: 'بدأنا رحلتنا في عام 2009 برؤية واضحة: توفير أدوات ومواد بناء عالية الجودة بأسعار معقولة. منذ ذلك الحين، نمونا لنصبح واحدة من أكبر الشركات المتخصصة في هذا المجال.',
    paragraph1_en: 'We started our journey in 2009 with a clear vision: to provide high-quality construction tools and materials at reasonable prices. Since then, we have grown to become one of the largest specialized companies in this field.',
    paragraph2_ar: 'خلال رحلتنا، ساعدنا في إنجاز آلاف المشاريع، من المنازل السكنية البسيطة إلى المجمعات التجارية الضخمة. نفخر بالثقة التي منحها لنا عملاؤنا عبر السنين.',
    paragraph2_en: 'During our journey, we helped complete thousands of projects, from simple residential homes to huge commercial complexes. We are proud of the trust our customers have given us over the years.',
    paragraph3_ar: 'نواصل استثمارنا في أحدث التقنيات والمعدات لضمان تقديم أفضل الخدمات والمنتجات. هدفنا هو أن نكون شريككم الموثوق في كل مشروع.',
    paragraph3_en: 'We continue to invest in the latest technologies and equipment to ensure the delivery of the best services and products. Our goal is to be your trusted partner in every project.',
    features: [
      {
        name_ar: 'منتجات عالية الجودة',
        name_en: 'High Quality Products'
      },
      {
        name_ar: 'معايير أمان صارمة',
        name_en: 'Strict Safety Standards'
      },
      {
        name_ar: 'تطوير مستمر',
        name_en: 'Continuous Development'
      },
      {
        name_ar: 'سعي للتميز',
        name_en: 'Pursuit of Excellence'
      }
    ]
  },

  // 10. Page Content
  pageContent: {
    about_page: {
      badge_ar: 'من نحن',
      badge_en: 'About Us',
      title_ar: 'نبني المستقبل معاً',
      title_en: 'Building the Future Together',
      subtitle_ar: 'شركة رائدة في مجال أدوات ومواد البناء منذ أكثر من 15 عاماً',
      subtitle_en: 'A leading company in construction tools and materials for over 15 years'
    },
    contact_page: {
      badge_ar: 'تواصل معنا',
      badge_en: 'Contact Us',
      title_ar: 'اتصل بنا',
      title_en: 'Contact Us',
      subtitle_ar: 'نحن هنا لمساعدتك. تواصل معنا في أي وقت',
      subtitle_en: 'We are here to help you. Contact us anytime'
    }
  },

  // 11. FAQs
  faqs: [
    {
      question_ar: 'ما هي سياسة الإرجاع؟',
      question_en: 'What is the return policy?',
      answer_ar: 'يمكنك إرجاع المنتجات خلال 30 يوم من تاريخ الشراء بشرط أن تكون في حالتها الأصلية',
      answer_en: 'You can return products within 30 days of purchase provided they are in their original condition',
      category_ar: 'عام',
      category_en: 'General',
      is_featured: true,
      order_index: 1
    },
    {
      question_ar: 'هل توفرون خصومات للكميات الكبيرة؟',
      question_en: 'Do you offer bulk discounts?',
      answer_ar: 'نعم، نوفر خصومات خاصة للطلبات الكبيرة والمشاريع التجارية. تواصل مع فريق المبيعات لمعرفة التفاصيل',
      answer_en: 'Yes, we offer special discounts for large orders and commercial projects. Contact our sales team for details',
      category_ar: 'مبيعات',
      category_en: 'Sales',
      is_featured: true,
      order_index: 2
    },
    {
      question_ar: 'ما هي مناطق التوصيل المتاحة؟',
      question_en: 'What are the available delivery areas?',
      answer_ar: 'نوصل لجميع أنحاء مصر خلال 24-48 ساعة، مع توصيل مجاني للطلبات أكثر من 1000 جنيه',
      answer_en: 'We deliver throughout Egypt within 24-48 hours, with free delivery for orders over 1000 EGP',
      category_ar: 'شحن',
      category_en: 'Shipping',
      is_featured: false,
      order_index: 3
    },
    {
      question_ar: 'كيف يمكنني الحصول على الدعم الفني؟',
      question_en: 'How can I get technical support?',
      answer_ar: 'يمكنك التواصل مع فريق الدعم الفني عبر الهاتف أو البريد الإلكتروني، متاحون 24/7',
      answer_en: 'You can contact our technical support team by phone or email, available 24/7',
      category_ar: 'دعم فني',
      category_en: 'Technical Support',
      is_featured: false,
      order_index: 4
    }
  ],

  // 12. Certifications
  certifications: [
    {
      name_ar: 'شهادة ISO 9001',
      name_en: 'ISO 9001 Certificate',
      description_ar: 'معتمدون في إدارة الجودة الشاملة',
      description_en: 'Certified in total quality management',
      issuer_ar: 'المنظمة الدولية للمعايير',
      issuer_en: 'International Organization for Standardization',
      issue_date: '2020-01-15',
      expiry_date: '2023-01-15',
      certificate_number: 'ISO-9001-2020-BS',
      image_url: null,
      is_active: true,
      order_index: 1
    },
    {
      name_ar: 'معايير OSHA',
      name_en: 'OSHA Standards',
      description_ar: 'ملتزمون بأعلى معايير الأمان والسلامة',
      description_en: 'Committed to the highest safety and security standards',
      issuer_ar: 'إدارة السلامة والصحة المهنية',
      issuer_en: 'Occupational Safety and Health Administration',
      issue_date: '2021-03-10',
      expiry_date: null,
      certificate_number: 'OSHA-2021-BS',
      image_url: null,
      is_active: true,
      order_index: 2
    },
    {
      name_ar: 'شريك معتمد',
      name_en: 'Authorized Partner',
      description_ar: 'شريك رسمي للعلامات التجارية العالمية',
      description_en: 'Official partner for global brands',
      issuer_ar: 'ديوالت',
      issuer_en: 'DeWalt',
      issue_date: '2019-06-01',
      expiry_date: '2025-06-01',
      certificate_number: 'DEWALT-PARTNER-2019',
      image_url: null,
      is_active: true,
      order_index: 3
    },
    {
      name_ar: 'رائد السوق',
      name_en: 'Market Leader',
      description_ar: 'الشركة الرائدة في المنطقة لثلاث سنوات متتالية',
      description_en: 'Leading company in the region for three consecutive years',
      issuer_ar: 'غرفة التجارة المصرية',
      issuer_en: 'Egyptian Chamber of Commerce',
      issue_date: '2023-12-01',
      expiry_date: null,
      certificate_number: 'ECC-LEADER-2023',
      image_url: null,
      is_active: true,
      order_index: 4
    }
  ]
};

// دالة إرسال البيانات إلى API
async function sendToAPI(endpoint, data, method = 'POST') {
  try {
    console.log(`🚀 Sending data to ${endpoint}...`);
    
    const response = await fetch(`${API_BASE_URL}/${endpoint}`, {
      method: method,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        // 'Authorization': 'Bearer YOUR_ADMIN_TOKEN' // أضف التوكن هنا إذا كان مطلوباً
      },
      body: JSON.stringify(data)
    });

    const result = await response.json();
    
    if (response.ok) {
      console.log(`✅ ${endpoint} - Success:`, result.message || 'Data sent successfully');
      return { success: true, data: result };
    } else {
      console.error(`❌ ${endpoint} - Error:`, result.message || 'Failed to send data');
      return { success: false, error: result };
    }
  } catch (error) {
    console.error(`💥 ${endpoint} - Network Error:`, error.message);
    return { success: false, error: error.message };
  }
}

// دالة إرسال البيانات المتعددة (للـ Arrays)
async function sendMultipleToAPI(endpoint, dataArray, delay = 1000) {
  console.log(`📋 Sending ${dataArray.length} items to ${endpoint}...`);
  
  const results = [];
  for (let i = 0; i < dataArray.length; i++) {
    const item = dataArray[i];
    console.log(`   📌 Item ${i + 1}/${dataArray.length}: ${item.name_ar || item.title_ar || item.question_ar || item.platform || 'Item'}`);
    
    const result = await sendToAPI(endpoint, item);
    results.push(result);
    
    // إضافة تأخير بين الطلبات لتجنب Rate Limiting
    if (i < dataArray.length - 1) {
      console.log(`   ⏳ Waiting ${delay}ms...`);
      await new Promise(resolve => setTimeout(resolve, delay));
    }
  }
  
  const successCount = results.filter(r => r.success).length;
  console.log(`📊 ${endpoint} Results: ${successCount}/${dataArray.length} successful\n`);
  
  return results;
}

// الدالة الرئيسية لإرسال جميع البيانات
async function seedAllContentData() {
  console.log('🎯 Starting Content Data Seeding Process...\n');
  console.log('📋 This will populate your Backend with real data from About Us & Contact pages\n');
  
  const results = {};

  try {
    // 1. Company Info (PUT - Singleton)
    console.log('1️⃣ Company Information...');
    results.companyInfo = await sendToAPI('company-info', contentData.companyInfo, 'PUT');
    await new Promise(resolve => setTimeout(resolve, 2000));

    // 2. Company Stats (PUT - Singleton)
    console.log('2️⃣ Company Statistics...');
    results.companyStats = await sendToAPI('company-stats', contentData.companyStats, 'PUT');
    await new Promise(resolve => setTimeout(resolve, 2000));

    // 3. Contact Info (PUT - Singleton)
    console.log('3️⃣ Contact Information...');
    results.contactInfo = await sendToAPI('contact-info', contentData.contactInfo, 'PUT');
    await new Promise(resolve => setTimeout(resolve, 2000));

    // 4. Company Story (PUT - Singleton)
    console.log('4️⃣ Company Story...');
    results.companyStory = await sendToAPI('company-story', contentData.companyStory, 'PUT');
    await new Promise(resolve => setTimeout(resolve, 2000));

    // 5. Page Content (PUT - Singleton)
    console.log('5️⃣ Page Content...');
    results.pageContent = await sendToAPI('page-content', contentData.pageContent, 'PUT');
    await new Promise(resolve => setTimeout(resolve, 2000));

    // 6. Departments (POST - Multiple)
    console.log('6️⃣ Departments...');
    results.departments = await sendMultipleToAPI('departments', contentData.departments, 1500);

    // 7. Social Links (POST - Multiple)
    console.log('7️⃣ Social Links...');
    results.socialLinks = await sendMultipleToAPI('social-links', contentData.socialLinks, 1500);

    // 8. Team Members (POST - Multiple)
    console.log('8️⃣ Team Members...');
    results.teamMembers = await sendMultipleToAPI('team-members', contentData.teamMembers, 1500);

    // 9. Company Values (POST - Multiple)
    console.log('9️⃣ Company Values...');
    results.companyValues = await sendMultipleToAPI('company-values', contentData.companyValues, 1500);

    // 10. Milestones (POST - Multiple)
    console.log('🔟 Milestones...');
    results.milestones = await sendMultipleToAPI('milestones', contentData.milestones, 1500);

    // 11. FAQs (POST - Multiple)
    console.log('1️⃣1️⃣ FAQs...');
    results.faqs = await sendMultipleToAPI('faqs', contentData.faqs, 1500);

    // 12. Certifications (POST - Multiple)
    console.log('1️⃣2️⃣ Certifications...');
    results.certifications = await sendMultipleToAPI('certifications', contentData.certifications, 1500);

  } catch (error) {
    console.error('💥 Unexpected error during seeding:', error);
  }

  // تقرير النتائج النهائية
  console.log('\n📊 FINAL SEEDING REPORT:');
  console.log('========================');
  
  Object.entries(results).forEach(([key, result]) => {
    if (Array.isArray(result)) {
      const successCount = result.filter(r => r.success).length;
      const status = successCount === result.length ? '✅' : '⚠️';
      console.log(`${status} ${key}: ${successCount}/${result.length} items`);
    } else {
      const status = result.success ? '✅' : '❌';
      console.log(`${status} ${key}: ${result.success ? 'Success' : 'Failed'}`);
    }
  });

  console.log('\n🎉 Content seeding process completed!');
  console.log('💡 Tip: Check your dashboard at /dashboard/content-management to verify the data');
}

// تشغيل Script
if (require.main === module) {
  console.log('🚀 BS Tools - Content Data Seeder');
  console.log('==================================\n');
  
  seedAllContentData().catch(error => {
    console.error('💥 Script failed:', error);
    process.exit(1);
  });
}

module.exports = { contentData, seedAllContentData }; 
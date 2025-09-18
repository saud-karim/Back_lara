<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CompanyInfo;
use App\Models\CompanyStats;
use App\Models\ContactInfo;
use App\Models\Department;
use App\Models\SocialLink;
use App\Models\TeamMember;
use App\Models\CompanyValue;
use App\Models\CompanyMilestone;
use App\Models\CompanyStory;
use App\Models\PageContent;
use App\Models\FAQ;
use App\Models\Certification;

class DynamicContentSeederFixed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding Dynamic Content Management Data (Fixed)...');

        // 1. Company Info (Singleton) - Fields: company_name, company_description, mission, vision, logo_text, founded_year, employees_count
        $this->command->info('1️⃣ Seeding Company Information...');
        CompanyInfo::updateOrCreate([], [
            'company_name' => 'BS Tools',
            'company_description' => 'شركة رائدة في مجال أدوات ومواد البناء منذ أكثر من 15 عاماً',
            'mission' => 'نسعى لتوفير أفضل أدوات ومواد البناء بأعلى جودة وأسعار تنافسية، مع تقديم خدمة عملاء استثنائية وحلول مبتكرة لجميع احتياجات البناء والتشييد.',
            'vision' => 'أن نكون الشركة الرائدة في منطقة الشرق الأوسط في توفير أدوات ومواد البناء عالية الجودة',
            // البيانات العربية والإنجليزية
            'company_name_ar' => 'بي إس تولز',
            'company_name_en' => 'BS Tools',
            'company_description_ar' => 'شركة رائدة في مجال أدوات ومواد البناء منذ أكثر من 15 عاماً، تقدم حلولاً شاملة ومبتكرة لقطاع الإنشاءات والبناء',
            'company_description_en' => 'Leading company in construction tools and materials for over 15 years, providing comprehensive and innovative solutions for the construction and building sector',
            'mission_ar' => 'نسعى لتوفير أفضل أدوات ومواد البناء بأعلى جودة وأسعار تنافسية، مع تقديم خدمة عملاء استثنائية وحلول مبتكرة لجميع احتياجات البناء والتشييد',
            'mission_en' => 'We strive to provide the best construction tools and materials with the highest quality and competitive prices, while delivering exceptional customer service and innovative solutions for all construction and building needs',
            'vision_ar' => 'أن نكون الشركة الرائدة في منطقة الشرق الأوسط في توفير أدوات ومواد البناء عالية الجودة، والشريك المفضل لكل مقاول ومهندس',
            'vision_en' => 'To be the leading company in the Middle East for providing high-quality construction tools and materials, and the preferred partner for every contractor and engineer',
            'logo_text' => 'BS',
            'founded_year' => '2009',
            'employees_count' => '150+'
        ]);

        // 2. Company Stats (Singleton) - Fields: years_experience, total_customers, completed_projects, support_availability
        $this->command->info('2️⃣ Seeding Company Statistics...');
        CompanyStats::updateOrCreate([], [
            'years_experience' => '15+',
            'total_customers' => '50K+',
            'completed_projects' => '1000+',
            'support_availability' => '24/7'
        ]);

        // 3. Contact Info (Singleton) - Multilingual fields
        $this->command->info('3️⃣ Seeding Contact Information...');
        ContactInfo::updateOrCreate([], [
            'main_phone' => '+20 123 456 7890',
            'secondary_phone' => '+20 987 654 3210',
            'toll_free' => '+20 800 123 456',
            'main_email' => 'info@bstools.com',
            'sales_email' => 'sales@bstools.com',
            'support_email' => 'support@bstools.com',
            'whatsapp' => '+20 100 000 0001',
            // Legacy address fields (for backward compatibility)
            'address_street' => 'شارع التحرير، المعادي',
            'address_district' => 'المعادي',
            'address_city' => 'القاهرة',
            'address_country' => 'مصر',
            // New multilingual address fields
            'address_street_ar' => 'شارع التحرير، المعادي',
            'address_street_en' => 'Tahrir Street, Maadi',
            'address_district_ar' => 'المعادي',
            'address_district_en' => 'Maadi',
            'address_city_ar' => 'القاهرة',
            'address_city_en' => 'Cairo',
            'address_country_ar' => 'مصر',
            'address_country_en' => 'Egypt',
            // Legacy working hours fields (for backward compatibility)
            'working_hours_weekdays' => 'الأحد - الخميس: 9:00 ص - 6:00 م',
            'working_hours_friday' => 'الجمعة: مغلق',
            'working_hours_saturday' => 'السبت: 9:00 ص - 2:00 م',
            // New multilingual working hours fields
            'working_hours_weekdays_ar' => 'الأحد - الخميس: 9:00 ص - 6:00 م',
            'working_hours_weekdays_en' => 'Sunday - Thursday: 9:00 AM - 6:00 PM',
            'working_hours_friday_ar' => 'الجمعة: مغلق',
            'working_hours_friday_en' => 'Friday: Closed',
            'working_hours_saturday_ar' => 'السبت: 9:00 ص - 2:00 م',
            'working_hours_saturday_en' => 'Saturday: 9:00 AM - 2:00 PM',
            // Multilingual labels
            'emergency_phone_label_ar' => 'الطوارئ',
            'emergency_phone_label_en' => 'Emergency',
            'toll_free_label_ar' => 'الخط المجاني',
            'toll_free_label_en' => 'Toll Free'
        ]);

        // 4. Company Story (Singleton)
        $this->command->info('4️⃣ Seeding Company Story...');
        CompanyStory::updateOrCreate([], [
            'paragraph1_ar' => 'بدأنا رحلتنا في عام 2009 برؤية واضحة: توفير أدوات ومواد بناء عالية الجودة بأسعار معقولة. منذ ذلك الحين، نمونا لنصبح واحدة من أكبر الشركات المتخصصة في هذا المجال.',
            'paragraph1_en' => 'We started our journey in 2009 with a clear vision: to provide high-quality construction tools and materials at reasonable prices. Since then, we have grown to become one of the largest specialized companies in this field.',
            'paragraph2_ar' => 'خلال رحلتنا، ساعدنا في إنجاز آلاف المشاريع، من المنازل السكنية البسيطة إلى المجمعات التجارية الضخمة. نفخر بالثقة التي منحها لنا عملاؤنا عبر السنين.',
            'paragraph2_en' => 'During our journey, we helped complete thousands of projects, from simple residential homes to huge commercial complexes. We are proud of the trust our customers have given us over the years.',
            'paragraph3_ar' => 'نواصل استثمارنا في أحدث التقنيات والمعدات لضمان تقديم أفضل الخدمات والمنتجات. هدفنا هو أن نكون شريككم الموثوق في كل مشروع.',
            'paragraph3_en' => 'We continue to invest in the latest technologies and equipment to ensure the delivery of the best services and products. Our goal is to be your trusted partner in every project.',
            'features' => [
                ['name_ar' => 'منتجات عالية الجودة', 'name_en' => 'High Quality Products'],
                ['name_ar' => 'معايير أمان صارمة', 'name_en' => 'Strict Safety Standards'],
                ['name_ar' => 'تطوير مستمر', 'name_en' => 'Continuous Development'],
                ['name_ar' => 'سعي للتميز', 'name_en' => 'Pursuit of Excellence']
            ]
        ]);

        // 5. Page Content (Singleton)
        $this->command->info('5️⃣ Seeding Page Content...');
        PageContent::updateOrCreate([], [
            'about_page' => [
                'badge_ar' => 'من نحن',
                'badge_en' => 'About Us',
                'title_ar' => 'نبني المستقبل معاً',
                'title_en' => 'Building the Future Together',
                'subtitle_ar' => 'شركة رائدة في مجال أدوات ومواد البناء منذ أكثر من 15 عاماً',
                'subtitle_en' => 'A leading company in construction tools and materials for over 15 years'
            ],
            'contact_page' => [
                'badge_ar' => 'تواصل معنا',
                'badge_en' => 'Contact Us',
                'title_ar' => 'اتصل بنا',
                'title_en' => 'Contact Us',
                'subtitle_ar' => 'نحن هنا لمساعدتك. تواصل معنا في أي وقت',
                'subtitle_en' => 'We are here to help you. Contact us anytime'
            ]
        ]);

        // 6. Departments
        $this->command->info('6️⃣ Seeding Departments...');
        Department::truncate();
        $departments = [
            ['name_ar' => 'المبيعات', 'name_en' => 'Sales', 'description_ar' => 'للاستفسار عن المنتجات والأسعار', 'description_en' => 'For product inquiries and pricing', 'phone' => '+20 123 456 7891', 'email' => 'sales@bstools.com', 'icon' => '💼', 'color' => 'bg-blue-500', 'order' => 1, 'is_active' => true],
            ['name_ar' => 'الدعم الفني', 'name_en' => 'Technical Support', 'description_ar' => 'للمساعدة التقنية وحل المشاكل', 'description_en' => 'For technical assistance and problem solving', 'phone' => '+20 123 456 7892', 'email' => 'support@bstools.com', 'icon' => '🔧', 'color' => 'bg-green-500', 'order' => 2, 'is_active' => true],
            ['name_ar' => 'خدمة العملاء', 'name_en' => 'Customer Service', 'description_ar' => 'للشكاوى واقتراحات التحسين', 'description_en' => 'For complaints and improvement suggestions', 'phone' => '+20 123 456 7893', 'email' => 'service@bstools.com', 'icon' => '👥', 'color' => 'bg-purple-500', 'order' => 3, 'is_active' => true],
            ['name_ar' => 'الشراكات', 'name_en' => 'Partnerships', 'description_ar' => 'للشراكات التجارية والتعاون', 'description_en' => 'For business partnerships and cooperation', 'phone' => '+20 123 456 7894', 'email' => 'partners@bstools.com', 'icon' => '🤝', 'color' => 'bg-orange-500', 'order' => 4, 'is_active' => true]
        ];
        foreach ($departments as $department) { Department::create($department); }

        // 7. Social Links
        $this->command->info('7️⃣ Seeding Social Links...');
        SocialLink::truncate();
        $socialLinks = [
            ['platform' => 'Facebook', 'url' => 'https://facebook.com/bstools', 'icon' => '📘', 'color' => 'bg-blue-600', 'order' => 1, 'is_active' => true],
            ['platform' => 'Twitter', 'url' => 'https://twitter.com/bstools', 'icon' => '🐦', 'color' => 'bg-sky-500', 'order' => 2, 'is_active' => true],
            ['platform' => 'LinkedIn', 'url' => 'https://linkedin.com/company/bstools', 'icon' => '💼', 'color' => 'bg-blue-700', 'order' => 3, 'is_active' => true],
            ['platform' => 'Instagram', 'url' => 'https://instagram.com/bstools', 'icon' => '📷', 'color' => 'bg-pink-500', 'order' => 4, 'is_active' => true],
            ['platform' => 'YouTube', 'url' => 'https://youtube.com/bstools', 'icon' => '📺', 'color' => 'bg-red-600', 'order' => 5, 'is_active' => true],
            ['platform' => 'WhatsApp', 'url' => 'https://wa.me/201234567890', 'icon' => '💬', 'color' => 'bg-green-500', 'order' => 6, 'is_active' => true]
        ];
        foreach ($socialLinks as $socialLink) { SocialLink::create($socialLink); }

        // 8. Team Members
        $this->command->info('8️⃣ Seeding Team Members...');
        TeamMember::truncate();
        $teamMembers = [
            ['name_ar' => 'أحمد محمد', 'name_en' => 'Ahmed Mohamed', 'role_ar' => 'مدير عام', 'role_en' => 'General Manager', 'experience_ar' => '15 سنة خبرة في إدارة المشاريع والتطوير', 'experience_en' => '15 years of experience in project management and development', 'specialty_ar' => 'إدارة المشاريع', 'specialty_en' => 'Project Management', 'image' => '👨‍💼', 'order' => 1, 'is_active' => true],
            ['name_ar' => 'سارة أحمد', 'name_en' => 'Sarah Ahmed', 'role_ar' => 'مديرة المبيعات', 'role_en' => 'Sales Manager', 'experience_ar' => '12 سنة خبرة في علاقات العملاء والتسويق', 'experience_en' => '12 years of experience in customer relations and marketing', 'specialty_ar' => 'علاقات العملاء', 'specialty_en' => 'Customer Relations', 'image' => '👩‍💼', 'order' => 2, 'is_active' => true],
            ['name_ar' => 'عمر حسن', 'name_en' => 'Omar Hassan', 'role_ar' => 'مهندس فني', 'role_en' => 'Technical Engineer', 'experience_ar' => '10 سنوات خبرة في الاستشارات التقنية والتركيب', 'experience_en' => '10 years of experience in technical consulting and installation', 'specialty_ar' => 'الاستشارات التقنية', 'specialty_en' => 'Technical Consulting', 'image' => '👨‍🔧', 'order' => 3, 'is_active' => true],
            ['name_ar' => 'فاطمة علي', 'name_en' => 'Fatima Ali', 'role_ar' => 'مديرة التقنية', 'role_en' => 'Technology Manager', 'experience_ar' => '8 سنوات خبرة في تطوير الأنظمة والتقنيات', 'experience_en' => '8 years of experience in systems and technology development', 'specialty_ar' => 'تطوير الأنظمة', 'specialty_en' => 'Systems Development', 'image' => '👩‍💻', 'order' => 4, 'is_active' => true]
        ];
        foreach ($teamMembers as $teamMember) { TeamMember::create($teamMember); }

        // 9. Company Values
        $this->command->info('9️⃣ Seeding Company Values...');
        CompanyValue::truncate();
        $companyValues = [
            ['title_ar' => 'الجودة', 'title_en' => 'Quality', 'description_ar' => 'نضمن أعلى مستويات الجودة في جميع منتجاتنا وخدماتنا', 'description_en' => 'We guarantee the highest levels of quality in all our products and services', 'icon' => '⭐', 'color' => 'from-yellow-500 to-orange-500', 'order' => 1, 'is_active' => true],
            ['title_ar' => 'الدعم', 'title_en' => 'Support', 'description_ar' => 'نقدم دعماً شاملاً لعملائنا قبل وبعد البيع', 'description_en' => 'We provide comprehensive support to our customers before and after sales', 'icon' => '🎯', 'color' => 'from-blue-500 to-cyan-500', 'order' => 2, 'is_active' => true],
            ['title_ar' => 'الابتكار', 'title_en' => 'Innovation', 'description_ar' => 'نسعى دائماً للتطوير وتقديم حلول مبتكرة', 'description_en' => 'We always strive for development and providing innovative solutions', 'icon' => '🚀', 'color' => 'from-purple-500 to-pink-500', 'order' => 3, 'is_active' => true],
            ['title_ar' => 'الموثوقية', 'title_en' => 'Reliability', 'description_ar' => 'نبني علاقات طويلة الأمد مع عملائنا بناءً على الثقة', 'description_en' => 'We build long-term relationships with our customers based on trust', 'icon' => '🛡️', 'color' => 'from-green-500 to-teal-500', 'order' => 4, 'is_active' => true]
        ];
        foreach ($companyValues as $companyValue) { CompanyValue::create($companyValue); }

        // 10. Company Milestones
        $this->command->info('🔟 Seeding Company Milestones...');
        CompanyMilestone::truncate();
        $milestones = [
            ['year' => '2009', 'event_ar' => 'تأسيس الشركة', 'event_en' => 'Company Foundation', 'description_ar' => 'بداية رحلتنا في عالم أدوات البناء', 'description_en' => 'The beginning of our journey in the world of construction tools', 'order' => 1, 'is_active' => true],
            ['year' => '2012', 'event_ar' => 'أول فرع', 'event_en' => 'First Branch', 'description_ar' => 'افتتاح أول فرع لنا في القاهرة', 'description_en' => 'Opening our first branch in Cairo', 'order' => 2, 'is_active' => true],
            ['year' => '2015', 'event_ar' => 'شراكات دولية', 'event_en' => 'International Partnerships', 'description_ar' => 'توقيع أول اتفاقيات مع موردين عالميين', 'description_en' => 'Signing first agreements with international suppliers', 'order' => 3, 'is_active' => true],
            ['year' => '2018', 'event_ar' => 'التوسع الإقليمي', 'event_en' => 'Regional Expansion', 'description_ar' => 'افتتاح فروع في 5 محافظات جديدة', 'description_en' => 'Opening branches in 5 new governorates', 'order' => 4, 'is_active' => true],
            ['year' => '2021', 'event_ar' => 'المنصة الرقمية', 'event_en' => 'Digital Platform', 'description_ar' => 'إطلاق موقعنا الإلكتروني ومنصة التجارة', 'description_en' => 'Launching our website and e-commerce platform', 'order' => 5, 'is_active' => true],
            ['year' => '2024', 'event_ar' => 'القيادة السوقية', 'event_en' => 'Market Leadership', 'description_ar' => 'أصبحنا أكبر موزع لأدوات البناء في المنطقة', 'description_en' => 'We became the largest distributor of construction tools in the region', 'order' => 6, 'is_active' => true]
        ];
        foreach ($milestones as $milestone) { CompanyMilestone::create($milestone); }

        // 11. FAQs (Replace existing with real data)
        $this->command->info('1️⃣1️⃣ Seeding FAQs...');
        FAQ::truncate();
        $faqs = [
            ['question_ar' => 'ما هي سياسة الإرجاع؟', 'question_en' => 'What is the return policy?', 'answer_ar' => 'يمكنك إرجاع المنتجات خلال 30 يوم من تاريخ الشراء بشرط أن تكون في حالتها الأصلية', 'answer_en' => 'You can return products within 30 days of purchase provided they are in their original condition', 'category' => 'general', 'order' => 1, 'is_active' => true],
            ['question_ar' => 'هل توفرون خصومات للكميات الكبيرة؟', 'question_en' => 'Do you offer bulk discounts?', 'answer_ar' => 'نعم، نوفر خصومات خاصة للطلبات الكبيرة والمشاريع التجارية. تواصل مع فريق المبيعات لمعرفة التفاصيل', 'answer_en' => 'Yes, we offer special discounts for large orders and commercial projects. Contact our sales team for details', 'category' => 'sales', 'order' => 2, 'is_active' => true],
            ['question_ar' => 'ما هي مناطق التوصيل المتاحة؟', 'question_en' => 'What are the available delivery areas?', 'answer_ar' => 'نوصل لجميع أنحاء مصر خلال 24-48 ساعة، مع توصيل مجاني للطلبات أكثر من 1000 جنيه', 'answer_en' => 'We deliver throughout Egypt within 24-48 hours, with free delivery for orders over 1000 EGP', 'category' => 'shipping', 'order' => 3, 'is_active' => true],
            ['question_ar' => 'كيف يمكنني الحصول على الدعم الفني؟', 'question_en' => 'How can I get technical support?', 'answer_ar' => 'يمكنك التواصل مع فريق الدعم الفني عبر الهاتف أو البريد الإلكتروني، متاحون 24/7', 'answer_en' => 'You can contact our technical support team by phone or email, available 24/7', 'category' => 'support', 'order' => 4, 'is_active' => true],
            ['question_ar' => 'هل تقدمون ضمان على المنتجات؟', 'question_en' => 'Do you provide warranty on products?', 'answer_ar' => 'نعم، نقدم ضمان لمدة عام على جميع المنتجات ضد عيوب التصنيع', 'answer_en' => 'Yes, we provide one year warranty on all products against manufacturing defects', 'category' => 'products', 'order' => 5, 'is_active' => true],
            ['question_ar' => 'ما هي طرق الدفع المتاحة؟', 'question_en' => 'What payment methods are available?', 'answer_ar' => 'نقبل الدفع نقداً عند الاستلام، بطاقات الائتمان، والتحويل البنكي', 'answer_en' => 'We accept cash on delivery, credit cards, and bank transfers', 'category' => 'payment', 'order' => 6, 'is_active' => true]
        ];
        foreach ($faqs as $faq) { FAQ::create($faq); }

        // 12. Certifications (Replace existing with real data)
        $this->command->info('1️⃣2️⃣ Seeding Certifications...');
        Certification::truncate();
        $certifications = [
            ['name_ar' => 'شهادة ISO 9001', 'name_en' => 'ISO 9001 Certificate', 'description_ar' => 'معتمدون في إدارة الجودة الشاملة', 'description_en' => 'Certified in total quality management', 'icon' => '🏅', 'order' => 1, 'is_active' => true],
            ['name_ar' => 'معايير OSHA', 'name_en' => 'OSHA Standards', 'description_ar' => 'ملتزمون بأعلى معايير الأمان والسلامة', 'description_en' => 'Committed to the highest safety and security standards', 'icon' => '🛡️', 'order' => 2, 'is_active' => true],
            ['name_ar' => 'شريك معتمد', 'name_en' => 'Authorized Partner', 'description_ar' => 'شريك رسمي للعلامات التجارية العالمية', 'description_en' => 'Official partner for global brands', 'icon' => '🤝', 'order' => 3, 'is_active' => true],
            ['name_ar' => 'رائد السوق', 'name_en' => 'Market Leader', 'description_ar' => 'الشركة الرائدة في المنطقة لثلاث سنوات متتالية', 'description_en' => 'Leading company in the region for three consecutive years', 'icon' => '👑', 'order' => 4, 'is_active' => true]
        ];
        foreach ($certifications as $certification) { Certification::create($certification); }

        $this->command->info('✅ Dynamic Content Management data seeded successfully!');
        $this->command->info('📊 Summary:');
        $this->command->info('   - Company Info: 1 record (BS Tools)');
        $this->command->info('   - Company Stats: 1 record (15+ years, 50K+ customers)');
        $this->command->info('   - Contact Info: 1 record (Complete contact details)');
        $this->command->info('   - Departments: 4 records (Sales, Technical, Customer Service, Partnerships)');
        $this->command->info('   - Social Links: 6 records (Facebook, Twitter, LinkedIn, Instagram, YouTube, WhatsApp)');
        $this->command->info('   - Team Members: 4 records (Ahmed, Sarah, Omar, Fatima)');
        $this->command->info('   - Company Values: 4 records (Quality, Support, Innovation, Reliability)');
        $this->command->info('   - Milestones: 6 records (2009-2024 timeline)');
        $this->command->info('   - Company Story: 1 record (Complete 3-paragraph story)');
        $this->command->info('   - Page Content: 1 record (About & Contact page content)');
        $this->command->info('   - FAQs: 6 records (General, Sales, Shipping, Support, Products, Payment)');
        $this->command->info('   - Certifications: 4 records (ISO 9001, OSHA, Partner, Market Leader)');
        $this->command->info('🎉 All Dynamic Content is ready for the APIs!');
    }
} 
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ContactController extends Controller
{
    /**
     * إرسال رسالة اتصال
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'project_type' => 'nullable|in:residential,commercial,industrial,other'
        ]);

        $ticketId = 'TKT-' . date('Y') . '-' . str_pad(ContactMessage::count() + 1, 3, '0', STR_PAD_LEFT);

        $contactMessage = ContactMessage::create([
            'id' => $ticketId,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'company' => $request->company,
            'subject' => $request->subject,
            'message' => $request->message,
            'project_type' => $request->project_type,
            'status' => ContactMessage::STATUS_NEW
        ]);

        // يمكن إضافة إرسال إيميل للإدارة هنا
        // Mail::to('admin@buildtools.com')->send(new NewContactMessage($contactMessage));

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال رسالتك بنجاح، سنتواصل معك قريباً',
            'data' => [
                'ticket_id' => $ticketId
            ]
        ], 201);
    }

    /**
     * جلب أقسام الاتصال المتاحة
     */
    public function departments(): JsonResponse
    {
        $departments = [
            [
                'id' => 'sales',
                'name' => [
                    'ar' => 'المبيعات',
                    'en' => 'Sales'
                ],
                'email' => 'sales@buildtools.com',
                'phone' => '+201234567890'
            ],
            [
                'id' => 'support',
                'name' => [
                    'ar' => 'الدعم الفني',
                    'en' => 'Technical Support'
                ],
                'email' => 'support@buildtools.com',
                'phone' => '+201234567891'
            ],
            [
                'id' => 'projects',
                'name' => [
                    'ar' => 'إدارة المشاريع',
                    'en' => 'Project Management'
                ],
                'email' => 'projects@buildtools.com',
                'phone' => '+201234567892'
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'departments' => $departments
            ]
        ]);
    }

    /**
     * جلب معلومات التواصل العامة
     */
    public function info(): JsonResponse
    {
        $contactInfo = [
            'company' => [
                'name' => [
                    'ar' => 'بيلد تولز',
                    'en' => 'BuildTools'
                ],
                'description' => [
                    'ar' => 'متجرك الموثوق لجميع مواد ومعدات البناء',
                    'en' => 'Your trusted store for all construction materials and equipment'
                ]
            ],
            'contact' => [
                'email' => 'info@buildtools.com',
                'phone' => '+201234567890',
                'whatsapp' => '+201234567890',
                'address' => [
                    'ar' => 'شارع التحرير، القاهرة، مصر',
                    'en' => 'Tahrir Street, Cairo, Egypt'
                ]
            ],
            'hours' => [
                'weekdays' => [
                    'ar' => 'السبت - الخميس: 9:00 ص - 6:00 م',
                    'en' => 'Saturday - Thursday: 9:00 AM - 6:00 PM'
                ],
                'friday' => [
                    'ar' => 'الجمعة: 2:00 م - 6:00 م',
                    'en' => 'Friday: 2:00 PM - 6:00 PM'
                ]
            ],
            'social_media' => [
                'facebook' => 'https://facebook.com/buildtools',
                'instagram' => 'https://instagram.com/buildtools',
                'linkedin' => 'https://linkedin.com/company/buildtools',
                'youtube' => 'https://youtube.com/buildtools'
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $contactInfo
        ]);
    }
} 
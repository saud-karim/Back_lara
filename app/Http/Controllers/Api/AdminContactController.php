<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AdminContactController extends Controller
{
    /**
     * إحصائيات رسائل الاتصال
     */
    public function stats(): JsonResponse
    {
        try {
            // إحصائيات عامة
            $totalMessages = ContactMessage::count();
            $newMessages = ContactMessage::new()->count();
            $inProgressMessages = ContactMessage::inProgress()->count();
            $resolvedMessages = ContactMessage::resolved()->count();
            $closedMessages = ContactMessage::where('status', ContactMessage::STATUS_CLOSED)->count();

            // إحصائيات هذا الشهر
            $thisMonthMessages = ContactMessage::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            // إحصائيات حسب نوع المشروع
            $projectTypes = ContactMessage::select('project_type', DB::raw('COUNT(*) as count'))
                ->whereNotNull('project_type')
                ->groupBy('project_type')
                ->pluck('count', 'project_type')
                ->toArray();

            // رسائل الأسبوع الماضي (لحساب النمو)
            $lastWeekMessages = ContactMessage::whereBetween('created_at', [
                now()->subWeeks(2)->startOfWeek(),
                now()->subWeek()->endOfWeek()
            ])->count();

            $thisWeekMessages = ContactMessage::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count();

            $weeklyGrowth = $lastWeekMessages > 0 
                ? round((($thisWeekMessages - $lastWeekMessages) / $lastWeekMessages) * 100, 1)
                : ($thisWeekMessages > 0 ? 100 : 0);

            // أحدث الرسائل (5 رسائل)
            $recentMessages = ContactMessage::with([])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get()
                ->map(function ($message) {
                    return [
                        'id' => $message->id,
                        'name' => $message->name,
                        'email' => $message->email,
                        'subject' => $message->subject,
                        'status' => $message->status,
                        'status_name' => $message->status_name,
                        'project_type' => $message->project_type,
                        'created_at' => $message->created_at->format('Y-m-d H:i'),
                        'time_ago' => $message->created_at->diffForHumans()
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'overview' => [
                        'total_messages' => $totalMessages,
                        'new_messages' => $newMessages,
                        'in_progress_messages' => $inProgressMessages,
                        'resolved_messages' => $resolvedMessages,
                        'closed_messages' => $closedMessages,
                        'this_month_messages' => $thisMonthMessages,
                        'weekly_growth' => $weeklyGrowth
                    ],
                    'status_breakdown' => [
                        'new' => $newMessages,
                        'in_progress' => $inProgressMessages,
                        'resolved' => $resolvedMessages,
                        'closed' => $closedMessages
                    ],
                    'project_types' => $projectTypes,
                    'recent_messages' => $recentMessages
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in AdminContactController@stats: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في جلب الإحصائيات',
                'error' => config('app.debug') ? $e->getMessage() : 'خطأ في الخادم'
            ], 500);
        }
    }

    /**
     * عرض جميع رسائل الاتصال مع الفلترة والبحث
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'per_page' => 'nullable|integer|min:1|max:100',
                'page' => 'nullable|integer|min:1',
                'status' => 'nullable|in:new,in_progress,resolved,closed',
                'project_type' => 'nullable|in:residential,commercial,industrial,other',
                'search' => 'nullable|string|max:255',
                'sort_by' => 'nullable|in:created_at,name,email,subject,status',
                'sort_order' => 'nullable|in:asc,desc',
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date|after_or_equal:date_from'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'بيانات غير صحيحة',
                    'errors' => $validator->errors()
                ], 422);
            }

            $query = ContactMessage::query();

            // تطبيق الفلاتر
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('project_type')) {
                $query->where('project_type', $request->project_type);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%")
                      ->orWhere('subject', 'LIKE', "%{$search}%")
                      ->orWhere('company', 'LIKE', "%{$search}%")
                      ->orWhere('id', 'LIKE', "%{$search}%");
                });
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            // الترتيب
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // الصفحات
            $perPage = min($request->get('per_page', 15), 100);
            $messages = $query->paginate($perPage);

            // تحضير البيانات
            $data = $messages->through(function ($message) {
                return [
                    'id' => $message->id,
                    'name' => $message->name,
                    'email' => $message->email,
                    'phone' => $message->phone,
                    'company' => $message->company,
                    'subject' => $message->subject,
                    'message' => Str::limit($message->message, 100),
                    'full_message' => $message->message,
                    'project_type' => $message->project_type,
                    'project_type_name' => $message->project_type_name,
                    'status' => $message->status,
                    'status_name' => $message->status_name,
                    'admin_notes' => $message->admin_notes,
                    'created_at' => $message->created_at->format('Y-m-d H:i'),
                    'updated_at' => $message->updated_at->format('Y-m-d H:i'),
                    'time_ago' => $message->created_at->diffForHumans()
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data->items(),
                'meta' => [
                    'current_page' => $messages->currentPage(),
                    'per_page' => $messages->perPage(),
                    'total' => $messages->total(),
                    'last_page' => $messages->lastPage(),
                    'from' => $messages->firstItem(),
                    'to' => $messages->lastItem()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in AdminContactController@index: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في جلب الرسائل',
                'error' => config('app.debug') ? $e->getMessage() : 'خطأ في الخادم'
            ], 500);
        }
    }

    /**
     * عرض رسالة واحدة بالتفصيل
     */
    public function show(string $id): JsonResponse
    {
        try {
            $message = ContactMessage::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $message->id,
                    'name' => $message->name,
                    'email' => $message->email,
                    'phone' => $message->phone,
                    'company' => $message->company,
                    'subject' => $message->subject,
                    'message' => $message->message,
                    'project_type' => $message->project_type,
                    'project_type_name' => $message->project_type_name,
                    'status' => $message->status,
                    'status_name' => $message->status_name,
                    'admin_notes' => $message->admin_notes,
                    'created_at' => $message->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $message->updated_at->format('Y-m-d H:i:s'),
                    'time_ago' => $message->created_at->diffForHumans(),
                    'formatted_date' => $message->created_at->format('d/m/Y - h:i A')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'الرسالة غير موجودة',
                'error' => 'الرسالة المطلوبة غير موجودة'
            ], 404);
        }
    }

    /**
     * تحديث حالة الرسالة وإضافة ملاحظات إدارية
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:new,in_progress,resolved,closed',
                'admin_notes' => 'nullable|string|max:1000'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'بيانات غير صحيحة',
                    'errors' => $validator->errors()
                ], 422);
            }

            $message = ContactMessage::findOrFail($id);
            
            $oldStatus = $message->status;
            $message->update([
                'status' => $request->status,
                'admin_notes' => $request->admin_notes
            ]);

            // إضافة log للتغيير
            Log::info("Contact message status updated", [
                'message_id' => $id,
                'old_status' => $oldStatus,
                'new_status' => $request->status,
                'admin_notes' => $request->admin_notes
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الرسالة بنجاح',
                'data' => [
                    'id' => $message->id,
                    'status' => $message->status,
                    'status_name' => $message->status_name,
                    'admin_notes' => $message->admin_notes,
                    'updated_at' => $message->updated_at->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                return response()->json([
                    'success' => false,
                    'message' => 'الرسالة غير موجودة',
                    'error' => 'الرسالة المطلوبة غير موجودة'
                ], 404);
            }

            Log::error('Error in AdminContactController@update: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تحديث الرسالة',
                'error' => config('app.debug') ? $e->getMessage() : 'خطأ في الخادم'
            ], 500);
        }
    }

    /**
     * حذف رسالة (soft delete)
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $message = ContactMessage::findOrFail($id);
            $message->delete();

            Log::info("Contact message deleted", [
                'message_id' => $id,
                'message_subject' => $message->subject
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الرسالة بنجاح'
            ]);

        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                return response()->json([
                    'success' => false,
                    'message' => 'الرسالة غير موجودة',
                    'error' => 'الرسالة المطلوبة غير موجودة'
                ], 404);
            }

            Log::error('Error in AdminContactController@destroy: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في حذف الرسالة',
                'error' => config('app.debug') ? $e->getMessage() : 'خطأ في الخادم'
            ], 500);
        }
    }

    /**
     * معالجة مجمعة للرسائل
     */
    public function bulkAction(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'action' => 'required|in:update_status,delete',
                'message_ids' => 'required|array|min:1',
                'message_ids.*' => 'required|string|exists:contact_messages,id',
                'status' => 'required_if:action,update_status|in:new,in_progress,resolved,closed',
                'admin_notes' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'بيانات غير صحيحة',
                    'errors' => $validator->errors()
                ], 422);
            }

            $messageIds = $request->message_ids;
            $processedCount = 0;

            if ($request->action === 'update_status') {
                $processedCount = ContactMessage::whereIn('id', $messageIds)
                    ->update([
                        'status' => $request->status,
                        'admin_notes' => $request->admin_notes,
                        'updated_at' => now()
                    ]);

                Log::info("Bulk status update for contact messages", [
                    'message_ids' => $messageIds,
                    'new_status' => $request->status,
                    'processed_count' => $processedCount
                ]);

                $action_message = 'تم تحديث حالة الرسائل بنجاح';

            } elseif ($request->action === 'delete') {
                $processedCount = ContactMessage::whereIn('id', $messageIds)->delete();

                Log::info("Bulk delete for contact messages", [
                    'message_ids' => $messageIds,
                    'processed_count' => $processedCount
                ]);

                $action_message = 'تم حذف الرسائل بنجاح';
            }

            return response()->json([
                'success' => true,
                'message' => $action_message,
                'data' => [
                    'processed_count' => $processedCount,
                    'total_requested' => count($messageIds)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in AdminContactController@bulkAction: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في المعالجة المجمعة',
                'error' => config('app.debug') ? $e->getMessage() : 'خطأ في الخادم'
            ], 500);
        }
    }

    /**
     * تحليلات متقدمة لرسائل الاتصال
     */
    public function analytics(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'period' => 'nullable|in:week,month,quarter,year',
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date|after_or_equal:date_from'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'بيانات غير صحيحة',
                    'errors' => $validator->errors()
                ], 422);
            }

            $period = $request->get('period', 'month');
            
            // تحديد الفترة الزمنية
            $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : now()->subMonths(1);
            $dateTo = $request->date_to ? Carbon::parse($request->date_to) : now();

            // الرسائل خلال الفترة
            $messages = ContactMessage::whereBetween('created_at', [$dateFrom, $dateTo]);

            // إحصائيات حسب الوقت
            $timeStats = $messages->get()
                ->groupBy(function ($message) use ($period) {
                    switch ($period) {
                        case 'week':
                            return $message->created_at->format('Y-W');
                        case 'quarter':
                            return $message->created_at->format('Y') . '-Q' . $message->created_at->quarter;
                        case 'year':
                            return $message->created_at->format('Y');
                        default: // month
                            return $message->created_at->format('Y-m');
                    }
                })
                ->map(function ($group) {
                    return [
                        'count' => $group->count(),
                        'new' => $group->where('status', 'new')->count(),
                        'resolved' => $group->where('status', 'resolved')->count()
                    ];
                });

            // أكثر المواضيع شيوعاً
            $commonSubjects = ContactMessage::whereBetween('created_at', [$dateFrom, $dateTo])
                ->select('subject', DB::raw('COUNT(*) as count'))
                ->groupBy('subject')
                ->orderByDesc('count')
                ->take(10)
                ->get();

            // أوقات الذروة (ساعات اليوم)
            $peakHours = ContactMessage::whereBetween('created_at', [$dateFrom, $dateTo])
                ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as count'))
                ->groupBy('hour')
                ->orderBy('hour')
                ->get()
                ->keyBy('hour');

            // معدل الاستجابة
            $averageResponseTime = ContactMessage::whereBetween('created_at', [$dateFrom, $dateTo])
                ->where('status', '!=', 'new')
                ->avg(DB::raw('TIMESTAMPDIFF(HOUR, created_at, updated_at)'));

            return response()->json([
                'success' => true,
                'data' => [
                    'period' => $period,
                    'date_range' => [
                        'from' => $dateFrom->format('Y-m-d'),
                        'to' => $dateTo->format('Y-m-d')
                    ],
                    'time_stats' => $timeStats,
                    'common_subjects' => $commonSubjects,
                    'peak_hours' => $peakHours,
                    'average_response_time_hours' => round($averageResponseTime, 1),
                    'summary' => [
                        'total_messages' => $messages->count(),
                        'resolution_rate' => $messages->count() > 0 
                            ? round(($messages->where('status', 'resolved')->count() / $messages->count()) * 100, 1)
                            : 0
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in AdminContactController@analytics: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في جلب التحليلات',
                'error' => config('app.debug') ? $e->getMessage() : 'خطأ في الخادم'
            ], 500);
        }
    }
} 
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactMessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'company' => $this->company,
            'subject' => $this->subject,
            'message' => $this->message,
            'message_excerpt' => \Str::limit($this->message, 100),
            'project_type' => $this->project_type,
            'project_type_name' => $this->getProjectTypeName(),
            'status' => $this->status,
            'status_name' => $this->getStatusName(),
            'status_badge' => $this->getStatusBadge(),
            'admin_notes' => $this->admin_notes,
            'is_new' => $this->isNew(),
            'is_urgent' => $this->isUrgent(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'formatted_date' => $this->created_at->format('d/m/Y - h:i A'),
            'time_ago' => $this->created_at->diffForHumans(),
            'days_since_created' => $this->created_at->diffInDays(now())
        ];
    }

    /**
     * Get project type name in Arabic
     */
    private function getProjectTypeName(): string
    {
        $types = [
            'residential' => 'سكني',
            'commercial' => 'تجاري',
            'industrial' => 'صناعي',
            'other' => 'أخرى'
        ];

        return $types[$this->project_type] ?? $this->project_type ?? 'غير محدد';
    }

    /**
     * Get status name in Arabic
     */
    private function getStatusName(): string
    {
        $statuses = [
            'new' => 'جديد',
            'in_progress' => 'قيد المعالجة',
            'resolved' => 'تم الحل',
            'closed' => 'مغلق'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Get status badge class for frontend
     */
    private function getStatusBadge(): array
    {
        $badges = [
            'new' => [
                'class' => 'badge-primary',
                'color' => 'blue',
                'text' => 'جديد'
            ],
            'in_progress' => [
                'class' => 'badge-warning',
                'color' => 'orange',
                'text' => 'قيد المعالجة'
            ],
            'resolved' => [
                'class' => 'badge-success',
                'color' => 'green',
                'text' => 'تم الحل'
            ],
            'closed' => [
                'class' => 'badge-secondary',
                'color' => 'gray',
                'text' => 'مغلق'
            ]
        ];

        return $badges[$this->status] ?? $badges['new'];
    }

    /**
     * Check if message is urgent (older than 24 hours and still new)
     */
    private function isUrgent(): bool
    {
        return $this->status === 'new' && $this->created_at->diffInHours(now()) > 24;
    }
} 
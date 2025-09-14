<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminCustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'company' => $this->company,
            'avatar' => $this->avatar ? asset('storage/' . $this->avatar) : null,
            'status' => $this->status,
            'email_verified_at' => $this->email_verified_at,
            'role' => $this->role,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'last_activity' => $this->last_activity,
            'registration_source' => $this->registration_source,
            
            // Computed attributes
            'orders_count' => $this->orders_count ?? 0,
            'total_spent' => number_format($this->total_spent ?? 0, 2),
            'currency' => 'EGP',
            'favorite_payment_method' => $this->favorite_payment_method ?? 'cash',
            'addresses_count' => $this->addresses_count ?? 0,
            'is_verified' => $this->isVerified(),
            'has_recent_activity' => $this->hasRecentActivity(),
            
            // Relationships (when loaded)
            'recent_orders' => OrderResource::collection($this->whenLoaded('recentOrders')),
            'addresses' => AddressResource::collection($this->whenLoaded('addresses')),
            'customer_notes' => CustomerNoteResource::collection($this->whenLoaded('customerNotes')),
        ];
    }
}

class CustomerNoteResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'note' => $this->note,
            'type' => $this->type,
            'type_name' => $this->type_name,
            'admin' => [
                'id' => $this->admin->id,
                'name' => $this->admin->name,
            ],
            'created_at' => $this->created_at,
        ];
    }
}

class AddressResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->type ?? 'home',
            'name' => $this->name ?? 'العنوان الرئيسي',
            'city' => $this->city,
            'street' => $this->street,
            'building' => $this->building_number,
            'is_default' => $this->is_default ?? false,
        ];
    }
}

class OrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $this->status,
            'total_amount' => number_format($this->total_amount, 2),
            'items_count' => $this->items_count ?? $this->orderItems->count() ?? 0,
            'created_at' => $this->created_at,
        ];
    }
}

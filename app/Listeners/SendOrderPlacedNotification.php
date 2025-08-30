<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Notifications\OrderPlacedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOrderPlacedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderPlaced $event): void
    {
        $order = $event->order;
        $user = $order->user;

        // Send notification to customer
        $user->notify(new OrderPlacedNotification($order));

        // Create notification record
        $user->notifications()->create([
            'type' => 'order',
            'message' => "Your order #{$order->id} has been placed successfully.",
        ]);
    }
} 
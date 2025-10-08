<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('ar_SA');
        
        // Get existing users and products
        $users = User::where('role', 'customer')->limit(10)->get();
        $products = Product::limit(20)->get();
        $admin = User::where('role', 'admin')->first();

        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->warn('No users or products found. Skipping order seeding.');
            return;
        }

        $statuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'];
        $paymentMethods = ['credit_card', 'debit_card', 'cash_on_delivery', 'bank_transfer'];
        $paymentStatuses = ['pending', 'paid', 'failed', 'refunded'];

        // Create 30 orders
        for ($i = 1; $i <= 30; $i++) {
            $user = $users->random();
            $status = $faker->randomElement($statuses);
            $paymentStatus = $status === 'delivered' ? 'paid' : $faker->randomElement($paymentStatuses);
            
            $order = Order::create([
                'order_number' => 'ORD-' . date('Y') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'user_id' => $user->id,
                'status' => $status,
                'subtotal' => $faker->numberBetween(100, 5000),
                'tax_amount' => 0, // Will be calculated
                'shipping_amount' => $faker->numberBetween(25, 100),
                'discount_amount' => $faker->optional(0.3)->numberBetween(10, 200) ?? 0,
                'total_amount' => 0, // Will be calculated
                'currency' => 'EGP',
                'payment_method' => $faker->randomElement($paymentMethods),
                'payment_status' => $paymentStatus,
                'shipping_address' => [
                    'name' => $user->name,
                    'phone' => $faker->phoneNumber,
                    'street' => $faker->streetAddress,
                    'city' => $faker->randomElement(['القاهرة', 'الرياض', 'جدة', 'الإسكندرية', 'الدمام']),
                    'district' => $faker->randomElement(['وسط البلد', 'المعادي', 'الزمالك', 'النخيل', 'الروضة']),
                    'governorate' => $faker->randomElement(['القاهرة', 'الرياض', 'مكة المكرمة', 'الإسكندرية', 'الشرقية']),
                    'postal_code' => $faker->numberBetween(10000, 99999),
                ],
                'notes' => $faker->optional(0.4)->sentence(),
                'tracking_number' => in_array($status, ['shipped', 'delivered']) ? 
                    'TRK' . $faker->numberBetween(100000, 999999) : null,
                'estimated_delivery' => in_array($status, ['confirmed', 'processing', 'shipped']) ? 
                    $faker->dateTimeBetween('now', '+10 days') : null,
                'created_at' => $faker->dateTimeBetween('-3 months', 'now'),
            ]);

            // Create order items (2-5 items per order)
            $itemsCount = $faker->numberBetween(2, 5);
            $subtotal = 0;

            for ($j = 0; $j < $itemsCount; $j++) {
                $product = $products->random();
                $quantity = $faker->numberBetween(1, 3);
                $unitPrice = $product->price ?? $faker->numberBetween(50, 1000);
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                ]);

                $subtotal += ($quantity * $unitPrice);
            }

            // Calculate totals
            $taxAmount = $subtotal * 0.14; // 14% VAT
            $totalAmount = $subtotal + $taxAmount + $order->shipping_amount - $order->discount_amount;

            $order->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
            ]);

            // Create status history
            $statusFlow = $this->getStatusFlow($status);
            
            foreach ($statusFlow as $index => $historyStatus) {
                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'user_id' => $index === 0 ? null : ($admin?->id ?? null), // First status change is automatic
                    'status' => $historyStatus,
                    'previous_status' => $index === 0 ? null : $statusFlow[$index - 1],
                    'notes' => $faker->optional(0.6)->sentence(),
                    'metadata' => $this->getStatusMetadata($historyStatus, $faker),
                    'created_at' => $order->created_at->addHours($index * 6),
                ]);
            }
        }

        $this->command->info('Created 30 orders with items and status history.');
    }

    /**
     * Get the status flow for an order based on its current status.
     */
    private function getStatusFlow(string $currentStatus): array
    {
        $flows = [
            'pending' => ['pending'],
            'confirmed' => ['pending', 'confirmed'],
            'processing' => ['pending', 'confirmed', 'processing'],
            'shipped' => ['pending', 'confirmed', 'processing', 'shipped'],
            'delivered' => ['pending', 'confirmed', 'processing', 'shipped', 'delivered'],
            'cancelled' => ['pending', 'cancelled'],
        ];

        return $flows[$currentStatus] ?? ['pending'];
    }

    /**
     * Get metadata for status change.
     */
    private function getStatusMetadata(string $status, $faker): array
    {
        $metadata = [];

        switch ($status) {
            case 'confirmed':
                $metadata['payment_verified'] = true;
                break;
            case 'processing':
                $metadata['warehouse_assigned'] = $faker->name;
                break;
            case 'shipped':
                $metadata['carrier'] = $faker->randomElement(['أرامكس', 'فيدكس', 'DHL', 'البريد السعودي']);
                $metadata['tracking_url'] = 'https://track.example.com/' . $faker->uuid;
                break;
            case 'delivered':
                $metadata['delivered_to'] = $faker->name;
                $metadata['signature_required'] = $faker->boolean;
                break;
            case 'cancelled':
                $metadata['cancellation_reason'] = $faker->randomElement([
                    'طلب العميل',
                    'نفاد المخزون',
                    'مشكلة في الدفع',
                    'عدم توفر التوصيل'
                ]);
                break;
        }

        return $metadata;
    }
}

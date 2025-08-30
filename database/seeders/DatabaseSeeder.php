<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            PermissionSeeder::class,
            BrandSeeder::class, 
            EcommerceSeeder::class,
            ExtendedDataSeeder::class,
            AssignRolesToUsersSeeder::class,
        ]);

        $this->command->info('🎉 All seeders completed successfully!');
        $this->command->info('🔐 Test Accounts Created:');
        $this->command->info('   👨‍💼 Admin: admin@construction.com | password: password');
        $this->command->info('   👤 Customer: customer@construction.com | password: password');
        $this->command->info('   🏭 Supplier: supplier@construction.com | password: password');
        $this->command->info('📦 Database now contains rich test data for API testing!');
    }
}

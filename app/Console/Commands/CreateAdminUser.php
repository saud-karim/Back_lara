<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin {email=admin@shop.com} {password=admin123}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user with token for API access';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $this->info('🔧 Creating Admin User...');
        $this->info('========================');

        try {
            // Check if user exists
            $admin = User::where('email', $email)->first();
            
            if (!$admin) {
                // Create new admin user
                $admin = User::create([
                    'name' => 'Admin User',
                    'email' => $email,
                    'password' => Hash::make($password),
                    'email_verified_at' => now()
                ]);
                $this->info("✅ Created new admin user: {$email}");
            } else {
                $this->info("✅ Admin user already exists: {$email}");
            }

            // Assign admin role if Spatie Permission exists
            if (class_exists('Spatie\Permission\Models\Role')) {
                $this->info('🔑 Assigning admin role...');
                
                try {
                    $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
                    
                    if (!$admin->hasRole('admin')) {
                        $admin->assignRole('admin');
                        $this->info('✅ Admin role assigned');
                    } else {
                        $this->info('✅ User already has admin role');
                    }
                } catch (\Exception $e) {
                    $this->warn('⚠️ Could not assign role: ' . $e->getMessage());
                }
            }

            // Create token
            $this->info('🎫 Creating API token...');
            $token = $admin->createToken('admin-api-token')->plainTextToken;

            // Display results
            $this->newLine();
            $this->info('🎉 SUCCESS! Admin user created with API access');
            $this->info('==============================================');
            $this->line("📧 Email: {$email}");
            $this->line("🔐 Password: {$password}");
            $this->line("🎫 API Token: {$token}");

            $this->newLine();
            $this->info('📱 Test the APIs now:');
            $this->info('====================');
            $this->line('curl -X GET "http://localhost/api/v1/admin/company-info" \\');
            $this->line("     -H \"Authorization: Bearer {$token}\" \\");
            $this->line('     -H "Accept: application/json"');

            $this->newLine();
            $this->info('🔗 Available Content Management APIs:');
            $this->info('=====================================');
            $this->line('✅ GET /api/v1/admin/company-info');
            $this->line('✅ GET /api/v1/admin/company-stats');
            $this->line('✅ GET /api/v1/admin/contact-info');
            $this->line('✅ GET /api/v1/admin/departments');
            $this->line('✅ GET /api/v1/admin/social-links');
            $this->line('✅ GET /api/v1/admin/team-members');
            $this->line('✅ GET /api/v1/admin/company-values');
            $this->line('✅ GET /api/v1/admin/company-milestones');
            $this->line('✅ GET /api/v1/admin/company-story');
            $this->line('✅ GET /api/v1/admin/page-content');
            $this->line('✅ GET /api/v1/admin/faqs');
            $this->line('✅ GET /api/v1/admin/certifications');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Error creating admin user: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

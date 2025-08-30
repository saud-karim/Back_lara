<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AssignRolesToUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Find admin user and assign admin role
        $adminUser = User::where('email', 'admin@construction.com')->first();
        if ($adminUser) {
            $adminUser->assignRole('admin');
        }

        // Find customer user and assign customer role
        $customerUser = User::where('email', 'customer@construction.com')->first();
        if ($customerUser) {
            $customerUser->assignRole('customer');
        }

        // Find supplier user and assign supplier role
        $supplierUser = User::where('email', 'supplier@construction.com')->first();
        if ($supplierUser) {
            $supplierUser->assignRole('supplier');
        }

        // Assign roles based on the 'role' field for all other users
        User::whereNotIn('email', [
            'admin@construction.com', 
            'customer@construction.com', 
            'supplier@construction.com'
        ])->get()->each(function ($user) {
            if ($user->role && Role::where('name', $user->role)->exists()) {
                $user->assignRole($user->role);
            }
        });
    }
}

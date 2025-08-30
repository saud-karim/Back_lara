<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Products
            'view products',
            'create products',
            'edit products',
            'delete products',
            
            // Categories
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',
            
            // Orders
            'view orders',
            'create orders',
            'update orders',
            'delete orders',
            
            // Suppliers
            'view suppliers',
            'create suppliers',
            'edit suppliers',
            'delete suppliers',
            
            // Users
            'view users',
            'create users',
            'edit users',
            'delete users',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());

        $customerRole = Role::firstOrCreate(['name' => 'customer']);
        $customerRole->syncPermissions([
            'view products',
            'view categories',
            'create orders',
            'view orders',
            'view suppliers',
        ]);

        $supplierRole = Role::firstOrCreate(['name' => 'supplier']);
        $supplierRole->syncPermissions([
            'view products',
            'create products',
            'edit products',
            'view categories',
            'view orders',
            'view suppliers',
            'edit suppliers',
        ]);
    }
} 
<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AuthService
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    /**
     * Register a new user.
     */
    public function register(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = $this->userRepository->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => $data['role'] ?? 'customer',
                'address' => $data['address'] ?? null,
                'phone' => $data['phone'] ?? null,
            ]);

            // Assign role using Spatie Permission
            try {
                $user->assignRole($user->role);
            } catch (\Exception $e) {
                // If role doesn't exist, create default roles first
                $this->createDefaultRoles();
                $user->assignRole($user->role);
            }

            return $user;
        });
    }

    /**
     * Login user with credentials.
     */
    public function login(array $credentials): ?User
    {
        $user = $this->userRepository->findByEmail($credentials['email']);

        if ($user && Hash::check($credentials['password'], $user->password)) {
            return $user;
        }

        return null;
    }

    /**
     * Reset password for user.
     */
    public function resetPassword(string $email, string $newPassword): bool
    {
        $user = $this->userRepository->findByEmail($email);

        if ($user) {
            $this->userRepository->update($user->id, [
                'password' => Hash::make($newPassword),
            ]);
            return true;
        }

        return false;
    }

    /**
     * Update user profile.
     */
    public function updateProfile(User $user, array $data): User
    {
        $updateData = array_filter($data, function ($value) {
            return !is_null($value);
        });

        if (isset($updateData['password'])) {
            $updateData['password'] = Hash::make($updateData['password']);
        }

        return $this->userRepository->update($user->id, $updateData);
    }

    /**
     * Create default roles if they don't exist
     */
    private function createDefaultRoles(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create basic permissions if they don't exist
        $basicPermissions = [
            'view products',
            'view categories',
            'create orders',
            'view orders',
            'view suppliers',
        ];

        foreach ($basicPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create customer role if it doesn't exist
        if (!Role::where('name', 'customer')->exists()) {
            $customerRole = Role::create(['name' => 'customer']);
            $customerRole->givePermissionTo($basicPermissions);
        }

        // Create admin role if it doesn't exist
        if (!Role::where('name', 'admin')->exists()) {
            $adminRole = Role::create(['name' => 'admin']);
            $adminRole->givePermissionTo(Permission::all());
        }

        // Create supplier role if it doesn't exist  
        if (!Role::where('name', 'supplier')->exists()) {
            $supplierRole = Role::create(['name' => 'supplier']);
            $supplierRole->givePermissionTo($basicPermissions);
        }
    }
} 
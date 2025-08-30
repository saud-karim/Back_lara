<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository
{
    /**
     * Create a new user.
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * Find user by ID.
     */
    public function find(int $id): User
    {
        return User::findOrFail($id);
    }

    /**
     * Find user by email.
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Update user.
     */
    public function update(int $id, array $data): User
    {
        $user = $this->find($id);
        $user->update($data);
        return $user;
    }

    /**
     * Delete user.
     */
    public function delete(int $id): bool
    {
        $user = $this->find($id);
        return $user->delete();
    }

    /**
     * Get all users with pagination.
     */
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        $query = User::query();

        if (isset($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Get users by role.
     */
    public function getByRole(string $role): Collection
    {
        return User::where('role', $role)->get();
    }

    /**
     * Get customers.
     */
    public function getCustomers(): Collection
    {
        return $this->getByRole('customer');
    }

    /**
     * Get suppliers.
     */
    public function getSuppliers(): Collection
    {
        return $this->getByRole('supplier');
    }

    /**
     * Get admins.
     */
    public function getAdmins(): Collection
    {
        return $this->getByRole('admin');
    }
} 
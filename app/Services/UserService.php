<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserService
{
    public function filter(string $search = '', string $role = '', int $perPage = 5): LengthAwarePaginator
    {
        return User::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('nip', 'like', '%' . $search . '%');
                });
            })
            ->when($role, function ($query) use ($role) {
                $query->where('role', $role);
            })
            ->paginate($perPage);
    }

    public function totalUsers(): int
    {
        return User::count();
    }

    public function countByRole(\App\UserRole $role): int
    {
        return User::where('role', $role->value)->count();
    }

    public function getAvailableRoles(): array
    {
        $roles = collect(\App\UserRole::cases())->map(function ($role) {
            return ['id' => $role->value, 'name' => ucfirst($role->value)];
        })->toArray();

        array_unshift($roles, ['id' => '', 'name' => 'Semua Role']);

        return $roles;
    }

    public function tableHeaders(): array
    {
        return [
            ['key' => 'id', 'label' => '#'],
            ['key' => 'name', 'label' => 'Nama'],
            ['key' => 'email', 'label' => 'Email'],
            ['key' => 'nip', 'label' => 'NIP'],
            ['key' => 'jabatan', 'label' => 'Jabatan'],
            ['key' => 'role', 'label' => 'Role'],
        ];
    }
}

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
            ['key' => 'serial_number', 'label' => '#'],
            ['key' => 'name', 'label' => 'Nama'],
            ['key' => 'email', 'label' => 'Email'],
            ['key' => 'nip', 'label' => 'NIP'],
            ['key' => 'jabatan', 'label' => 'Jabatan'],
            ['key' => 'bagian', 'label' => 'Bagian'],
        ];
    }

    public function createUser(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($data['password']),
            'nip' => $data['nip'],
            'golongan' => $data['golongan'],
            'jabatan' => $data['jabatan'],
            'role' => \App\UserRole::PEGAWAI, // Role locked to pegawai
            'bagian' => $data['bagian'],
        ]);
    }

    public function importUsers(string $filePath): array
    {
        $imported = 0;
        $skipped = 0;

        (new \Rap2hpoutre\FastExcel\FastExcel)->import($filePath, function ($line) use (&$imported, &$skipped) {
            $email = $line['Email'] ?? null;
            $nip = $line['NIP'] ?? null;

            if (empty($email) || empty($nip)) {
                $skipped++;
                return null;
            }

            // Check if user exists
            $exists = User::where('email', $email)->orWhere('nip', $nip)->exists();

            if ($exists) {
                $skipped++;
                return null;
            }

            $imported++;
            
            // Map the Bagian text to enum value if necessary
            $bagianText = $line['Bagian'] ?? '';
            $bagianValue = null;
            foreach (\App\Bagian::cases() as $bagianEnum) {
                if (strtolower($bagianEnum->label()) === strtolower(trim($bagianText)) || strtolower($bagianEnum->value) === strtolower(trim($bagianText))) {
                    $bagianValue = $bagianEnum->value;
                    break;
                }
            }

            return User::create([
                'name' => $line['Nama'] ?? 'User Baru',
                'email' => $email,
                'password' => \Illuminate\Support\Facades\Hash::make($nip),
                'nip' => $nip,
                'golongan' => $line['Golongan'] ?? null,
                'jabatan' => $line['Jabatan'] ?? null,
                'role' => \App\UserRole::PEGAWAI,
                'bagian' => $bagianValue,
            ]);
        });

        return [
            'imported' => $imported,
            'skipped' => $skipped
        ];
    }
}

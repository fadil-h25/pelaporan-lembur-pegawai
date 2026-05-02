<?php

namespace Database\Seeders;

use App\Models\User;
use App\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SystemSettingSeeder::class,
        ]);
        // User::factory(10)->create();

        User::create([
            'name' => 'Administrator Lembur',
            'email' => 'admin1@gmail.com',
            'password' => Hash::make('password123'), // Ganti dengan password yang aman
            'nip' => '',
            'golongan' => 'IV/a',
            'jabatan' => '',
            'role' => UserRole::ADMIN->value,
        ]);
        User::create([
            'name' => 'Operator Lembur',
            'email' => 'operator1@gmail.com',
            'password' => Hash::make('password123'), // Ganti dengan password yang aman
            'nip' => '',
            'golongan' => 'IV/a',
            'jabatan' => '',
            'role' => UserRole::OPERATOR->value,
        ]);

        User::create([
            'name' => 'Pegawai Lembur',
            'email' => 'pegawai1@gmail.com',
            'password' => Hash::make('password123'), // Ganti dengan password yang aman
            'nip' => '',
            'golongan' => 'IV/a',
            'jabatan' => '',
            'role' => UserRole::PEGAWAI->value,
        ]);
    }
}

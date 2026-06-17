<?php

/**
 * DEPRECATED: Seeder ini tidak digunakan lagi.
 * System settings sekarang menggunakan config file (config/system.php)
 * Seeder ini tetap ada untuk backward compatibility tapi tidak direkomendasikan untuk digunakan.
 */

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SystemSetting;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SystemSetting::create([
            'nama_kasek' => 'AWALUDDIN MUSTAFA, S.E., M.Si',
            'nip_kasek' => '19740712 200212 1 006',
            'akhiran_surat' => '/SL/SPKL/SN/',
        ]);
    }
}

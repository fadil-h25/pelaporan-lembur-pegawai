<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * DEPRECATED: Migration ini tidak digunakan lagi.
 * System settings sekarang menggunakan config file (config/system.php)
 * Migration ini tetap ada untuk backward compatibility tapi tidak direkomendasikan untuk digunakan.
 */
return new class extends Migration
{
    public function up()
    {
        // DEPRECATED: Tidak perlu membuat tabel lagi
        // Schema::create('system_settings', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('nama_kasek')->default('AWALUDDIN MUSTAFA, S.E., M.Si');
        //     $table->string('nip_kasek')->default('19740712 200212 1 006');
        //     $table->string('akhiran_surat')->default('/SPKL/SN/');
        //     $table->timestamps();
        // });
    }

    public function down(): void
    {
        // DEPRECATED: Tidak perlu drop tabel
        // Schema::dropIfExists('system_settings');
    }
};

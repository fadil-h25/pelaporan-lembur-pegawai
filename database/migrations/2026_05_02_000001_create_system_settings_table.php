<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kasek')->default('AWALUDDIN MUSTAFA, S.E., M.Si');
            $table->string('nip_kasek')->default('19740712 200212 1 006');
            $table->string('akhiran_surat')->default('/SPKL/SN/');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};

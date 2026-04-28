<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lemburs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nama');
            $table->string('nip');
            $table->string('golongan');
            $table->string('jabatan');
            $table->date('tanggal_lembur');
            $table->integer('jumlah_jam');
            $table->string('pembebanan_anggaran');
            $table->text('rencana_kerja');
            $table->text('hasil_kerja')->nullable();
            $table->string('status')->default('pending');
            $table->string('nama_kasek')->default('AWALUDDIN MUSTAFA, S.E., M.Si');
            $table->string('nip_kasek')->default('19740712 200212 1 006');
            $table->timestamps();
        }); // <-- Pastikan ada tutup kurung dan titik koma di sini!
    }

    public function down(): void
    {
        Schema::dropIfExists('lemburs');
    }
};

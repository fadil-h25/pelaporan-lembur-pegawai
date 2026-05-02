<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('lemburs', function (Blueprint $table) {

            $table->integer('no_utama')->nullable()->after('id');
            $table->integer('no_sisipan')->default(0)->after('no_utama');
        });
    }

    public function down(): void
    {
        Schema::table('lemburs', function (Blueprint $table) {
            $table->dropColumn(['no_utama', 'no_sisipan']);
        });
    }
};

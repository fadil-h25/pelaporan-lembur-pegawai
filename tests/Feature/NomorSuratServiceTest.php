<?php

use App\Models\Lembur;
use App\Models\User;
use App\Services\NomorSuratService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('pengambilan nomor sisipan bertingkat sesuai kasus user', function () {
    $service = new NomorSuratService();
    $user = User::factory()->create();

    // 05 Jan -> 0001
    Lembur::create([
        'user_id' => $user->id,
        'nama' => 'User A',
        'nip' => '123',
        'golongan' => 'IV/a',
        'jabatan' => 'Staff',
        'tanggal_lembur' => '2026-01-05',
        'jumlah_jam' => 2,
        'pembebanan_anggaran' => 'DIPA',
        'rencana_kerja' => 'Kerja A',
        'no_utama' => 1,
        'no_sisipan' => '0',
    ]);

    // 10 Jan -> 0002
    Lembur::create([
        'user_id' => $user->id,
        'nama' => 'User B',
        'nip' => '124',
        'golongan' => 'IV/a',
        'jabatan' => 'Staff',
        'tanggal_lembur' => '2026-01-10',
        'jumlah_jam' => 2,
        'pembebanan_anggaran' => 'DIPA',
        'rencana_kerja' => 'Kerja B',
        'no_utama' => 2,
        'no_sisipan' => '0',
    ]);

    // 07 Jan -> 0001.1
    Lembur::create([
        'user_id' => $user->id,
        'nama' => 'User C',
        'nip' => '125',
        'golongan' => 'IV/a',
        'jabatan' => 'Staff',
        'tanggal_lembur' => '2026-01-07',
        'jumlah_jam' => 2,
        'pembebanan_anggaran' => 'DIPA',
        'rencana_kerja' => 'Kerja C',
        'no_utama' => 1,
        'no_sisipan' => '1',
    ]);

    // 09 Jan -> 0001.2
    Lembur::create([
        'user_id' => $user->id,
        'nama' => 'User D',
        'nip' => '126',
        'golongan' => 'IV/a',
        'jabatan' => 'Staff',
        'tanggal_lembur' => '2026-01-09',
        'jumlah_jam' => 2,
        'pembebanan_anggaran' => 'DIPA',
        'rencana_kerja' => 'Kerja D',
        'no_utama' => 1,
        'no_sisipan' => '2',
    ]);

    // Sekarang ambil nomor untuk 8 Januari
    $nomor = $service->generate('2026-01-08');

    expect($nomor['no_utama'])->toBe(1);
    expect($nomor['no_sisipan'])->toBe('1.1');
});

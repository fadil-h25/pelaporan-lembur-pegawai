<?php

namespace App\Services;

use App\Models\Lembur;
use Carbon\Carbon;

class NomorSuratService
{
    /**
     * Generate nomor utama dan nomor sisipan.
     * Aturan: 
     * 1. Jika tanggal baru > tanggal yang ada, no_utama naik, sisipan 0.
     * 2. Jika tanggal "nyelip" di antara data lama, no_utama ikut induk, sisipan++.
     */
    public function generate(string $tanggalLembur): array
    {
        $tanggal = Carbon::parse($tanggalLembur)->startOfDay();
        $tanggalTerbesar = Lembur::max('tanggal_lembur');

        // 1. LOGIKA TANGGAL BARU (Urutan Normal)
        if (!$tanggalTerbesar || $tanggal > Carbon::parse($tanggalTerbesar)) {
            $noUtamaTerbesar = Lembur::max('no_utama') ?? (config('app_settings.surat.nomor_awal', 1) - 1);
            return [
                'no_utama' => $noUtamaTerbesar + 1,
                'no_sisipan' => 0,
            ];
        }

        // 2. LOGIKA TANGGAL SISIPAN (Data Telat Masuk)
        // Cari data terdekat yang tanggalnya <= tanggal input untuk jadi "induk"
        $induk = Lembur::whereDate('tanggal_lembur', '<=', $tanggal)
            ->orderBy('tanggal_lembur', 'desc')
            ->orderBy('no_utama', 'desc')
            ->first();

        // Jika tidak ada induk sama sekali (kasus ekstrim), pakai nomor awal
        $noUtama = $induk ? $induk->no_utama : config('app_settings.surat.nomor_awal', 1);

        // Cari sisipan terbesar di nomor induk tersebut agar tidak duplikat
        $sisipanTerbesar = Lembur::where('no_utama', $noUtama)->max('no_sisipan') ?? 0;

        return [
            'no_utama' => $noUtama,
            'no_sisipan' => $sisipanTerbesar + 1,
        ];
    }

    /**
     * Format nomor surat lengkap untuk Order (SP) dan SPJ.
     * Contoh: 0001/SP/SPKL/SN/05/2026 atau 0001.1/SPJ/SPKL/SN/05/2026
     */
    public function format(Lembur $lembur, string $type = 'spk'): string
    {
        $t = Carbon::parse($lembur->tanggal_lembur);

        // Format 4 digit (0001, 0002, dst)
        $noPad = str_pad($lembur->no_utama, 4, '0', STR_PAD_LEFT);

        // Tambahkan titik jika ada sisipan (contoh: 0001.1)
        $sisipan = $lembur->no_sisipan > 0 ? "." . $lembur->no_sisipan : "";

        // Pembeda kode antara Surat Perintah dan Surat Pertanggungjawaban
        $kode = $type === 'lpj' ? '/SPJ' : '/SP';

        // Akhiran dari file config
        $akhiran = config('app_settings.surat.akhiran');

        return $noPad . $sisipan . $kode . $akhiran . $t->format('m/Y');
    }
}

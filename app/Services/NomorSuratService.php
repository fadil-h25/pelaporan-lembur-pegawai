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
        $tanggalTerbesar = Lembur::whereNotNull('no_utama')->max('tanggal_lembur');

        // Jika tanggal "nyelip" (lebih kecil dari tanggal terbaru di DB yang punya nomor)
        if ($tanggalTerbesar && $tanggal < Carbon::parse($tanggalTerbesar)) {
            return $this->generateNomorSisipan($tanggal);
        }
        //
        // Jika tanggal baru atau lebih besar (logika normal/isi lubang)
        return $this->generateNomorUtamaBaru();
    }

    /**
     * Logika khusus untuk mencari nomor induk dan membuat sisipan (.1, .2, dst)
     */
    private function generateNomorSisipan(Carbon $tanggal): array
    {
        $induk = Lembur::whereNotNull('no_utama')
            ->whereDate('tanggal_lembur', '<=', $tanggal)
            ->orderBy('tanggal_lembur', 'desc')
            ->orderBy('no_utama', 'desc')
            ->first();

        $noUtama = $induk ? $induk->no_utama : config('app_settings.surat.nomor_awal', 1);
        $sisipanTerbesar = Lembur::whereNotNull('no_utama')
            ->where('no_utama', $noUtama)
            ->max('no_sisipan') ?? 0;

        return [
            'no_utama' => $noUtama,
            'no_sisipan' => $sisipanTerbesar + 1,
        ];
    }

    /**
     * Logika untuk mencari nomor utama yang tersedia.
     * Sudah termasuk fitur "Tambal Lubang" jika ada nomor yang kosong di tengah.
     */
    private function generateNomorUtamaBaru(): array
    {
        // Ambil semua no_utama yang sudah ada
        $existingNumbers = Lembur::where('no_sisipan', 0)
            ->pluck('no_utama')
            ->toArray();

        $nomorTujuan = config('app_settings.surat.nomor_awal', 1);

        // Cari angka terkecil yang belum terpakai (mengisi gap/lubang)
        while (in_array($nomorTujuan, $existingNumbers)) {
            $nomorTujuan++;
        }

        return [
            'no_utama' => $nomorTujuan,
            'no_sisipan' => 0,
        ];
    }
    /**
     * Format nomor surat lengkap untuk Order (SP) dan SPJ.
     * Contoh: 0001/SP/SPKL/SN/05/2026 atau 0001.1/SPJ/SPKL/SN/05/2026
     */
    public function format(Lembur $lembur, string $type = 'spk'): string
    {
        if (is_null($lembur->no_utama)) {
            return '';
        }

        $t = Carbon::parse($lembur->tanggal_lembur);

        // Format 4 digit (0001, 0002, dst)
        $noPad = str_pad($lembur->no_utama, 4, '0', STR_PAD_LEFT);

        // Tambahkan titik jika ada sisipan (contoh: 0001.1)
        $sisipan = $lembur->no_sisipan > 0 ? "." . $lembur->no_sisipan : "";

        // Akhiran dari file config berdasarkan tipe surat (spk / lpj)
        $akhiran = config("system.akhiran_surat_{$type}", '/SPKL/SN/');

        return $noPad . $sisipan . $akhiran . $t->format('m/Y');
    }
}

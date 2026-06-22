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
     * Membandingkan dua nomor surat hierarki secara leksikografis (seperti version comparison).
     * Contoh: compareHierarchy("1.1", "1.2") -> -1, compareHierarchy("1.1.1", "1.2") -> -1
     */
    private function compareHierarchy(string $a, string $b): int
    {
        $partsA = array_map('intval', explode('.', $a));
        $partsB = array_map('intval', explode('.', $b));
        $len = max(count($partsA), count($partsB));
        
        for ($i = 0; $i < $len; $i++) {
            $valA = $partsA[$i] ?? 0;
            $valB = $partsB[$i] ?? 0;
            if ($valA !== $valB) {
                return $valA <=> $valB;
            }
        }
        
        return 0;
    }

    /**
     * Mendapatkan sibling berikutnya (menaikkan angka terakhir dari representasi hierarki).
     * Contoh: getNextSibling("1") -> "1.1", getNextSibling("1.1") -> "1.2", getNextSibling("1.1.1") -> "1.1.2"
     */
    private function getNextSibling(string $num): string
    {
        $parts = explode('.', $num);
        if (count($parts) === 1) {
            return $parts[0] . '.1';
        }
        $lastIdx = count($parts) - 1;
        $parts[$lastIdx] = (int)$parts[$lastIdx] + 1;
        return implode('.', $parts);
    }

    /**
     * Memisahkan representasi hierarki string kembali menjadi no_utama dan no_sisipan.
     * Contoh: splitHierarchy("1.1.1") -> ['no_utama' => 1, 'no_sisipan' => '1.1']
     */
    private function splitHierarchy(string $num): array
    {
        $parts = explode('.', $num);
        $noUtama = (int)$parts[0];
        array_shift($parts);
        $noSisipan = empty($parts) ? '0' : implode('.', $parts);
        
        return [
            'no_utama' => $noUtama,
            'no_sisipan' => $noSisipan,
        ];
    }

    /**
     * Logika khusus untuk mencari nomor induk dan membuat sisipan (.1, .1.1, dst)
     */
    private function generateNomorSisipan(Carbon $tanggal): array
    {
        // 1. Ambil semua lembur yang sudah memiliki nomor, urutkan berdasarkan tanggal, no_utama, no_sisipan
        $existing = Lembur::whereNotNull('no_utama')
            ->get()
            ->sort(function ($a, $b) {
                // Bandingkan tanggal dulu
                $dateA = Carbon::parse($a->tanggal_lembur);
                $dateB = Carbon::parse($b->tanggal_lembur);
                if (!$dateA->eq($dateB)) {
                    return $dateA <=> $dateB;
                }
                
                // Jika tanggal sama, bandingkan hierarkinya
                $numA = $a->no_utama . ($a->no_sisipan !== '0' && $a->no_sisipan !== 0 ? '.' . $a->no_sisipan : '');
                $numB = $b->no_utama . ($b->no_sisipan !== '0' && $b->no_sisipan !== 0 ? '.' . $b->no_sisipan : '');
                return $this->compareHierarchy($numA, $numB);
            })
            ->values();

        // 2. Temukan record langsung sebelum (left) dan langsung setelah (right) tanggal target
        $left = null;
        $right = null;

        foreach ($existing as $item) {
            $itemDate = Carbon::parse($item->tanggal_lembur);
            if ($itemDate <= $tanggal) {
                $left = $item;
            } else {
                if (is_null($right)) {
                    $right = $item;
                }
            }
        }

        // Jika left tidak ditemukan (sebagai fallback)
        if (!$left) {
            $left = $existing->first();
        }

        $numLeft = $left->no_utama . ($left->no_sisipan !== '0' && $left->no_sisipan !== 0 ? '.' . $left->no_sisipan : '');
        $numRight = $right ? $right->no_utama . ($right->no_sisipan !== '0' && $right->no_sisipan !== 0 ? '.' . $right->no_sisipan : '') : null;

        // 3. Tentukan kandidat nomor untuk tanggal target
        $candidate = null;

        if ($numRight) {
            // Coba sibling dari left terlebih dahulu
            $sibling = $this->getNextSibling($numLeft);
            
            // Jika sibling < right leksikografis
            if ($this->compareHierarchy($sibling, $numRight) < 0) {
                // Pastikan tidak ada duplikat di DB (hanya untuk berjaga-jaga)
                $split = $this->splitHierarchy($sibling);
                $exists = Lembur::where('no_utama', $split['no_utama'])
                    ->where('no_sisipan', $split['no_sisipan'])
                    ->exists();
                    
                if (!$exists) {
                    $candidate = $sibling;
                }
            }
            
            // Jika sibling tidak valid / tidak < right, maka harus branch-off dari left (menambahkan .1 di belakang left)
            if (is_null($candidate)) {
                $suffixVal = 1;
                do {
                    $branch = $numLeft . '.' . $suffixVal;
                    $split = $this->splitHierarchy($branch);
                    $exists = Lembur::where('no_utama', $split['no_utama'])
                        ->where('no_sisipan', $split['no_sisipan'])
                        ->exists();
                        
                    if (!$exists) {
                        $candidate = $branch;
                        break;
                    }
                    $suffixVal++;
                } while (true);
            }
        } else {
            // Jika tidak ada right
            $sibling = $this->getNextSibling($numLeft);
            $candidate = $sibling;
        }

        return $this->splitHierarchy($candidate);
    }

    /**
     * Logika untuk mencari nomor utama yang tersedia.
     * Sudah termasuk fitur "Tambal Lubang" jika ada nomor yang kosong di tengah.
     */
    private function generateNomorUtamaBaru(): array
    {
        // Ambil semua no_utama yang sudah ada
        $existingNumbers = Lembur::whereIn('no_sisipan', [0, '0'])
            ->pluck('no_utama')
            ->toArray();

        $nomorTujuan = config('app_settings.surat.nomor_awal', 1);

        // Cari angka terkecil yang belum terpakai (mengisi gap/lubang)
        while (in_array($nomorTujuan, $existingNumbers)) {
            $nomorTujuan++;
        }

        return [
            'no_utama' => $nomorTujuan,
            'no_sisipan' => '0',
        ];
    }
    /**
     * Format nomor surat lengkap untuk Order (SP) dan SPJ.
     * Contoh: 0001/SL/SPKL/SN/05/2026 atau 0001.1/SL/SPKL/SN/05/2026
     */
    public function format(Lembur $lembur, string $type = 'spk'): string
    {
        if (is_null($lembur->no_utama)) {
            // If SPK (SPKL) document is downloaded before number assignment,
            // return a spaced placeholder followed by the akhiran and month/year.
            if ($type === 'spk') {
                $t = Carbon::parse($lembur->tanggal_lembur);
                $akhiran = config("system.akhiran_surat_{$type}", '/SL/SPKL/SN/');
                // Use non-breaking spaces so the padding is preserved in HTML output
                $nbsp = "\xC2\xA0"; // UTF-8 NBSP
                $placeholder = str_repeat($nbsp, 15);

                return $placeholder . $akhiran . $t->format('m/Y');
            }

            return '';
        }

        $t = Carbon::parse($lembur->tanggal_lembur);

        // Format 4 digit (0001, 0002, dst)
        $noPad = str_pad($lembur->no_utama, 4, '0', STR_PAD_LEFT);

        // Tambahkan titik jika ada sisipan (contoh: 0001.1)
        $sisipan = ($lembur->no_sisipan !== '0' && $lembur->no_sisipan !== 0 && !empty($lembur->no_sisipan)) ? "." . $lembur->no_sisipan : "";

        // Akhiran dari file config berdasarkan tipe surat (spk / lpj)
        $akhiran = config("system.akhiran_surat_{$type}", '/SL/SPKL/SN/');

        return $noPad . $sisipan . $akhiran . $t->format('m/Y');
    }
}

<?php

namespace App\Services;

use App\Models\Lembur;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use PhpOffice\PhpWord\TemplateProcessor;
use Carbon\Carbon;

class LemburService
{
    /**
     * Filter dan Paginate Data Lembur
     */
    public function filter(string $search = '', int $perPage = 5, string $startDate = '', string $endDate = '', string $sort = 'terbaru'): LengthAwarePaginator
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $roleValue = $user->role instanceof \BackedEnum ? $user->role->value : $user->role;

        return Lembur::query()
            ->when(!in_array($roleValue, [\App\UserRole::ADMIN->value, \App\UserRole::OPERATOR->value]), function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal_lembur', [$startDate, $endDate]);
            })
            ->when($startDate && !$endDate, function ($query) use ($startDate) {
                $query->whereDate('tanggal_lembur', '>=', $startDate);
            })
            ->when(!$startDate && $endDate, function ($query) use ($endDate) {
                $query->whereDate('tanggal_lembur', '<=', $endDate);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', '%' . $search . '%')
                        ->orWhere('nip', 'like', '%' . $search . '%')
                        ->orWhere('rencana_kerja', 'like', '%' . $search . '%');
                });
            })
            ->when($sort === 'terbaru', fn($q) => $q->orderBy('tanggal_lembur', 'desc'))
            ->when($sort === 'terlama', fn($q) => $q->orderBy('tanggal_lembur', 'asc'))
            ->when($sort === 'nomor_asc', fn($q) => $q->orderBy('id', 'asc'))
            ->when($sort === 'nomor_desc', fn($q) => $q->orderBy('id', 'desc'))
            ->paginate($perPage);
    }

    public function totalJamTahunIni(): int
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $roleValue = $user->role instanceof \BackedEnum ? $user->role->value : $user->role;

        return (int) Lembur::query()
            ->when(!in_array($roleValue, [\App\UserRole::ADMIN->value, \App\UserRole::OPERATOR->value]), function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereYear('tanggal_lembur', Carbon::now()->year)
            ->sum('jumlah_jam');
    }

    public function totalJamBulanIni(): int
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $roleValue = $user->role instanceof \BackedEnum ? $user->role->value : $user->role;

        return (int) Lembur::query()
            ->when(!in_array($roleValue, [\App\UserRole::ADMIN->value, \App\UserRole::OPERATOR->value]), function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereYear('tanggal_lembur', Carbon::now()->year)
            ->whereMonth('tanggal_lembur', Carbon::now()->month)
            ->sum('jumlah_jam');
    }

    /**
     * Simpan Data Lembur Baru (Create) - Auto generate nomor surat
     */
    public function create(array $data): Lembur
    {
        // Generate nomor surat berdasarkan logika sisipan
        $nomor = $this->generateNomorSurat($data['tanggal_lembur']);
        $data['no_utama'] = $nomor['no_utama'];
        $data['no_sisipan'] = $nomor['no_sisipan'];

        return Lembur::create($data);
    }

    /**
     * Update Data Lembur (Edit)
     */
    public function update(Lembur $lembur, array $data): bool
    {
        return $lembur->update($data);
    }

    /**
     * Hapus Data Lembur (Delete)
     */
    public function delete(Lembur $lembur): ?bool
    {
        return $lembur->delete();
    }

    /**
     * Hitung nomor surat berdasarkan urutan data di database
     */
    public function getNomorDatabase(Lembur $lembur): int
    {
        return Lembur::orderBy('created_at', 'asc')->pluck('id')->search($lembur->id) + 1;
    }

    /**
     * Generate nomor surat (no_utama dan no_sisipan) berdasarkan logika sisipan
     * - Normal input (tidak ada surat lebih baru): no_utama++, no_sisipan = 0
     * - Sisipan input (ada surat lebih baru): no_utama = no induk, no_sisipan++
     */
    public function generateNomorSurat(string $tanggalLembur): array
    {
        $tanggal = Carbon::parse($tanggalLembur)->startOfDay();

        // Cek apakah ada data lembur di tanggal SETELAHNYA
        $adaDataLebihBaru = Lembur::where('tanggal_lembur', '>', $tanggal)->exists();

        if (!$adaDataLebihBaru) {
            // INPUT NORMAL - ambil nomor utama terbesar + 1
            $noUtamaTerbesar = Lembur::max('no_utama') ?? (config('system.nomor_surat_awal') - 1);
            $noUtamaBaru = max($noUtamaTerbesar + 1, config('system.nomor_surat_awal'));
            $noSisipanBaru = 0;
        } else {
            // INPUT SISIPAN - cari nomor induk dari surat terakhir sebelum tanggal ini
            $nomorInduk = Lembur::where('tanggal_lembur', '<', $tanggal)
                ->orderBy('tanggal_lembur', 'desc')
                ->orderBy('no_utama', 'desc')
                ->value('no_utama');

            if ($nomorInduk === null) {
                // Tidak ada surat sebelumnya, mulai dari nomor awal config
                $noUtamaBaru = config('system.nomor_surat_awal');
                $noSisipanBaru = 0;
            } else {
                // Cari sisipan terakhir dari nomor induk tersebut
                $sisipanTerbesar = Lembur::where('no_utama', $nomorInduk)
                    ->max('no_sisipan') ?? 0;

                $noUtamaBaru = $nomorInduk;
                $noSisipanBaru = $sisipanTerbesar + 1;
            }
        }

        return [
            'no_utama' => $noUtamaBaru,
            'no_sisipan' => $noSisipanBaru,
        ];
    }

    /**
     * Format nomor surat lengkap dengan pattern:
     * - XXXX/AKHIRAN_SURAT/MM/YYYY (untuk nomor utama tanpa sisipan)
     * - XXXX.X/AKHIRAN_SURAT/MM/YYYY (untuk nomor dengan sisipan > 0)
     *
     * contoh:
     * - 0001/SPKL/SN/05/2026 (untuk no_sisipan = 0)
     * - 0001.1/LPJ/SN/05/2026 (untuk no_sisipan = 1)
     */
    public function formatNomorSurat(Lembur $lembur, string $type = 'spk'): string
    {
        $t = Carbon::parse($lembur->tanggal_lembur);
        $noUtama = str_pad($lembur->no_utama, 4, '0', STR_PAD_LEFT);
        $akhiran = $type === 'lpj' ? config('system.akhiran_surat_lpj') : config('system.akhiran_surat_spk');

        // Jika no_sisipan adalah 0, tidak tampilkan .0
        $nomor = $lembur->no_sisipan == 0 ? $noUtama : "{$noUtama}.{$lembur->no_sisipan}";

        return $nomor . $akhiran . $t->format('m/Y');
    }

    /**
     * Fitur Terbilang
     */
    public function terbilang($n): string
    {
        $b = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];
        if ($n < 12) {
            return ' ' . $b[$n];
        } elseif ($n < 20) {
            return $this->terbilang($n - 10) . ' belas';
        } elseif ($n < 100) {
            return $this->terbilang($n / 10) . ' puluh' . $this->terbilang($n % 10);
        }

        return '';
    }

    /**
     * Proses Download Dokumen Cetak (.docx)
     */
    public function downloadCetak($type, Lembur $lembur)
    {
        $tp = new TemplateProcessor(public_path("templates/template_$type.docx"));
        Carbon::setLocale('id');
        $t = Carbon::parse($lembur->tanggal_lembur);

        $nomorSurat = $this->formatNomorSurat($lembur, $type);
        $tp->setValue('no_surat', $nomorSurat);
        $tp->setValue('nama', $lembur->nama);
        $tp->setValue('nip', $lembur->nip);
        $tp->setValue('jabatan', $lembur->jabatan);
        $tp->setValue('golongan', $lembur->golongan);
        $tp->setValue('hari_tanggal', $t->translatedFormat('l / d F Y'));
        $tp->setValue('tanggal', $t->translatedFormat('d F Y'));
        $tp->setValue('jam', $lembur->jumlah_jam);
        $tp->setValue('terbilang', trim($this->terbilang($lembur->jumlah_jam)));
        $tp->setValue('pekerjaan', $lembur->rencana_kerja);
        $tp->setValue('hasil', $lembur->hasil_kerja);
        $tp->setValue('anggaran', $lembur->pembebanan_anggaran);
        $tp->setValue('nama_kasek', config('system.nama_kasek'));
        $tp->setValue('nip_kasek', config('system.nip_kasek'));

        if ($lembur->dokumentasi && file_exists(storage_path('app/public/' . $lembur->dokumentasi))) {
            $tp->setImageValue('gambar', [
                'path' => storage_path('app/public/' . $lembur->dokumentasi),
                'width' => 400,
                'height' => 300,
                'ratio' => true
            ]);
        } else {
            $tp->setValue('gambar', 'Tidak ada dokumentasi');
        }

        $path = storage_path('app/public/' . $type . '_' . $lembur->id . '_' . time() . '.docx');
        $tp->saveAs($path);

        return response()->download($path)->deleteFileAfterSend(true);
    }

    /**
     * Headers untuk konfigurasi Table di UI
     */
    public function tableHeaders(): array
    {
        return [
            ['key' => 'nomor', 'label' => 'No. Surat', 'sortable' => false],
            ['key' => 'nama', 'label' => 'Nama'],
            ['key' => 'tanggal_lembur', 'label' => 'Tanggal Lembur'],
            ['key' => 'hasil_kerja', 'label' => 'Hasil Kerja'],
            ['key' => 'jumlah_jam', 'label' => 'Jam'],
        ];
    }
}

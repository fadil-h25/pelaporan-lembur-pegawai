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

        return Lembur::query()
            ->when(!in_array($user->role->value, [\App\UserRole::ADMIN->value, \App\UserRole::OPERATOR->value]), function ($query) use ($user) {
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
                $query->where(function($q) use ($search) {
                    $q->where('nama', 'like', '%' . $search . '%')
                      ->orWhere('nip', 'like', '%' . $search . '%')
                      ->orWhere('rencana_kerja', 'like', '%' . $search . '%');
                });
            })
            ->when($sort === 'terbaru', fn ($q) => $q->orderBy('tanggal_lembur', 'desc'))
            ->when($sort === 'terlama', fn ($q) => $q->orderBy('tanggal_lembur', 'asc'))
            ->when($sort === 'nomor_asc', fn ($q) => $q->orderBy('id', 'asc'))
            ->when($sort === 'nomor_desc', fn ($q) => $q->orderBy('id', 'desc'))
            ->paginate($perPage);
    }

    public function totalJamTahunIni(): int
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        return (int) Lembur::query()
            ->when(!in_array($user->role->value, [\App\UserRole::ADMIN->value, \App\UserRole::OPERATOR->value]), function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereYear('tanggal_lembur', Carbon::now()->year)
            ->sum('jumlah_jam');
    }

    public function totalJamBulanIni(): int
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        return (int) Lembur::query()
            ->when(!in_array($user->role->value, [\App\UserRole::ADMIN->value, \App\UserRole::OPERATOR->value]), function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereYear('tanggal_lembur', Carbon::now()->year)
            ->whereMonth('tanggal_lembur', Carbon::now()->month)
            ->sum('jumlah_jam');
    }

    /**
     * Simpan Data Lembur Baru (Create)
     */
    public function create(array $data): Lembur
    {
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
    public function downloadCetak($type, Lembur $lembur, $nomor)
    {
        $tp = new TemplateProcessor(public_path("templates/template_$type.docx"));
        Carbon::setLocale('id');
        $t = Carbon::parse($lembur->tanggal_lembur);
        
        $tp->setValue('no_surat', str_pad($nomor, 4, '0', STR_PAD_LEFT) . '/SPKL/SN/' . $t->format('m/Y'));
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
        $tp->setValue('nama_kasek', 'AWALUDDIN MUSTAFA, S.E., M.Si');
        $tp->setValue('nip_kasek', '19740712 200212 1 006');
        
        if ($lembur->dokumentasi && \Illuminate\Support\Facades\Storage::disk('local')->exists('dokumentasi/' . $lembur->dokumentasi)) {
            $pathImage = \Illuminate\Support\Facades\Storage::disk('local')->path('dokumentasi/' . $lembur->dokumentasi);
            $tp->setImageValue('gambar', [
                'path' => $pathImage,
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
            ['key' => 'hasil_kerja', 'label' => 'Hasil Kerja'],
            ['key' => 'jumlah_jam', 'label' => 'Jam'],
        ];
    }
}

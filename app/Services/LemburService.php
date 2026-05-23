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
    private NomorSuratService $nomorSuratService;

    public function __construct(NomorSuratService $nomorSuratService)
    {
        $this->nomorSuratService = $nomorSuratService;
    }
    private function getBaseQuery(string $search = '', string $startDate = '', string $endDate = '', string $sort = 'terbaru', string $hasNomor = '')
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
            ->when($hasNomor === 'yes', fn($q) => $q->whereNotNull('no_utama'))
            ->when($hasNomor === 'no', fn($q) => $q->whereNull('no_utama'));
    }

    public function filter(string $search = '', int $perPage = 5, string $startDate = '', string $endDate = '', string $sort = 'terbaru', string $hasNomor = ''): LengthAwarePaginator
    {
        return $this->getBaseQuery($search, $startDate, $endDate, $sort, $hasNomor)->paginate($perPage);
    }

    public function exportExcel(string $search = '', string $startDate = '', string $endDate = '', string $sort = 'terbaru', string $hasNomor = '')
    {
        $query = $this->getBaseQuery($search, $startDate, $endDate, $sort, $hasNomor);

        return (new \Rap2hpoutre\FastExcel\FastExcel($query->get()))->download('data_lembur.xlsx', function ($lembur) {
            return [
                'Nomor Surat (SPK)' => $this->nomorSuratService->format($lembur, 'spk'),
                'Nomor Surat (LPJ)' => $this->nomorSuratService->format($lembur, 'lpj'),
                'Nama' => $lembur->nama,
                'NIP' => $lembur->nip,
                'Jabatan' => $lembur->jabatan,
                'Golongan' => $lembur->golongan,
                'Tanggal Lembur' => Carbon::parse($lembur->tanggal_lembur)->translatedFormat('d F Y'),
                'Jumlah Jam' => $lembur->jumlah_jam,
                'Pembebanan Anggaran' => $lembur->pembebanan_anggaran,
                'Rencana Kerja' => $lembur->rencana_kerja,
                'Hasil Kerja' => $lembur->hasil_kerja,
            ];
        });
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

    public function totalTanpaNomor(): int
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $roleValue = $user->role instanceof \BackedEnum ? $user->role->value : $user->role;

        return (int) Lembur::query()
            ->when(!in_array($roleValue, [\App\UserRole::ADMIN->value, \App\UserRole::OPERATOR->value]), function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereNull('no_utama')
            ->count();
    }

    public function totalDenganNomor(): int
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $roleValue = $user->role instanceof \BackedEnum ? $user->role->value : $user->role;

        return (int) Lembur::query()
            ->when(!in_array($roleValue, [\App\UserRole::ADMIN->value, \App\UserRole::OPERATOR->value]), function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereNotNull('no_utama')
            ->count();
    }

    /**
     * Simpan Data Lembur Baru (Create) - Auto generate nomor surat
     */
    public function create(array $data): Lembur
    {
        $data['no_utama'] = null;
        $data['no_sisipan'] = 0;

        return Lembur::create($data);
    }

    public function generateNomor(Lembur $lembur): void
    {
        if (is_null($lembur->no_utama)) {
            $nomor = $this->nomorSuratService->generate($lembur->tanggal_lembur);
            $lembur->update([
                'no_utama' => $nomor['no_utama'],
                'no_sisipan' => $nomor['no_sisipan']
            ]);
        }
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


    /**
     * Format nomor surat lengkap dengan pattern:
     * - XXXX/AKHIRAN_SURAT/MM/YYYY (untuk nomor utama tanpa sisipan)
     * - XXXX.X/AKHIRAN_SURAT/MM/YYYY (untuk nomor dengan sisipan > 0)
     *
     * contoh:
     * - 0001/SPKL/SN/05/2026 (untuk no_sisipan = 0)
     * - 0001.1/LPJ/SN/05/2026 (untuk no_sisipan = 1)
     */


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

        $nomorSurat = $this->nomorSuratService->format($lembur, $type);
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

        if ($lembur->dokumentasi && file_exists(storage_path('app/private/dokumentasi/' . $lembur->dokumentasi))) {
            $tp->setImageValue('gambar', [
                'path' => storage_path('app/private/dokumentasi/' . $lembur->dokumentasi),
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
            ['key' => 'bagian', 'label' => 'Bagian'],
        ];
    }

    public function formatNomorSurat($lembur, $type = 'spk'): string
    {
        return $this->nomorSuratService->format($lembur, $type);
    }
}

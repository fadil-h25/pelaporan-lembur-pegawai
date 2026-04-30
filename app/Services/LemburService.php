<?php

namespace App\Services;

use App\Models\Lembur;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LemburService
{
    /**
     * Filter dan Paginate Data Lembur
     */
    public function filter(string $search = '', int $perPage = 5): LengthAwarePaginator
    {
        return Lembur::query()
            ->when($search, function ($query) use ($search) {
                $query->where('nama', 'like', '%' . $search . '%')
                    ->orWhere('nip', 'like', '%' . $search . '%')
                    ->orWhere('rencana_kerja', 'like', '%' . $search . '%');
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Total Keseluruhan Data Lembur
     */
    public function total(): int
    {
        return Lembur::count();
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
     * Cetak Data Lembur (Print)
     * Mengembalikan URL cetak (bisa disesuaikan dengan routing)
     */
    public function getPrintUrl(Lembur $lembur): string
    {
        // Sementara mengembalikan URL placeholder, sesuaikan dengan rute Anda
        return "/lembur/{$lembur->id}/print";
    }

    /**
     * Headers untuk konfigurasi Table di UI
     */
    public function tableHeaders(): array
    {
        return [
            ['key' => 'id', 'label' => '#'],
            ['key' => 'nama', 'label' => 'Nama'],
            ['key' => 'nip', 'label' => 'NIP'],
            ['key' => 'tanggal_lembur', 'label' => 'Tgl Lembur'],
            ['key' => 'jumlah_jam', 'label' => 'Jam'],
            ['key' => 'rencana_kerja', 'label' => 'Rencana Kerja'],
            ['key' => 'status', 'label' => 'Status'],
        ];
    }
}

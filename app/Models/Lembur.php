<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lembur extends Model
{
    use HasFactory;

    // Daftarkan semua kolom yang boleh diisi melalui form/inputan
    protected $fillable = [
        'user_id',
        'nama',
        'nip',
        'golongan',
        'jabatan',
        'tanggal_lembur',
        'jumlah_jam',
        'pembebanan_anggaran',
        'rencana_kerja',
        'hasil_kerja',
        'status',
        'nama_kasek',
        'nip_kasek',
        'dokumentasi',
    ];

    // Relasi balik ke User (Opsional, tapi sangat berguna nanti)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App;

enum Bagian: string
{
    case BAGIAN1 = 'Administrasi';
    case BAGIAN2 = 'Penanganan Pelanggaran & Penyelesaian Sengketa PP';
    case BAGIAN3 = 'Pengawasan Pemilu';
    case SDMKUANGAN = 'Hukum, Hubungan Masyarakat, Data dan Informasi';

    public function label(): string
    {
        return match($this) {
            self::BAGIAN1 => 'Administrasi',
            self::BAGIAN2 => 'Penanganan Pelanggaran & Penyelesaian Sengketa PP',
            self::BAGIAN3 => 'Pengawasan Pemilu',
            self::SDMKUANGAN => 'Hukum, Hubungan Masyarakat, Data dan Informasi',
        };
    }
}

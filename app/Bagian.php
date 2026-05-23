<?php

namespace App;

enum Bagian: string
{
    case BAGIAN1 = 'bagian1';
    case BAGIAN2 = 'bagian2';
    case BAGIAN3 = 'bagian3';
    case SDMKUANGAN= 'SDM dan Keuangan';
  

    public function label(): string
    {
        return match($this) {
            self::BAGIAN1 => 'Bagian 1',
            self::BAGIAN2 => 'Bagian 2',
            self::BAGIAN3 => 'Bagian 3',
            self::SDMKUANGAN => 'SDM dan Keuangan',
        };
    }
}

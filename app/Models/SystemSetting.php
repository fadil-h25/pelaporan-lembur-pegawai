<?php

/**
 * DEPRECATED: Model ini tidak digunakan lagi.
 * System settings sekarang menggunakan config file (config/system.php)
 * Model ini tetap ada untuk backward compatibility tapi tidak direkomendasikan untuk digunakan.
 */

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    // use HasFactory;

    protected $fillable = [
        'nama_kasek',
        'nip_kasek',
        'akhiran_surat',
    ];

    /**
     * Get the first (and only) system setting record
     */
    public static function getSettings(): self
    {
        return self::first() ?? self::create([
            
            'nama_kasek' => 'AWALUDDIN MUSTAFA, S.E., M.Si',
            'nip_kasek' => '19740712 200212 1 006',
            'akhiran_surat' => '/SL/SPKL/SN/',
        ]);
    }
}

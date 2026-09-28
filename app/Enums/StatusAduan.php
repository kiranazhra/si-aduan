<?php

namespace App\Enums;

/**
 * Status aduan (hanya 3):
 *  - Diproses        : aduan sudah masuk dan sedang ditangani admin (status awal)
 *  - Dikoordinasikan : aduan sudah diteruskan ke unit terkait
 *  - Selesai         : aduan sudah dijawab dan ditutup
 */
enum StatusAduan: string
{
    case Diproses        = 'diproses';
    case Dikoordinasikan = 'dikoordinasikan';
    case Selesai         = 'selesai';

    public function label(): string
    {
        return match ($this) {
            self::Diproses        => 'Diproses',
            self::Dikoordinasikan => 'Dikoordinasikan',
            self::Selesai         => 'Selesai',
        };
    }

    public function warna(): string
    {
        return match ($this) {
            self::Diproses        => 'warning',
            self::Dikoordinasikan => 'info',
            self::Selesai         => 'success',
        };
    }

    /** Tombol "Teruskan ke Unit" hanya muncul saat aduan masih Diproses. */
    public function bolehDiteruskan(): bool
    {
        return $this === self::Diproses;
    }
}

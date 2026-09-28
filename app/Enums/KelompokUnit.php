<?php

namespace App\Enums;

/** Pengelompokan unit/poli (mengikuti UNIT_GROUPS di prototype). */
enum KelompokUnit: string
{
    case RawatInap = 'rawat_inap';
    case Klinis    = 'klinis';
    case Poli      = 'poli';
    case Penunjang = 'penunjang';
    case Manajemen = 'manajemen';
    case Lainnya   = 'lainnya';

    public function label(): string
    {
        return match ($this) {
            self::RawatInap => 'Rawat Inap',
            self::Klinis    => 'Instalasi & Unit Klinis',
            self::Poli      => 'Poli / Klinik',
            self::Penunjang => 'Penunjang & Administrasi',
            self::Manajemen => 'Manajemen & Bidang',
            self::Lainnya   => 'Lainnya / Eksternal',
        };
    }
}

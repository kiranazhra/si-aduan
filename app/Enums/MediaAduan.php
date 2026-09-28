<?php

namespace App\Enums;

/** Media / sumber masuknya aduan (untuk Rekap Media). */
enum MediaAduan: string
{
    case Portal     = 'portal';
    case WhatsApp   = 'whatsapp';
    case Email      = 'email';
    case KotakSaran = 'kotak_saran';

    public function label(): string
    {
        return match ($this) {
            self::Portal     => 'Portal',
            self::WhatsApp   => 'WhatsApp',
            self::Email      => 'Email',
            self::KotakSaran => 'Kotak Saran',
        };
    }
}

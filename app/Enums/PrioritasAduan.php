<?php

namespace App\Enums;

enum PrioritasAduan: string
{
    case Tinggi = 'tinggi';
    case Sedang = 'sedang';
    case Rendah = 'rendah';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function warna(): string
    {
        return match ($this) {
            self::Tinggi => 'danger',
            self::Sedang => 'warning',
            self::Rendah => 'gray',
        };
    }
}

<?php

namespace App\Enums;

/** Peran (hak akses) pengguna. Disimpan di kolom users.peran */
enum PeranPengguna: string
{
    case SuperAdmin  = 'super_admin';
    case Operator    = 'operator';
    case PetugasUnit = 'petugas_unit';
    case Viewer      = 'viewer';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin  => 'Super Admin',
            self::Operator    => 'Operator',
            self::PetugasUnit => 'Petugas Unit',
            self::Viewer      => 'Viewer',
        };
    }

    public function deskripsi(): string
    {
        return match ($this) {
            self::SuperAdmin  => 'Semua fitur',
            self::Operator    => 'Lihat & ubah status semua aduan',
            self::PetugasUnit => 'Lihat & tanggapi aduan unit sendiri',
            self::Viewer      => 'Lihat saja (tanpa ubah)',
        };
    }

    public function warna(): string
    {
        return match ($this) {
            self::SuperAdmin  => 'purple',
            self::Operator    => 'blue',
            self::PetugasUnit => 'emerald',
            self::Viewer      => 'slate',
        };
    }

    /** Peran yang wajib terikat ke satu unit. */
    public function perluUnit(): bool
    {
        return $this === self::PetugasUnit;
    }
}

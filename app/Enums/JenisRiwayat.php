<?php

namespace App\Enums;

/** Jenis kejadian di riwayat aduan. */
enum JenisRiwayat: string
{
    case Diterima      = 'diterima';       // aduan diterima sistem
    case Diteruskan    = 'diteruskan';     // diteruskan ke unit
    case StatusDiubah  = 'status_diubah';  // status diubah
    case Tanggapan     = 'tanggapan';      // tanggapan petugas unit
    case SolusiDikirim = 'solusi_dikirim'; // solusi dikirim ke pelapor
    case Selesai       = 'selesai';        // aduan selesai

    /** Nama ikon Material Icons (sama dengan yang dipakai di prototype). */
    public function ikon(): string
    {
        return match ($this) {
            self::Diterima      => 'inbox',
            self::Diteruskan    => 'send',
            self::StatusDiubah  => 'sync',
            self::Tanggapan     => 'manage_accounts',
            self::SolusiDikirim => 'mark_email_read',
            self::Selesai       => 'check_circle',
        };
    }
}

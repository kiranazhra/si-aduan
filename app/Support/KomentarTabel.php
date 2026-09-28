<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Memberi keterangan (komentar) pada TABEL.
 * Keterangan pada KOLOM memakai ->comment('...') di masing-masing migration.
 * Keduanya tampil di phpMyAdmin (tab Struktur -> kolom "Komentar").
 */
trait KomentarTabel
{
    protected function komentarTabel(string $tabel, string $komentar): void
    {
        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::statement('ALTER TABLE `' . $tabel . '` COMMENT = ' . DB::getPdo()->quote($komentar));
        }
    }
}

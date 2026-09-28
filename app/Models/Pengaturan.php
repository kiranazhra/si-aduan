<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $table = 'pengaturan';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diubah_pada';

    protected $fillable = ['nama', 'isi'];

    public static function ambil(string $nama, mixed $bawaan = null): mixed
    {
        return static::where('nama', $nama)->value('isi') ?? $bawaan;
    }

    /** Untuk pengaturan aktif/nonaktif (1/0). */
    public static function aktif(string $nama, bool $bawaan = false): bool
    {
        return filter_var(static::ambil($nama, $bawaan), FILTER_VALIDATE_BOOLEAN);
    }

    public static function simpan(string $nama, mixed $isi): void
    {
        static::updateOrCreate(
            ['nama' => $nama],
            ['isi' => is_bool($isi) ? ($isi ? '1' : '0') : (string) $isi]
        );
    }
}

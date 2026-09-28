<?php

namespace App\Models;

use App\Support\KodeSingkat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lokasi extends Model
{
    protected $table = 'lokasi';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diubah_pada';

    protected $fillable = ['nama', 'kode', 'urutan', 'aktif'];

    protected function casts(): array
    {
        return ['aktif' => 'boolean'];
    }

    public function aduan(): HasMany
    {
        return $this->hasMany(Aduan::class);
    }

    /** Kode singkat untuk nomor tiket (pakai kolom kode; bila kosong dibuat dari nama). */
    public function kodeTiket(): string
    {
        return $this->kode ?: KodeSingkat::lokasi((string) $this->nama);
    }
}

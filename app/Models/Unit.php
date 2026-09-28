<?php

namespace App\Models;

use App\Enums\KelompokUnit;
use App\Support\KodeSingkat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    protected $table = 'unit';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diubah_pada';

    protected $fillable = ['kode', 'nama', 'kelompok', 'penanggung_jawab', 'no_wa', 'aktif'];

    protected function casts(): array
    {
        return [
            'kelompok' => KelompokUnit::class,
            'aktif'    => 'boolean',
        ];
    }

    public function petugas(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /** Aduan yang DITANGANI unit ini (aduan.unit_id -> diteruskan admin ke sini). */
    public function aduan(): HasMany
    {
        return $this->hasMany(Aduan::class);
    }

    /** Aduan yang LOKASI KEJADIANNYA di unit ini (aduan.lokasi_id -> pilihan pelapor di form). */
    public function aduanLokasi(): HasMany
    {
        return $this->hasMany(Aduan::class, 'lokasi_id');
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('aktif', true);
    }

    /** Kode singkat (3 huruf) untuk nomor tiket, dibuat otomatis dari nama unit. */
    public function kodeTiket(): string
    {
        return KodeSingkat::unit((string) $this->nama);
    }

    /** Tautan wa.me ke penanggung jawab unit (null bila nomor belum diisi). */
    public function urlWhatsapp(?string $teks = null): ?string
    {
        if (! $this->no_wa) {
            return null;
        }

        return 'https://wa.me/' . $this->no_wa . ($teks ? '?text=' . rawurlencode($teks) : '');
    }
}

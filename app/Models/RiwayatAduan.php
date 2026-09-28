<?php

namespace App\Models;

use App\Enums\JenisRiwayat;
use App\Enums\StatusAduan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatAduan extends Model
{
    protected $table = 'riwayat_aduan';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diubah_pada';

    protected $fillable = ['aduan_id', 'petugas_id', 'unit_id', 'jenis', 'judul', 'catatan', 'status'];

    protected function casts(): array
    {
        return [
            'jenis'  => JenisRiwayat::class,
            'status' => StatusAduan::class,
        ];
    }

    public function aduan(): BelongsTo   { return $this->belongsTo(Aduan::class); }
    public function petugas(): BelongsTo { return $this->belongsTo(User::class, 'petugas_id'); }
    public function unit(): BelongsTo    { return $this->belongsTo(Unit::class); }
}

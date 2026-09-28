<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PesanWhatsapp extends Model
{
    protected $table = 'pesan_whatsapp';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diubah_pada';

    protected $fillable = [
        'aduan_id', 'dikirim_oleh', 'jenis_penerima', 'no_wa_penerima',
        'isi_pesan', 'status', 'keterangan_gagal', 'terkirim_pada',
    ];

    protected function casts(): array
    {
        return ['terkirim_pada' => 'datetime'];
    }

    public function aduan(): BelongsTo   { return $this->belongsTo(Aduan::class); }
    public function pengirim(): BelongsTo { return $this->belongsTo(User::class, 'dikirim_oleh'); }

    /** Tautan wa.me lengkap dengan isi pesan. */
    public function url(): string
    {
        return 'https://wa.me/' . $this->no_wa_penerima . '?text=' . rawurlencode($this->isi_pesan);
    }
}

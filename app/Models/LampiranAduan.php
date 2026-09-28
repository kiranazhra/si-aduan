<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class LampiranAduan extends Model
{
    protected $table = 'lampiran_aduan';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diubah_pada';

    protected $fillable = ['aduan_id', 'alamat_file', 'nama_file', 'jenis_file', 'ukuran', 'path'];

    /**
     * Kompatibilitas dengan form pengaduan publik yang mengirim ['path' => ...].
     * Nilai 'path' otomatis diubah menjadi kolom alamat_file, nama_file, jenis_file, dan ukuran.
     */
    public function setPathAttribute(string $path): void
    {
        $disk  = Storage::disk('public');
        $ada   = $disk->exists($path);

        $this->attributes['alamat_file'] = $path;
        $this->attributes['nama_file']   = basename($path);
        $this->attributes['jenis_file']  = $ada ? ($disk->mimeType($path) ?: 'application/octet-stream') : 'application/octet-stream';
        $this->attributes['ukuran']      = $ada ? (int) $disk->size($path) : 0;
    }

    public function aduan(): BelongsTo
    {
        return $this->belongsTo(Aduan::class);
    }

    /** Alamat file untuk ditampilkan (butuh: php artisan storage:link). */
    public function url(): string
    {
        return asset('storage/' . ltrim($this->alamat_file, '/'));
    }

    public function adalahGambar(): bool
    {
        return str_starts_with((string) $this->jenis_file, 'image/');
    }
}

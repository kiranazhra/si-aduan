<?php

namespace App\Models;

use App\Enums\PeranPengguna;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Ganti / gabungkan dengan app/Models/User.php bawaan Laravel.
 * Kolom name, email, password tetap bawaan Laravel (dipakai untuk login).
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
        'peran', 'unit_id', 'no_wa', 'aktif',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'peran'             => PeranPengguna::class,
            'aktif'             => 'boolean',
        ];
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function adalahSuperAdmin(): bool
    {
        return $this->peran === PeranPengguna::SuperAdmin;
    }

    /** Admin/Operator: boleh meneruskan aduan ke unit dan mengirim solusi ke pelapor. */
    public function bolehKoordinasi(): bool
    {
        return in_array($this->peran, [PeranPengguna::SuperAdmin, PeranPengguna::Operator], true);
    }

    /** Petugas unit: boleh menanggapi aduan unitnya sendiri. */
    public function bolehMenanggapi(): bool
    {
        return $this->peran === PeranPengguna::PetugasUnit;
    }

    /** Viewer: hanya melihat (tombol ekspor tetap boleh, ubah data tidak). */
    public function hanyaLihat(): bool
    {
        return $this->peran === PeranPengguna::Viewer;
    }
}

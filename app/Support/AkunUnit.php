<?php

namespace App\Support;

use App\Enums\PeranPengguna;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * Membuat akun Petugas Unit secara otomatis untuk setiap unit/poli,
 * sehingga Super Admin tidak perlu membuatnya satu per satu.
 */
class AkunUnit
{
    /** Domain email akun otomatis. */
    public const DOMAIN_EMAIL = 'rsud-damanhuri.id';

    /** Kata sandi awal semua akun otomatis (sebaiknya diganti petugas setelah login pertama). */
    public const SANDI_AWAL = 'unit123';

    /** Buat akun untuk satu unit bila unit itu belum punya Petugas Unit. Mengembalikan akun baru, atau null bila dilewati. */
    public static function buatUntuk(Unit $unit): ?User
    {
        $sudahAda = User::where('unit_id', $unit->id)
            ->where('peran', PeranPengguna::PetugasUnit->value)
            ->exists();

        if ($sudahAda) {
            return null;
        }

        $dasar = Str::slug($unit->nama) ?: strtolower($unit->kode);
        $email = $dasar . '@' . self::DOMAIN_EMAIL;

        // Bentrok email (nama unit mirip) -> tambahkan kode unit supaya unik.
        if (User::where('email', $email)->exists()) {
            $email = $dasar . '-' . strtolower($unit->kode) . '@' . self::DOMAIN_EMAIL;
        }

        return User::create([
            'name'     => 'Petugas ' . $unit->nama,
            'email'    => $email,
            'password' => self::SANDI_AWAL, // di-hash otomatis oleh cast 'hashed'
            'peran'    => PeranPengguna::PetugasUnit,
            'unit_id'  => $unit->id,
            'no_wa'    => $unit->no_wa,
            'aktif'    => (bool) $unit->aktif,
        ]);
    }

    /** Buat akun untuk semua unit yang belum punya. Mengembalikan jumlah akun baru. */
    public static function buatSemua(): int
    {
        $dibuat = 0;

        foreach (Unit::orderBy('nama')->get() as $unit) {
            if (self::buatUntuk($unit)) {
                $dibuat++;
            }
        }

        return $dibuat;
    }
}
